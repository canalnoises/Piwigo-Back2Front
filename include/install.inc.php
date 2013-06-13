<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

include_once(PHPWG_PLUGINS_PATH . B2F_ID . '/include/functions.inc.php');

function back2front_install() 
{
  global $conf, $prefixeTable;
  
  // configuration
  if (empty($conf['back2front']))
  {
    // create virtual private cat for storage
    $versos_cat = create_virtual_category('Back2Front private album');
    $versos_cat = array(
      'id' => $versos_cat['id'],
      'comment' => 'Used by Back2Front to store backsides.',
      'status'  => 'private',
      'visible' => 'false',
      'commentable' => 'false',
      );
    
    mass_updates(
      CATEGORIES_TABLE,
      array(
        'primary' => array('id'),
        'update' => array_diff(array_keys($versos_cat), array('id'))
        ),
      array($versos_cat)
      );
    
    $Back2Front_default_config = array(
      'versos_cat' => $versos_cat['id'],
      'switch_mode' => 'click',
      'transition' => 'none',
      'position' => 'top',
      'link_name' => array('default'=>null),
      'show_thumbnail' => true,
      );
      
    $conf['back2front'] = serialize($Back2Front_default_config);
    
    conf_update_param('back2front', $conf['back2front']);
  }
  else
  {
    if (is_string($conf['back2front']))
    {
      if (($old_conf = @unserialize($conf['back2front'])) === false)
      {
        $old_conf = explode(',', $conf['back2front']);
      }
    }
    else
    {
      $old_conf = $conf['back2front'];
    }
    
    // convert old comma separated conf
    if (isset($old_conf[0]))
    {
      $new_conf = array(
        'versos_cat' => $old_conf[0],
        'switch_mode' => $old_conf[1],
        'transition' => $old_conf[2],
        'position' => $old_conf[3],
        'link_name' => unserialize($old_conf[4]),
        'show_thumbnail' => @$old_conf[5],
        );
    
      $conf['back2front'] = serialize($new_conf);
      conf_update_param('back2front', $conf['back2front']);
    }
  }
  
  // create tables
  $query = '
CREATE TABLE IF NOT EXISTS `' . $prefixeTable . 'image_verso` (
  `image_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `verso_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `categories` varchar(128) NULL,
  PRIMARY KEY (`image_id`),
  UNIQUE KEY (`verso_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
';
  pwg_query($query);
}

?>