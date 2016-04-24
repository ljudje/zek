#!/usr/bin/env bash

BASEDIR=$(dirname "$0")
cd $BASEDIR
make deploy
echo 'Deploy finished'