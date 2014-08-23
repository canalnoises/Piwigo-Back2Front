<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class Back2Front_maintain extends PluginMaintain
{
  private $table;
  
  private $default_conf = array(
    'versos_cat' => 0,
    'switch_mode' => 'click',
    'transition' => 'none',
    'position' => 'top',
    'link_name' => array('default'=>null),
    'show_thumbnail' => true,
    );
  
  function __construct($id)
  {
    global $prefixeTable;
    
    parent::__construct($id);
    $this->table = $prefixeTable . 'image_verso';
  }

  function install($plugin_version, &$errors=array())
  {
    global $conf;

    if (empty($conf['back2front']))
    {
      // create virtual private cat for storage
      include_once(PHPWG_ROOT_PATH . 'admin/include/functions.inc.php');
      $cat = create_virtual_category('Back2Front private album');
      $info = array(
        'comment' => 'Used by Back2Front to store backsides.',
        'status'  => 'private',
        'visible' => 'false',
        'commentable' => 'false',
        );
      
      single_update(
        CATEGORIES_TABLE,
        $info,
        array('id' => $cat['id'])
        );
      
      $this->default_conf['versos_cat'] = $cat['id'];
      
      conf_update_param('back2front', $this->default_conf, true);
    }
    
    // create tables
    $query = '
CREATE TABLE IF NOT EXISTS `' . $this->table . '` (
  `image_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `verso_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `categories` varchar(128) NULL,
  PRIMARY KEY (`image_id`),
  UNIQUE KEY (`verso_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;';
    pwg_query($query);
  }

  function update($old_version, $new_version, &$errors=array())
  {
    $this->install($new_version, $errors);
  }

  function uninstall()
  {
    global $conf;
    
    include_once(PHPWG_PLUGINS_PATH . 'Back2Front/include/functions.inc.php');
    $conf['back2front'] = safe_unserialize($conf['back2front']);
    
    $images_versos = pwg_query("SELECT * FROM `" . $this->table . "`;");
    while ($item = pwg_db_fetch_assoc($images_versos))
    {
      back2front_restaure_categories($item);
    }

    pwg_query("DROP TABLE `" . $this->table . "`;");
    pwg_query("DELETE FROM `" . CATEGORIES_TABLE ."`WHERE id = ".$conf['back2front']['versos_cat'].";");
    
    // rebuild categories cache
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    invalidate_user_cache(true);
  
    conf_delete_param('back2front');
  }
}
