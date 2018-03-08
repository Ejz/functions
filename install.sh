#!/usr/bin/env bash

cd "`dirname "$0"`"

[ "$EUID" -eq "0" ] || { echo "Please, run ${0} with root!"; exit 1; }

composer self-update
composer config --global github-protocols https
[ "$GH_TOKEN" ] && composer config -g github-oauth.github.com "$GH_TOKEN"
composer install
composer update

test -f .env || cp .env.example .env

# SCSS
if [ -d public ]; then
    cd public
    if [ -f sass.pid ] && kill `cat sass.pid` >/dev/null 2>&1; then
        echo "Killed previous sass process .. PID = "`cat sass.pid`
    fi
    find . -type f -iname '*.css' -or -iname '*.css.map' -delete
    nohup sass --watch .:. --style compressed >sass.log 2>&1 &
    echo "$!" >sass.pid
    sleep 5
    cd -
fi
