#!/usr/bin/env bash

this=`readlink -fe "$0"`
this_dir=`dirname "$this"`
cd "$this_dir"
prefix=`basename "$this_dir"`

sudo=""
[ "$EUID" -ne "0" ] && sudo="sudo"

vars=()
vars+=("HOST ${prefix}.dev")
vars+=("DOCKER_NAME_PREFIX ${prefix}_")
vars+=('DOCKER_IMAGE_PREFIX ejzspb/')
vars+=('SQL_HOST yes')
vars+=('SQL_PORT 3306')
vars+=('SQL_USER user')
vars+=('SQL_PASS pass')
vars+=("SQL_DB ${prefix}")

for var in "${vars[@]}"; do
    one=`echo "$var" | cut -d" " -f1`
    two=`echo "$var" | cut -d" " -f2-`
    temp="${!one}"
    if [ -z "$temp" ] && [ -f "vars/${one}" ]; then
        temp=`cat "vars/${one}"`
    fi
    [ "$temp" ] && two="$temp"
    append=", Docker - [yes], Ignore - [no]"
    echo "$one" | grep -q "_HOST" || append=""
    echo -n "${one}? (${two} - [ENTER]${append}): "
    if [ -t 0 ]; then
        read input
    else
        echo
        input=""
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

rm -f cgi/local.ini
list=`docker ps -a --filter "name=^/${DOCKER_NAME_PREFIX}" | awk '{print $1}' | tail -n +2`
if [ "$list" ]; then
    echo "Delete Docker containers with prefix ${DOCKER_NAME_PREFIX}:"
    "$sudo" docker rm -f -v $list
fi

if [ "$SQL_HOST" == "yes" -o "$SQL_HOST" == "localhost" -o "$SQL_HOST" == "127.0.0.1" ]; then
    if [ "$SQL_HOST" != "yes" ] && lsof -i -P -n | grep LISTEN | grep -q ":${SQL_PORT}"; then
        echo "mariadb failed to run! port is occupied!"
        exit 1
    fi
    expose=""
    [ "$SQL_HOST" != "yes" ] && expose="-p ${SQL_HOST}:${SQL_PORT}:3306"
    docker pull "$DOCKER_IMAGE_PREFIX"mariadb
    docker run -d --name "$DOCKER_NAME_PREFIX"mariadb ${expose} \
        -e "MYSQL_RANDOM_ROOT_PASSWORD=yes" -e "MYSQL_DATABASE=${MARIADB_DBNAME}" \
        -e "MYSQL_USER=${MARIADB_USER}" -e "MYSQL_PASSWORD=${MARIADB_PASS}" "$DOCKER_IMAGE_PREFIX"mariadb
    { sleep 2; docker ps | grep -q "$DOCKER_NAME_PREFIX"mariadb; } || { echo "mariadb failed to run!"; exit 1; }
    ip=`docker inspect "$DOCKER_NAME_PREFIX"mariadb | grep IPAddress | tail -1 | cut -d'"' -f4`
    SQL_HOST="$ip"
    echo "SQL_HOST=${SQL_HOST}"
fi

if [ "$ELASTICSEARCH_HOST" == "yes" ]; then
    docker pull elasticsearch
    docker run -d --name "$DOCKER_NAME_PREFIX"elasticsearch -e "ES_JAVA_OPTS=-Xms512m -Xmx512m" elasticsearch
    { sleep 2; docker ps | grep -q "$DOCKER_NAME_PREFIX"elasticsearch; } || { echo "elasticsearch failed to run!"; exit 1; }
    ip=`docker inspect "$DOCKER_NAME_PREFIX"elasticsearch | grep IPAddress | tail -1 | cut -d'"' -f4`
    ELASTICSEARCH_HOST="$ip"
    echo "ELASTICSEARCH_HOST=${ELASTICSEARCH_HOST}"
fi

# Start nginx
if [ "$HOST" ]; then
    docker pull "$DOCKER_IMAGE_PREFIX"nginx
    expose=""
    lsof -i -P -n | grep LISTEN | grep -q ':80' || expose="-p 0.0.0.0:80:80"
    docker run --add-host "$HOST":127.0.0.1 -v "`pwd`":/var/www/"$HOST" ${expose} \
        --name "$DOCKER_NAME_PREFIX"nginx -d "$DOCKER_IMAGE_PREFIX"nginx
    { sleep 2; docker ps | grep -q "$DOCKER_NAME_PREFIX"nginx; } || { echo "nginx failed to run!"; exit 1; }
    ip=`docker inspect "$DOCKER_NAME_PREFIX"nginx | grep IPAddress | tail -1 | cut -d'"' -f4`
    echo "HOST=${ip}"
fi

echo
echo "// --------------------- //"
echo "//  Containers started!  //"
echo "// --------------------- //"
echo

if [ "$HOST" ]; then
    CGI="/var/www/${HOST}/cgi"
    EXEC="docker exec -i ${DOCKER_NAME_PREFIX}nginx"
    $EXEC "$CGI"/../install.sh
    $EXEC php "$CGI"/bootstrap.php ini_file_set LOCAL_INI global.default_host "$HOST"
    $EXEC php "$CGI"/bootstrap.php ini_file_set LOCAL_INI mailgun.domain "$HOST"
    $EXEC php "$CGI"/bootstrap.php ini_file_set LOCAL_INI sql.host "$SQL_HOST"
    $EXEC php "$CGI"/bootstrap.php ini_file_set LOCAL_INI sql.port "$SQL_PORT"
    $EXEC php "$CGI"/bootstrap.php ini_file_set LOCAL_INI sql.user "$SQL_USER"
    $EXEC php "$CGI"/bootstrap.php ini_file_set LOCAL_INI sql.pass "$SQL_PASS"
    $EXEC php "$CGI"/bootstrap.php ini_file_set LOCAL_INI sql.db "$SQL_DB"

    $EXEC php "$CGI"/../phpunit.phar --filter=testSmoke
else
    ./install.sh
fi
