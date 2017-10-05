#!/bin/sh

MODE_PATH="ace/lib/ace/mode"

count=$(ls ${MODE_PATH}/*_highlight_rules.js | wc -l)
echo "$count modes existants"

for mode in $(cat exclude-modes.txt); do
	echo $mode
	rm -f "${MODE_PATH}/${mode}.js"
	rm -f "${MODE_PATH}/${mode}_highlight_rules.js"
	rm -fR "${MODE_PATH}/${mode}"
done

count=$(ls ${MODE_PATH}/*_highlight_rules.js | wc -l)
echo "$count modes restants"