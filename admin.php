<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $conf, $template;
load_language('plugin.lang', F2B_PATH);
$conf['Front2Back'] = explode(',', $conf['Front2Back']);

// Enregistrement de la configuration
if (isset($_POST['submit']))
{
	$conf['Front2Back'] = array(
		!empty($_POST['path']) ? rtrim($_POST['path'],'/').'/' : '',
		!empty($_POST['parent']) ? rtrim($_POST['parent'],'/').'/' : '',
		!empty($_POST['hd_parent']) ? rtrim($_POST['hd_parent'],'/').'/' : '',
		!empty($_POST['suffix']) ? $_POST['suffix'] : '',
	);
			
    $query = 'UPDATE ' . CONFIG_TABLE . '
		SET value="' . implode (',', $conf['Front2Back']) . '"
		WHERE param="Front2Back"';
    pwg_query($query);
	
	array_push($page['infos'], l10n('Information data registered in database'));
}

$template->assign(array(
	'PATH' => $conf['Front2Back'][0],
	'PARENT' => $conf['Front2Back'][1],
	'HD_PARENT' => $conf['Front2Back'][2],
	'SUFFIX' => $conf['Front2Back'][3],
));
	
$template->set_filename('Front2Back_conf', dirname(__FILE__).'/template/admin.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'Front2Back_conf');

?>