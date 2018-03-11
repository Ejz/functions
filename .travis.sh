#!/usr/bin/env bash

./deploy.sh || exit 1
./deploy.sh --test || exit 1
./deploy.sh --exec php gendoc.php || exit 1
./gitci.sh "README.md" "Update README.md [skip ci]" || exit 1
