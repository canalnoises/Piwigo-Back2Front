<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

function plugin_install() {
	global $prefixeTable;

	pwg_query("CREATE TABLE `" . $prefixeTable . "image_verso` (
    `image_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
    `verso_id` smallint(8) unsigned NOT NULL DEFAULT '0',
    `old_level` tinyint unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`image_id`,`verso_id`)
	) DEFAULT CHARSET=utf8;");
}

function plugin_activate(){
	global $conf, $prefixeTable;

}

function plugin_uninstall() {
	global $prefixeTable;

  $query = "SELECT * FROM `" . $prefixeTable . "image_verso`;";
  $result = pwg_query($query);
  while ($item = pwg_db_fetch_assoc($result))
  {
    pwg_query("UPDATE ".IMAGES_TABLE." SET level = ".$item['old_level']." WHERE id = ".$item['verso_id'].";");
  }
  
	pwg_query("DROP TABLE `" . $prefixeTable . "image_verso`;");
}
?>