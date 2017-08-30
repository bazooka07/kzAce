// For Javascript embedded in html or php page
//const PARAMS = <?php $this->__print_params(true); ?>

const PARAMS = JSON.parse(document.querySelector('script[data-main]').getAttribute('data-params'));

// console.log('PARAMS:');
// console.log(PARAMS);

require.config({
	// baseUrl	: PARAMS.baseUrl,
	paths: {
		cloud9: 'https://cloud9ide.github.io/emmet-core/emmet',
		ace: PARAMS.ace
	},
	shim: {
		ace: {
			deps: ['cloud9'],
			exports: 'emmet'
		}
	}
});

define(
	PARAMS.pluginName,
	['ace/ace', 'cloud9', 'ace/ext/statusbar', 'ace/ext/emmet' , 'ace/ext/keybinding_menu'],
	// ['ace/ace', 'cloud9', 'ace/ext/statusbar', 'ace/ext/emmet', 'ace/ext/menu_tools/get_editor_keyboard_shortcuts'],
	function(ace, cloud9) {

		function myFullScreen(editor) {
			const FULL_SCREEN = 'fullScreen';

			document.body.classList.toggle(FULL_SCREEN);
			const fullScreen = document.body.classList.contains(FULL_SCREEN);
			if(fullScreen) {
				editor.container.classList.add(FULL_SCREEN);
			} else {
				editor.container.classList.remove(FULL_SCREEN);
			}
			editor.setAutoScrollEditorIntoView(!fullScreen);

			// hack against Ace !
			if(fullScreen) {
				editor.container.maxLines = editor.getOption('maxLines');
				editor.container.removeAttribute('style');
				editor.setOption('maxLines', '');
			} else if(editor.container.maxLines) {
				editor.setOption('maxLines', editor.container.maxLines);
			}

			editor.resize();
		}

		function myShowKeyboardShortcuts(editor) {
			const ID1 = PARAMS.pluginName + '-kb-shortcuts';

			var container = document.getElementById(ID1);
			if(container == null) {
				const TARGET = 'document.getElementById(\'' + ID1 + '\')';
				const CLOSE_CLICK = TARGET + '.classList.toggle(\'active\')';
				const CLOSE_BTN =
					'<div class="close">' +
						'<button ' +
							'onclick="' + CLOSE_CLICK + '"' +
						'>X</button>' +
					'</div>';
				var items = [];
				const getEditorKeybordShortcuts = require('ace/ext/menu_tools/get_editor_keyboard_shortcuts').getEditorKeybordShortcuts;
				const keybindings = getEditorKeybordShortcuts(editor);
				keybindings.forEach(function(item) {
					items.push('<li><span>' + item.key + '</span> : ' + item.command + '</li>')
				});
				var container = document.createElement('DIV');
				container.id = ID1;
				container.innerHTML =
					'<div>' +
						'<h1>ShortCuts keyboard</h1>' +
						'<ul>' +
							items.join("\n") +
						'</ul>' +
						CLOSE_BTN +
					'</div>';
				document.body.appendChild(container);
			}
			container.classList.toggle('active');
		}

		function myShowSettingsMenu(editor) {
			config.loadModule("ace/ext/settings_menu", function(module) {
	            module.init(editor);
	            editor.showSettingsMenu();
	        });
		}

		console.log('< ' + '-'.repeat(25) + ' This is the ' + PARAMS.pluginName + ' module ' + '-'.repeat(25) + ' >');

		// Shortcuts keyboard
		// ace/lib/ace/commands/default_commands.js
		const DEFAULT_COMMANDS = require("ace/commands/default_commands");
		[
			{ name: 'fullscreen', bindKey: 'F11', exec: myFullScreen },
			{ name: 'helpMe', bindKey: 'Ctrl-F1', exec: myShowKeyboardShortcuts }
		].forEach(function(item) { DEFAULT_COMMANDS.commands.push(item); });

		// resolve conflict about "Ctrl-," between Ace and Emmet
		var ind = DEFAULT_COMMANDS.commands.findIndex(function(command) {
			return (command.name == 'showSettingsMenu');
		});
		if(ind != null) {
			DEFAULT_COMMANDS.commands[ind].bindKey = 'Ctrl-F11';
		}

		function focusOn(focus, editor) {
			// Ace does not manage resize event !
			// So drop and get the focus for enforcing repaint of the editor
			const container = editor.container;
			if(container.classList.contains('resize')) {
				const h = container.style.height;
				if(typeof container.previousHeight == 'undefined') {
					// console.log('I have the focus for the 1st time');
					container.previousHeight = h;
				}
				else if(container.previousHeight != h) {
					// console.log('I have the focus');
					const maxLines = editor.getOption('maxLines');
					if(typeof maxLines == 'number') {
						editor.setOption('maxLines', '');
					}
					editor.resize();
					container.previousHeight = h;
				}
			}
		}

		function createEditor(node, mode, context='normal') {
			const editor = ace.edit(node);
		    editor.setTheme('ace/theme/' + PARAMS.theme);
		    editor.getSession().setMode('ace/mode/' + mode);
		    const options = {
				normal: {
					minLines	: 4,
					maxLines	: 25, // remove style in the editor.container in fullscreen mode !
					autoScrollEditorIntoView: true,
					vScrollBarAlwaysVisible: true,
					enableEmmet	: true
				},
				sandbox: {
					minLines	: 2,
					maxLines	: 15,
					autoScrollEditorIntoView: true
				}
			}
			if(context in options) {
			    editor.setOptions(options[context]);
			}

			return editor;
		}

		const TEXTAREAS = 'content chapo backend frontend sandbox'.split(' ').map(function(item) {
			return item.replace(/^(\w+)$/, 'textarea[name="$1"]')
		});

		document.querySelectorAll(TEXTAREAS).forEach(function(node) {
		    if(node.name == 'sandbox') {
				// For testing in config.php
				const ed = createEditor(node, 'php', 'sandbox');
				const select = document.querySelector('select[name="theme"]');
				if(select != null) {
					select.addEventListener('change', function(event) {
						event.preventDefault();
						ed.setTheme('ace/theme/' + this.value);
					});
				}
			} else {
				const form1 = node.form;
				// Pour modifier z-index de .section .action-bar
				if((form1 != null)) {
					form1.classList.add(PARAMS.pluginName);
				}

				// container for editor
				const pre1 = document.createElement('PRE');
				pre1.id = 'ace-' + node.name;
				pre1.className = 'resize';
				node.parentElement.appendChild(pre1);

				// Container for statusbar
				const statusBar = document.createElement('DIV');
				statusBar.classList.add('statusbar');
				statusBar.innerHTML =
					'<div>' +
						'<span>' + PARAMS.pluginName + ' plugin</span>' +
						'<span data-command="helpMe">' + PARAMS.i18n.help + ': Ctrl-F1</span>' +
						'<span data-command="fullscreen">' + PARAMS.i18n.fullscreen + ': F11</span>' +
						'<span data-command="showSettingsMenu">' + PARAMS.i18n.settings + ': Ctrl-F11</span>' +
						'<span>Ace version ' + ace.version + '</span>' +
					'</div>' +
					'<div class="spacer">&nbsp;</div>';
				node.parentElement.appendChild(statusBar);

				node.classList.add('hide');

			    var mode = 'php';
			    if(/^(?:back|front)end/.test(node.name)) {
					mode = 'css';
				} else if('template' in form1.elements) {
					const ext = form1.elements.template.value.replace(/^.*\.(\w+)$/, '$1');
					if(/^(?:css|html|javascript|json|xml|yaml)$/.test(ext)) {
						mode = ext;
					}
				}
			    const ed = createEditor(pre1, mode);
			    ed.setValue(node.value);
			    ed.on('focus', focusOn);
			    statusBar.editor = ed;
			    statusBar.addEventListener('click', function(event) {
					if(event.target.tagName == 'SPAN' && event.target.hasAttribute('data-command')) {
						event.preventDefault();
						this.editor.execCommand(event.target.getAttribute('data-command'));
					}
				});

				const StatusBar = ace.require("ace/ext/statusbar").StatusBar;
				var st = new StatusBar(ed, statusBar);

				// Traitement avant envoi du formulaire
				if(form1 != null) {
					if(form1.editors == undefined) {
						form1.editors = [];
						form1.addEventListener('submit', function(event) {
							this.editors.forEach(function(item) {
								item.node.value = item.editor.getValue();
							});
						});
					}
					form1.editors.push({
						editor	: ed,
						node	: node
					});
				}
			}
		});
	}
);

require([PARAMS.pluginName]);