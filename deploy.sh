#!/usr/bin/env bash

this=`readlink -fe "$0"`
this_dir=`dirname "$this"`
cd "$this_dir"

DOCKER_IMAGE_PREFIX="ejzspb/"

while test "$#" -gt 0; do
    case "$1" in
        -h|--help)
            echo "-h, --help          show help"
            echo "-L, --login         login to nginx instance"
            echo "-T, --test          run phpunit with filter"
            echo "-e, --exec [cmd]    exec inside nginx"
            exit 0
            ;;
        -L|--login)
            login="yes"
            shift
            ;;
        -T|--test)
            test="yes"
            shift
            ;;
        -e|--exec)
            shift
            exec="$@"
            shift 1000
            ;;
        *)
            break
            ;;
    esac
done

sudo=""
[ "$EUID" -ne "0" ] && sudo="sudo"

test -f .env || cp .env.example .env
source .env
[ "$DOCKER_NAME_PREFIX" ] || {
    echo "Please, define DOCKER_NAME_PREFIX into .env";
    exit;
}

BASE="/var/www"
[ -t 0 ] && t="t"
EXEC="$sudo docker exec -i ${DOCKER_NAME_PREFIX}nginx"
EXEC_T="$sudo docker exec -i${t} ${DOCKER_NAME_PREFIX}nginx"
EXEC_GH_TOKEN="$sudo docker exec -i${t} ${DOCKER_NAME_PREFIX}nginx env GH_TOKEN=${GH_TOKEN}"

if test "$test"; then
    exec=($EXEC "phpunit" "-c" "/var/www/phpunit.xml")
elif test "$login"; then
    exec=($EXEC_T "bash")
elif test "${#exec[@]}" -gt 0; then
    exec=($EXEC_T ${exec[@]})
fi

if [ "${#exec[@]}" -gt 0 ]; then
    "${exec[@]}"
    exit "$?"
fi

list=`$sudo docker ps -a --filter "name=^/${DOCKER_NAME_PREFIX}" | awk '{print $1}' | tail -n +2`
if [ "$list" ]; then
    echo "Delete Docker containers with prefix ${DOCKER_NAME_PREFIX}:"
    $sudo docker rm -f -v $list
fi

#
# MARIADB
#
if [ "$MARIADB" = "yes" ]; then
    $sudo docker pull "$DOCKER_IMAGE_PREFIX"mariadb
fi

#
# NGINX
#
$sudo docker pull "$DOCKER_IMAGE_PREFIX"nginx
add_host=""
for dir in "$this_dir"/public/*; do
    test -d "$dir" || continue
    add_host="--add-host ${dir}:127.0.0.1 ${add_host}"
done
expose=""
if [ "$NGINX_EXPOSE" ]; then
    expose="-p ${NGINX_EXPOSE}:80"
fi
$sudo docker run -w "$BASE" $add_host -v "$this_dir":/var/www $expose \
    --name "$DOCKER_NAME_PREFIX"nginx -d "$DOCKER_IMAGE_PREFIX"nginx
{ sleep 2; $sudo docker ps | grep -q "$DOCKER_NAME_PREFIX"nginx; } || { echo "nginx failed to run!"; exit 1; }
ip=`$sudo docker inspect "$DOCKER_NAME_PREFIX"nginx | grep '"IPAddress"' | cut -d'"' -f4 | head -1`
echo "NGINX_HOST=${ip}"

echo
echo "// --------------------- //"
echo "//  Containers started!  //"
echo "// --------------------- //"
echo

set -x
set -e

$EXEC bash -c "echo 'export TERM=xterm' >>/root/.bashrc"
$EXEC bash -c "echo 'cd ${BASE}' >>/root/.bashrc"
$EXEC git config --global user.email "user@email.com"
$EXEC git config --global user.name "Name"
set +x
echo "$EXEC" "$BASE"/install.sh
if [ "$GH_TOKEN" ]; then
    echo "Run install.sh with GH_TOKEN!"
    $EXEC_GH_TOKEN "$BASE"/install.sh
else
    $EXEC "$BASE"/install.sh
fi
