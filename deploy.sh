#!/usr/bin/env bash

this=`realpath "$0"`
this_dir=`dirname "$this"`
cd "$this_dir"
prefix=`basename "$this_dir"`

sudo=""
[ "$EUID" -ne "0" ] && { sudo="sudo"; }

vars=()
DOCKER_NAME_PREFIX="${prefix}_"
DOCKER_IMAGE_PREFIX="ejzspb/"

"$sudo" docker pull "$DOCKER_IMAGE_PREFIX"mariadb
"$sudo" docker run -d --name "$DOCKER_NAME_PREFIX"mariadb -p 127.0.0.1:3306:3306 \
    -e "MYSQL_RANDOM_ROOT_PASSWORD=yes" -e "MYSQL_DATABASE=${SQL_DB}" \
    -e "MYSQL_USER=${SQL_USER}" -e "MYSQL_PASSWORD=${SQL_PASS}" "$DOCKER_IMAGE_PREFIX"mariadb
{ sleep 2; docker ps | grep -q "$DOCKER_NAME_PREFIX"mariadb; } || { echo "MariaDB failed to run!"; exit 1; }

./install.sh
