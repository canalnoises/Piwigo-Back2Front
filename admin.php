<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $conf, $template;
load_language('plugin.lang', B2F_PATH);
$conf['back2front'] = explode(',', $conf['back2front']);

// Enregistrement de la configuration
if (isset($_POST['submit']))
{
	$conf['back2front'] = array($conf['back2front'][0], $_POST['switch_mode'], $_POST['transition']);
			
    $query = 'UPDATE ' . CONFIG_TABLE . '
		SET value="' . implode (',', $conf['back2front']) . '"
		WHERE param="back2front"';
    pwg_query($query);
	
	array_push($page['infos'], l10n('Information data registered in database'));
}

$template->assign(array(
	'SWITCH_MODE' => $conf['back2front'][1],
	'TRANSITION' => $conf['back2front'][2],
));
	
$template->set_filename('back2front_conf', dirname(__FILE__).'/template/admin.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'back2front_conf');

?>