#!/bin/sh

# Node doit être installé

git clone --depth 1 https://github.com/ajaxorg/ace.git
cd ace

# pour les modes, voir script ace/lib/ace/ext/modelist.js
# ou ls ace/li/ace/mode/*_highlight_rules.js ( 136 fichiers )

MODE_PATH="ace/lib/ace/mode"
for mode in $(cat exclude-modes.txt); do
	rm -f "${MODE_PATH}/${mode}.js"
	rm -f "${MODE_PATH}/${mode}_highlight_rules.js"
	rm -fR "${MODE_PATH}/${mode}"
done

npm install

# Appliquer le patch pour corriger quelques bugs de Ace

# Only for Apache2
echo 'Options +Indexes' > demo/.htaccess

# Makefile.dryice est boggué. A remplacer par optimize de requirejs.org
# Ça buggue avec ace/ext/emmet
# Ne pas utiliser l'option --m pour minify. Ça buggue
node ./Makefile.dryice.js

npm install almond
cd demo/r.js/packed.js
r.js -o build.js
mv packet.js ../../../

cd ../../../

# on télécharge requirejs
wget http://requirejs.org/docs/release/2.3.5/minified/require.js

echo "Enjoy, my friends !"