#!/usr/bin/env bash

this=`readlink -fe "$0"`
this_dir=`dirname "$this"`
cd "$this_dir"
prefix=`basename "$this_dir"`
prefix=`echo "$prefix" | awk '{print tolower($0)}'`
while test "$#" -gt 0; do
    case "$1" in
        -h|--help)
            echo "-h, --help          show help"
            echo "-D, --defaults      do not ask user"
            echo "-L, --login         login to nginx instance"
            echo "-T, --test          run phpunit with filter"
            echo "-e, --exec [cmd]    exec inside nginx"
            exit 0
            ;;
        -D|--defaults)
            defaults="yes"
            shift
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

vars=()
vars+=("DOCKER_NAME_PREFIX ${prefix}_")
vars+=("DOCKER_IMAGE_PREFIX ejzspb/")
vars+=("DB_HOST yes")
vars+=("DB_PORT 3306")
vars+=("DB_USER user")
vars+=("DB_PASS pass")
vars+=("DB_NAME ${prefix}")
vars+=("EXPOSE_NGINX 0.0.0.0:80")
vars+=("EXPOSE_MARIADB 127.0.0.1:3306")

for var in "${vars[@]}"; do
    one=`echo "$var" | cut -d" " -f1`
    one_=`echo "$one" | cut -d_ -f1`
    one_host="${one_}_HOST"
    if [ "${!one_host}" == "no" -a "$one_host" != "$one" ]; then
        continue
    fi
    two=`echo "$var" | cut -d" " -f2-`
    temp="${!one}"
    if [ -z "$temp" ] && [ -f "vars/${one}" ]; then
        temp=`cat "vars/${one}"`
    fi
    [ "$temp" ] && two="$temp"
    append=", Docker - [yes], Ignore - [no]"
    echo "$one" | grep -q "_HOST" || append=""
    echo -n "${one}? (${two} - [ENTER]${append}): "
    if [ ! -t 0 ] || [ "$defaults" ]; then
        echo
        input=""
    else
        read input
    fi
    if [ -z "$input" ]; then
        temp="$two"
    else
        temp="$input"
    fi
    mkdir -p vars
    echo "$temp" >"vars/${one}"
    eval export "$one"='$temp'
done

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

if [ "${#exec[@]}" -gt 0 ] && $sudo docker ps --filter "name=^/${DOCKER_NAME_PREFIX}nginx" | grep -q "$DOCKER_NAME_PREFIX"nginx; then
    "${exec[@]}"
    exit "$?"
fi

# $sudo rm -f local.ini cgi/local.ini
# $sudo rm -rf vendor cgi/vendor

list=`$sudo docker ps -a --filter "name=^/${DOCKER_NAME_PREFIX}" | awk '{print $1}' | tail -n +2`
if [ "$list" ]; then
    echo "Delete Docker containers with prefix ${DOCKER_NAME_PREFIX}:"
    $sudo docker rm -f -v $list
fi

#
# MARIADB
#
if [ "$DB_HOST" == "yes" ]; then
    $sudo docker pull "$DOCKER_IMAGE_PREFIX"mariadb
    expose=""
    if [ "$EXPOSE_MARIADB" ]; then
        expose="-p ${EXPOSE_MARIADB}:${DB_PORT}"
    fi
    $sudo docker run -d --name "$DOCKER_NAME_PREFIX"mariadb $expose \
        -e "MYSQL_RANDOM_ROOT_PASSWORD=yes" -e "MYSQL_DATABASE=${DB_NAME}" \
        -e "MYSQL_USER=${DB_USER}" -e "MYSQL_PASSWORD=${DB_PASS}" "$DOCKER_IMAGE_PREFIX"mariadb
    { sleep 2; $sudo docker ps | grep -q "$DOCKER_NAME_PREFIX"mariadb; } || { echo "mariadb failed to run!"; exit 1; }
    ip=`$sudo docker inspect "$DOCKER_NAME_PREFIX"mariadb | grep '"IPAddress"' | cut -d'"' -f4 | head -1`
    DB_HOST="$ip"
    echo "DB_HOST=${DB_HOST}"
    echo "DB_PORT=${DB_PORT}"
fi

#
# NGINX
#
add_host=""
for dir in "$this_dir"/public/*; do
    test -d "$dir" || continue
    add_host="--add-host ${dir}:127.0.0.1 ${add_host}"
done
$sudo docker pull "$DOCKER_IMAGE_PREFIX"nginx
expose=""
if [ "$EXPOSE_NGINX" ]; then
    expose="-p ${EXPOSE_NGINX}:80"
fi
$sudo docker run -w "$BASE" $add_host -v "$this_dir":/var/www $expose \
    --name "$DOCKER_NAME_PREFIX"nginx -d "$DOCKER_IMAGE_PREFIX"nginx
{ sleep 2; $sudo docker ps | grep -q "$DOCKER_NAME_PREFIX"nginx; } || { echo "nginx failed to run!"; exit 1; }
ip=`$sudo docker inspect "$DOCKER_NAME_PREFIX"nginx | grep '"IPAddress"' | cut -d'"' -f4 | head -1`
echo "HOST=${ip}"

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
set -x

$EXEC php "$BASE"/index.php setenv DB_HOST "$DB_HOST"
$EXEC php "$BASE"/index.php setenv DB_PORT "$DB_PORT"
$EXEC php "$BASE"/index.php setenv DB_NAME "$DB_NAME"
$EXEC php "$BASE"/index.php setenv DB_USER "$DB_USER"
$EXEC php "$BASE"/index.php setenv DB_PASS "$DB_PASS"

if [ "${#exec[@]}" -gt 0 ]; then
    "${exec[@]}"
    exit "$?"
fi
