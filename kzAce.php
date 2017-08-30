<?php
if(!defined('PLX_ROOT')) { exit; }

// https://ace.c9.io/
// https://docs.emmet.io/

class kzAce extends plxPlugin {

	const ACE_PATH = 'ace/lib/ace'; // sans / final !
	const APP_JS = 'app.js';

	/*
	const ACE_PATH = 'ace/build/src'; // sans / final !
	const APP_JS = 'app-build.js';
	*/

	/* const ACE_PATH = 'ace-builds/src'; */
	public function __construct($default_lang) {

		parent::__construct($default_lang);

		$this->setConfigProfil(PROFIL_ADMIN);

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
			$this->addHook('AdminPluginCss', 'AdminPluginCss');
		}
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
		$result = array();
		$files = array_map(
			function($item) {
				return preg_replace('@^.*/(\w+)\.js$@', '\1', $item);
			},
			glob(__DIR__.'/'.$this::ACE_PATH.'/theme/*.js')
		);
		foreach($files as $item) {
			$result[$item] = ucfirst($item);
		}
		return $result;
	}

	private function __print_params($embedded=false) {
		$i18n = array();
		foreach(explode(' ', 'help fullscreen settings') as $field) {
			$i18n[$field] = $this->getLang('L_'.strtoupper($field));
		}
		$params = array(
			'ace' => $this::ACE_PATH,
			'pluginName' => __CLASS__,
			'theme'	=> $this->getParam('theme'),
			'i18n'	=> $i18n
		);
		if($embedded) {
			$params['baseUrl'] = $this->root();
		}
		echo json_encode(
			$params,
			JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE + JSON_FORCE_OBJECT
		);
	}

	public function AdminFootEndBody() {
		$baseUrl = $this->root();
		/* <script type="text/javascript" data-main="<?php echo $this->root(); ?>app.js" data-params='<?php $this->__print_params();?>' src="<?php echo $baseUrl ?>ace/demo/kitchen-sink/require.js" charset="utf-8"></script> */
?>
		<!-- script type="text/javascript" src="https://cloud9ide.github.io/emmet-core/emmet.js" charset="utf-8"></script -->
		<script type="text/javascript"
			data-main="<?php echo $this->root().$this::APP_JS; ?>"
			data-params='<?php $this->__print_params();?>'
			src="<?php echo $baseUrl ?>ace-builds/demo/kitchen-sink/require.js" charset="utf-8"
		>
		</script>
<?php
	}
}

?>