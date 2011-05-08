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

add_event_handler('render_element_content', 'Back2Front_picture_content', 99, 2);
//add_event_handler('loc_end_section_init', 'Back2Front_items');
add_event_handler('loc_end_admin', 'Back2Front_picture_modify');


/* 	add_event_handler('get_admin_plugin_menu_links', 'Front2Back_admin_menu');
	function Front2Back_admin_menu($menu) 
	{
		array_push($menu, array(
			'NAME' => 'Front2Back',
			'URL' => get_root_url().'admin.php?page=plugin-' . B2F_DIR));
		return $menu;
	} */

?>
