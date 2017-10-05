<?php
if(!defined('PLX_ROOT')) { exit; }

// https://ace.c9.io/
// https://docs.emmet.io/

class kzAce extends plxPlugin {

	//const ACE_PATH = 'ace/lib/ace'; // sans / final !
	const ACE_PATH = 'ace/build/src'; // sans / final !

	const EMMET_CORE_URL = 'https://cloud9ide.github.io/emmet-core/emmet.js';

	public function __construct($default_lang) {

		parent::__construct($default_lang);

		$this->setConfigProfil(PROFIL_ADMIN);


		if(is_dir(__DIR__.'/'.$this::ACE_PATH)) {
			// parametres_plugin.php?p=kzAce
			$script_name = basename($_SERVER['SCRIPT_NAME'], '.php');
			$enabled = explode('|', $this->getParam('admin_files'));
			if(
				in_array($script_name, $enabled) or
				(
					($script_name == 'parametres_plugin') and
					!empty($_GET['p']) and
					($_GET['p'] == __CLASS__)
				)
			) {
				$this->addHook('AdminFootEndBody', 'AdminFootEndBody');
			}
		} else {
			plxMsg::Error(sprintf($this->getLang('L_MISSING_LIB_ACE'), __DIR__.'/'.$this::ACE_PATH));
		}

		$this->addHook('AdminPluginCss', 'AdminPluginCss');
	}

	public function root() {
		return PLX_PLUGINS.__CLASS__.'/';
	}

	/**
	 * Gestion des onglets pour les feuilles de style des plugins.
	 *
	 * Devrait logiquement être intégré à PluXml !
	 * */
	public function AdminPluginCss() {
?>
	<script type="text/javascript">
		(function() {
			const ACTIVE = 'active';

			function activeTab(event) {
				event.preventDefault();
				document.body.querySelectorAll('label.' + ACTIVE).forEach(function(item) {
					item.classList.remove(ACTIVE);
				});
				event.target.classList.add(ACTIVE);
			}

			var counter = 0;
			document.body.querySelectorAll('#parametres_plugincss fieldset .grid > div').forEach(function(item) {
				item.classList.add('tab');
				const label = item.querySelector('label:first-of-type');
				label.setAttribute('data-order', counter);
				label.addEventListener('click', activeTab);
				if(counter == 0) {
					label.classList.add(ACTIVE);
				}
				counter++;
			});
		})();
	</script>
<?php
	}

	public function get_available_themes() {
		/* Dans les sources ou dans les modules compilés, l'emplacement des fichiers de thème varie :
		 *	ace/lib/ace/theme/*.js
		 *	ace/build/src/theme-*.js
		 * */

		// GLOB_BRACE n'est pas supporté par Alpine-Linux
		$theme_path = '/theme'.((strpos($this::ACE_PATH, 'build') > 0) ? '-' : '/').'*.js';

		$result = array('' => $this->getLang('L_DEFAULT'));
		$files = array_map(
			function($item) {
				return preg_replace('@^.*/(?:theme-)?(\w+)\.js$@', '\1', $item);
			},
			glob(__DIR__.'/'.$this::ACE_PATH.$theme_path)
		);
		foreach($files as $item) {
			$result[$item] = ucfirst($item);
		}
		return $result;
	}

	private function __print_params() {
		$i18n = array();
		foreach(explode(' ', 'help fullscreen settings') as $field) {
			$i18n[$field] = $this->getLang('L_'.strtoupper($field));
		}
		$params = array(
			'ace'			=> $this::ACE_PATH,
			'baseUrl'		=> $this->root(),
			'pluginName'	=> __CLASS__,
			'emmetCoreUrl'	=> $this::EMMET_CORE_URL,
			'i18n'			=> $i18n
		);
		$theme = $this->getParam('theme');
		if(!empty($theme)) {
			$params['theme'] = $theme;
		}

		echo json_encode(
			$params,
			JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE + JSON_FORCE_OBJECT
		);
	}

	public function AdminFootEndBody() {
		// Utiliser les apostrophes simples pour data-params (données JSON) !
		$function_name = 'json_encode';
		if(function_exists($function_name)) {
?>
		<script
			type="text/javascript"
			src="<?php echo $this->root(); ?>require.js"
			data-main="<?php echo $this->root(); ?>app.js"
			data-params='<?php $this->__print_params(); ?>'
		></script>
<?php
		} else {
			plxMsg::Error(sprintf($this->getLang('L_MISSING_FUNCTION'), $function_name));
		}
	}
}
?>