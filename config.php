<?php
if(!defined('PLX_ROOT')) { exit; }

# Control du token du formulaire
plxToken::validateFormToken($_POST);

/*
 * Pour obtenir la liste des fichiers *.php utilisant printArea dans le dossier core/admin/ :
 * grep -l printArea ../../core/admin/*.php |sed 's/^.*\/\([^\/]\+\)\.php$/\1/'
 * grep -l n'affiche que le nom des fichiers ayant une occurence
 *
 * Pour obtenir la liste de tous les noms des champs employés par printArea :
 * grep -hi printArea ../../core/admin/*.php | sed "s/^.*printArea('\([^']\+\).*$/\1/i"|sort |uniq
 * grep -h n'affiche pas le nom des fichiers
 * */

const ADMIN_FILES =
	'article categorie comment_new comment parametres_affichage '.
	'parametres_edittpl parametres_plugincss profil statique user';
$params = array(
	'theme'				=> 'string',
	/*
	'keyboardHandler'	=> 'string',
	'minLines'			=> 'numeric',
	'maxLines'			=> 'numeric',
	* */
	'admin_files'		=> 'string'
);

$adm_files = explode(' ', ADMIN_FILES);

if(!empty($_POST)) {
	foreach($params as $field=>$type) {
		if(!empty($_POST[$field])) {
			switch($type) {
				case 'string':
					$value = $_POST[$field];
					if(is_string($value)) {
						$param = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
						$plxPlugin->setParam($field, $param, 'string');
					} elseif(is_array($value)) {
						$values = array_filter($value, function($item) use ($adm_files) {
							return in_array($item, $adm_files);
						});
						if(!empty($values)) {
							$param = implode('|', $values);
							$plxPlugin->setParam($field, $param, 'string');
						} else {
							$plxPlugin->delParam($field);
						}
					}
					break;
				default:
					// Nothing to do
			}
		} else {
			$plxPlugin->delParam($field);
		}
	}
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p='.$plugin);
	exit;
}

$baseUrl = $plxPlugin->root();
$field = 'theme';
?>
	<form id="form_<?php echo $plugin; ?>" method="post">
		<div>
			<p>
				<label for="theme"><?php $plxPlugin->lang('L_'.strtoupper($field)); ?></label>
				<?php plxUtils::printSelect(
					$field,
					$plxPlugin->get_available_themes(),
					$plxPlugin->getParam($field)
				); ?>
			</p>
		</div>
		<div class="admin-files">
<?php
	$field = 'admin_files';
	$values = explode('|', $plxPlugin->getParam($field));
	foreach($adm_files as $item) {
?>
			<p>
				<label for="file_<?php echo $item; ?>"><?php $plxPlugin->lang('L_'.strtoupper($item)); ?></label>
				<input type="checkbox"
					name="admin_files[]"
					id="file_<?php echo $item; ?>"
					value="<?php echo $item; ?>"
					<?php if(in_array($item, $values)) { echo ' checked'; } ?>
				>
			</p>
<?php
	}
?>
		</div>
		<div class="icon">
			<a href="https://ace.c9.io/" target="_blank" rel="noreferrer">
				<img src="<?php echo $plxPlugin->root(); ?>icon.png" alt="Icon" />
			</a>
		</div>
		<div>
			<p>
				<?php echo plxToken::getTokenPostMethod(); ?>
				<input type="submit" value="<?php echo L_ARTICLE_UPDATE_BUTTON; ?>" />
			</p>
		</div>
	</form>
	<div>
		<textarea name="sandbox"><?php
			echo str_replace('<',  '&lt;', file_get_contents(__FILE__));
		?></textarea>
	</div>