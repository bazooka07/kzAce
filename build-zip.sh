#!/bin/sh

CURRENT_PWD=$(pwd)
cd $(dirname ${0})

PLUGIN_NAME=$(basename $PWD)
VERSION=$(\
	grep "<version>" infos.xml \
	| sed 's/^\s*<version>\([^<]*\)<\/version>.*$/\1/' \
)
echo "\n\033[33mPlugin ${PLUGIN_NAME} - version ${VERSION}\033[0m\n"

cd ..

ZIP_NAME="${CURRENT_PWD}/${PLUGIN_NAME}-$(echo ${VERSION} | sed 's/\./_/g').zip"
PATTERN1='(css/.*|lang/.*|\w[^/]*|\.htaccess|ace/build/src/.*)'

# Construction de l'archive
find ${PLUGIN_NAME} -regextype posix-egrep -regex "^${PLUGIN_NAME}/${PATTERN1}" \
	| zip ${ZIP_NAME} -@

# Dater l'archive zip avec le fichier le plus récent
PATTERN2='(css/.*|lang/.*|\w[^/]*)'
NEWER_FILE=$(\
	find  ${PLUGIN_NAME} -regextype posix-egrep -regex "^${PLUGIN_NAME}/${PATTERN1}" -print0 \
	| xargs -0 ls -dt | head -1 \
)
touch -r ${NEWER_FILE} ${ZIP_NAME}

echo "\nNom complet de l'archive du plugin:\n\033[32m${ZIP_NAME}\033[0m\n"

cd $OLDPWD

echo "Done !"