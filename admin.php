<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $conf, $template;
load_language('plugin.lang', B2F_PATH);
$conf['back2front'] = explode(',', $conf['back2front']);

include_once(B2F_PATH.'functions.inc.php');

// Enregistrement de la configuration
if (isset($_POST['submit']))
{
  $conf['back2front'] = array(
    $conf['back2front'][0], 
    $_POST['switch_mode'], 
    $_POST['transition'],
    $_POST['position'],
    serialize(stripslashes_deep(str_replace(array("'",'"',','), null, $_POST['link_name']))),
    isset($_POST['show_thumbnail']),
  );
  
  conf_update_param('back2front', implode (',', $conf['back2front']));
  array_push($page['infos'], l10n('Information data registered in database'));
}

// Gestion des langues pour le bloc menu
$conf['back2front'][4] = unserialize($conf['back2front'][4]);
$template->append('link_name', array(
  'LANGUAGE_NAME' => l10n('Default'),
  'LANGUAGE_CODE' => 'default',
  'VALUE' => @$conf['back2front'][4]['default'],
  )
);
foreach (get_languages() as $language_code => $language_name)
{
  $template->append('link_name', array(
    'LANGUAGE_NAME' => $language_name,
    'LANGUAGE_CODE' => $language_code,
    'VALUE' => isset($conf['back2front'][4][$language_code]) ? $conf['back2front'][4][$language_code] : '',
    )
  );
}

$template->assign(array(
  'B2F_PATH' => B2F_PATH,
  'SWITCH_MODE' => $conf['back2front'][1],
  'TRANSITION' => $conf['back2front'][2],
  'POSITION' => $conf['back2front'][3],
  'SHOW_THUMBNAIL' => $conf['back2front'][5],
));
  
$template->set_filename('back2front_conf', dirname(__FILE__).'/template/admin.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'back2front_conf');

?>