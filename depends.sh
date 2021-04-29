#!/bin/bash

npx updates -u -m
\rm -rf package-lock.json node_modules
npm install

./composer.phar update
