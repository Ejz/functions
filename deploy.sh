#!/usr/bin/env bash

this=`readlink -fe "$0"`
this_dir=`dirname "$this"`
cd "$this_dir"
prefix=`basename "$this_dir"`

sudo=""
[ "$EUID" -ne "0" ] && { sudo="sudo"; }

vars=()
vars+=("DOCKER_NAME_PREFIX ${prefix}_")
vars+=("DOCKER_IMAGE_PREFIX ejzspb/")
vars+=("SQL_HOST localhost")
vars+=("SQL_USER user")
vars+=("SQL_PASS pass")
vars+=("SQL_PORT 3306")
vars+=("SQL_DBNAME ${prefix}")

SAVE_DEFAULTS=""

for var in "${vars[@]}"; do
    one=`echo "$var" | cut -d" " -f1`
    two=`echo "$var" | cut -d" " -f2-`
    temp="${!one}"
    if [ -z "$temp" ] && [ -f "vars/${one}" ]; then
        temp=`cat "vars/${one}"`
    fi
    [ "$temp" ] && two="$temp"
    echo -n "Set ${one} (defaults to ${two}): "
    if [ "$PS1" ]; then
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
    [ "$SAVE_DEFAULTS" ] && echo "$temp" >"vars/${one}"
    eval export "$one"='$temp'
done

list=`"$sudo" docker ps -a --filter "name=^/${DOCKER_NAME_PREFIX}" | awk '{print $1}' | tail -n +2`
if [ "$list" ]; then
    echo "Delete Docker containers with prefix ${DOCKER_NAME_PREFIX}:"
    "$sudo" docker rm -f -v $list
fi

"$sudo" docker pull "$DOCKER_IMAGE_PREFIX"mariadb
"$sudo" docker run -d --name "$DOCKER_NAME_PREFIX"mariadb -p 127.0.0.1:"$SQL_PORT":3306 \
    -e "MYSQL_RANDOM_ROOT_PASSWORD=yes" -e "MYSQL_DATABASE=${SQL_DB}" \
    -e "MYSQL_USER=${SQL_USER}" -e "MYSQL_PASSWORD=${SQL_PASS}" "$DOCKER_IMAGE_PREFIX"mariadb
{ sleep 2; "$sudo" docker ps | grep -q "$DOCKER_NAME_PREFIX"mariadb; } || { echo "MariaDB failed to run!"; exit 1; }

./install.sh
