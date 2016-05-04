#!/usr/bin/env bash

BASEDIR=$(dirname "$0")
cd $BASEDIR
rm -rf node_modules
npm install
echo 'Install finished'