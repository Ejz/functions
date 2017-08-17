#!/usr/bin/env bash

message="$TRAVIS_COMMIT_MESSAGE"
branch="$TRAVIS_BRANCH"

cd "`dirname "$0"`"

./install.sh
