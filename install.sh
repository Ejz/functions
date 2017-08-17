#!/usr/bin/env bash

cd "`dirname "$0"`"

# Install composer itself
if ! which composer; then
    echo "Installing Composer .."
    curl -sS 'https://getcomposer.org/installer' | php
    chmod a+x composer.phar
    mv composer.phar /usr/local/bin/composer
fi

# Install PHPUnit
if ! which phpunit; then
    echo "Installing PHPUnit .."
    wget "https://phar.phpunit.de/phpunit-5.7.phar" -O phpunit.phar
    chmod a+x phpunit.phar
    mv phpunit.phar /usr/local/bin/phpunit
fi

# Composer install
if [ ! -d vendor ]; then
    composer install
else
    composer update
fi

# Some patches
p1="vendor/monolog/monolog/src/Monolog/Formatter/NormalizerFormatter.php"
p2="patch/normalizer.patch"
if [ -f "$p1" ] && [ -f "$p2" ]; then
    patch "$p1" "$p2"
fi
p1="vendor/monolog/monolog/src/Monolog/ErrorHandler.php"
p2="patch/errorhandler.patch"
if [ -f "$p1" ] && [ -f "$p2" ]; then
    patch "$p1" "$p2"
fi
