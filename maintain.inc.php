<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

defined('B2F_ID') or define('B2F_ID', basename(dirname(__FILE__)));
include_once(PHPWG_PLUGINS_PATH . B2F_ID . '/include/install.inc.php');
include_once(PHPWG_PLUGINS_PATH . B2F_ID . '/include/functions.inc.php');

function plugin_install() 
{
  back2front_install();
  
  define('back2front_installed', true);
}

function plugin_activate()
{
  if (!defined('back2front_installed'))
  {
    back2front_install();
  }
}


function plugin_uninstall()
{
  global $conf, $prefixeTable;
  
  $conf['back2front'] = unserialize($conf['back2front']);
  
  $query = "SELECT * FROM `" . $prefixeTable . "image_verso`;";
  $images_versos = pwg_query($query);
  
  while ($item = pwg_db_fetch_assoc($images_versos))
  {
    back2front_restaure_categories($item);
  }

  pwg_query("DROP TABLE `" . $prefixeTable . "image_verso`;");
  pwg_query("DELETE FROM `" . CONFIG_TABLE . "` WHERE param = 'back2front';");
  pwg_query("DELETE FROM `" . CATEGORIES_TABLE ."`WHERE id = ".$conf['back2front']['versos_cat'].";");
  
  // rebuild categories cache
  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  invalidate_user_cache(true);
}
?>