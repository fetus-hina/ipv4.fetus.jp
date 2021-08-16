#!/bin/bash

npx updates -u -m
\rm -rf package-lock.json node_modules
npm install
npm install '@fetus-hina/fetus.css'

./composer.phar update
