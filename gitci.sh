#!/usr/bin/env bash

message="$TRAVIS_COMMIT_MESSAGE"
branch="$TRAVIS_BRANCH"

cd "`dirname "$0"`"

git diff --quiet && exit 0
git checkout "$branch"
t="https://${GH_TOKEN}@github.com/${TRAVIS_REPO_SLUG}.git"
git remote set-url origin "$t"
git status
git add "$1"
git commit -m "$2"
git push origin "$branch"
