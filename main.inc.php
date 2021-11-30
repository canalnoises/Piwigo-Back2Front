<?php 
/*
Plugin Name: Back2Front
Version: auto
Description: Add a link on picture's page to show a alternative version of the pic (for postcards for example)
Plugin URI: auto
Author: Mistic
Author URI: http://www.strangeplanet.fr
Has Settings: true
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

global $prefixeTable;

define('B2F_ID',    basename(dirname(__FILE__)));
define('B2F_PATH',  PHPWG_PLUGINS_PATH . B2F_ID . '/');
define('B2F_TABLE', $prefixeTable . 'image_verso');
define('B2F_ADMIN', get_root_url() . 'admin.php?page=plugin-' . B2F_ID);

include_once(B2F_PATH . 'include/Back2Front.php');


add_event_handler('init', 'back2front_init');


function back2front_init()
{
  global $conf;
  
  $conf['back2front'] = safe_unserialize($conf['back2front']);

  load_language('plugin.lang', B2F_PATH);
}


if (script_basename() == 'picture')
{
  add_event_handler('render_element_content', 'back2front_picture_content', EVENT_HANDLER_PRIORITY_NEUTRAL+20, 2);
}

if (script_basename() == 'index')
{
  add_event_handler('loc_end_index_thumbnails', 'back2front_thumbnails');
}

if (script_basename() == 'admin')
{
  add_event_handler('loc_begin_admin_page', 'back2front_picture_modify');
  
  add_event_handler('get_admin_plugin_menu_links', 'back2front_admin_menu');
  function back2front_admin_menu($menu) 
  {
    array_push($menu, array(
      'NAME' => 'Back2Front',
      'URL' => B2F_ADMIN,
    ));
    return $menu;
  }
}
