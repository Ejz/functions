#!/usr/bin/env bash

cd "`dirname "$0"`"

# Install composer itself
if [ ! -f "composer.phar" ]; then
    echo "Installing Composer .."
    curl -sS 'https://getcomposer.org/installer' | php
    chmod a+x composer.phar
fi

# Install PHPUnit
if [ ! -f "phpunit.phar" ]; then
    echo "Installing PHPUnit .."
    ver="3.7"
    php_ver=`php -v | head -1 | cut -d" " -f2`
    if [ "$php_ver" = "`echo -e "${php_ver}\n5.6" | sort -rV | head -n1`" ]; then
        ver="5.7"
    fi
    wget "https://phar.phpunit.de/phpunit-${ver}.phar" -O phpunit.phar
    chmod a+x phpunit.phar
fi

# Composer install/update
./composer.phar install
./composer.phar update

# Patch NormalizerFormatter.php
p1="vendor/monolog/monolog/src/Monolog/Formatter/NormalizerFormatter.php"
p2="patch/normalizer.patch"
if [ -f "$p1" ] && [ -f "$p2" ]; then
    patch "$p1" "$p2"
fi

# Patch ErrorHandler.php
p1="vendor/monolog/monolog/src/Monolog/ErrorHandler.php"
p2="patch/errorhandler.patch"
if [ -f "$p1" ] && [ -f "$p2" ]; then
    patch "$p1" "$p2"
fi
