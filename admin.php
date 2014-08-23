<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

global $conf, $template;

include_once(B2F_PATH . 'include/functions.inc.php');

// Enregistrement de la configuration
if (isset($_POST['submit']))
{
  $conf['back2front'] = array(
    'versos_cat' => $conf['back2front']['versos_cat'],
    'switch_mode' => $_POST['switch_mode'],
    'transition' => $_POST['transition'],
    'position' => $_POST['position'],
    'link_name' => stripslashes_deep($_POST['link_name']),
    'show_thumbnail' => isset($_POST['show_thumbnail']),
    );
  
  conf_update_param('back2front', $conf['back2front']);
  $page['infos'][] = l10n('Information data registered in database');
}

// Gestion des langues pour le bloc menu
$template->append('link_name', array(
  'LANGUAGE_NAME' => l10n('Default'),
  'LANGUAGE_CODE' => 'default',
  'VALUE' => @$conf['back2front']['link_name']['default'],
  ));
  
foreach (get_languages() as $language_code => $language_name)
{
  $template->append('link_name', array(
    'LANGUAGE_NAME' => $language_name,
    'LANGUAGE_CODE' => $language_code,
    'VALUE' => @$conf['back2front']['link_name'][$language_code],
    ));
}

$template->assign(array(
  'B2F_PATH' => B2F_PATH,
  'SWITCH_MODE' => $conf['back2front']['switch_mode'],
  'TRANSITION' => $conf['back2front']['transition'],
  'POSITION' => $conf['back2front']['position'],
  'SHOW_THUMBNAIL' => $conf['back2front']['show_thumbnail'],
  ));
  
$template->set_filename('back2front_conf', realpath(B2F_PATH.'template/admin.tpl'));
$template->assign_var_from_handle('ADMIN_CONTENT', 'back2front_conf');
