<?php 
/*
Plugin Name: Back2Front
Version: auto
Description: Add a link on picture's page to show a alternative version of the pic (for postcards for example)
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=533
Author: Mistic
Author URI: http://www.strangeplanet.fr
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
global $prefixeTable;

define('B2F_DIR', basename(dirname(__FILE__)));
define('B2F_PATH', PHPWG_PLUGINS_PATH . B2F_DIR . '/');
define('B2F_TABLE', $prefixeTable . 'image_verso');

load_language('plugin.lang', B2F_PATH);
include_once(B2F_PATH . 'Back2Front.php');

if (script_basename() == 'picture')
{
  add_event_handler('render_element_content', 'Back2Front_picture_content', EVENT_HANDLER_PRIORITY_NEUTRAL+20, 2);
}

if (script_basename() == 'index')
{
  add_event_handler('loc_end_index_thumbnails', 'Back2Front_thumbnails');
}

if (script_basename() == 'admin')
{
  add_event_handler('loc_begin_admin_page', 'Back2Front_picture_modify');
  
  add_event_handler('get_admin_plugin_menu_links', 'Back2Front_admin_menu');
  function Back2Front_admin_menu($menu) 
  {
    array_push($menu, array(
      'NAME' => 'Back2Front',
      'URL' => get_root_url().'admin.php?page=plugin-' . B2F_DIR));
    return $menu;
  }
}

?>