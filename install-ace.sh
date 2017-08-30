#!/bin/sh

git clone --depth 1 https://github.com/ajaxorg/ace.git
cd ace
npm install
node ./Makefile.dryice.js -m

# Only for Apache2
echo 'Options +Indexes' > demo/.htaccess
