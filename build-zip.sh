#!/bin/sh

cd $(dirname ${0})

PLUGIN_NAME=$(basename $PWD)
VERSION=$(\
	grep "<version>" infos.xml \
	| sed 's/^\s*<version>\([^<]*\)<\/version>.*$/\1/; s/\./_/' \
)

cd ..

ZIP_NAME="${PLUGIN_NAME}-${VERSION}.zip"
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

echo "L'archive ${ZIP_NAME} est dans le dossier ${PWD}."

cd $OLDPWD

echo "Done !"