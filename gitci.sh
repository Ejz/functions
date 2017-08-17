#!/usr/bin/env bash

message="$TRAVIS_COMMIT_MESSAGE"
branch="$TRAVIS_BRANCH"

cd "`dirname "$0"`"

function inc_ver() {
    script='NF==1{print ++$NF};NF>1{if(length($NF+1)>length($NF))$(NF-1)++;$NF=sprintf("%0*d",length($NF),($NF+1)%(10^length($NF)));print}'
    echo "$1" | awk -F. -v OFS=. "$script"
}

# Pull requests and commits to not master branch just build, no deploy!!!
if [ "$TRAVIS_PULL_REQUEST" != "false" -o "$branch" != "master" ]; then
    echo "SKIP!"
    exit 0
fi

git diff --quiet && exit 0
git checkout "$branch"
t="https://${GH_TOKEN}@github.com/${TRAVIS_REPO_SLUG}.git"
git remote set-url origin "$t"
git status
git add "$1"
git commit -m "$2"
tag=`git tag | grep -P '^v1\.\d+\.\d+' | tail -n 1`
if [ -z "$tag" ]; then
    ntag="v1.0.0"
elif echo "$tag" | grep -qP -- '-RC\d+$'; then
    ntag=`inc_ver "$tag"`
else
    ntag=`inc_ver "$tag"`
    ntag="${ntag}-RC1"
fi
echo "Add ${ntag} tag!"
git tag -a "$ntag" -m "$ntag"
git push origin "$branch"
git push --tags
