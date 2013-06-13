<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

/*
 * restore verso to its original categories
 * criterias :
 *  - verso € 'versos' cat only => restore verso to original categories
 *  - otherwise nothing is changed
 *  
 * $item = array('verso_id', 'categories');
*/
function back2front_restaure_categories($item)
{
  global $conf;
  
  /* catch current verso categories */
  $query = 'SELECT DISTINCT category_id FROM '.IMAGE_CATEGORY_TABLE.' WHERE image_id = '.$item['verso_id'].';';
  $item['current_verso_cats'] = array_from_query($query, 'category_id');

  /* if verso € 'versos' cat only */
  if (count($item['current_verso_cats']) == 1 && $item['current_verso_cats'][0] == $conf['back2front']['versos_cat'])
  {
    foreach (explode(',',$item['categories']) as $cat)
    {
      $datas[] = array(
        'image_id' => $item['verso_id'],
        'category_id' => $cat,
        );
    }
  }

  if (isset($datas))
  {
    mass_inserts(
      IMAGE_CATEGORY_TABLE,
      array('image_id', 'category_id'),
      $datas
      );
  }
  
  $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id = '.$item['verso_id'].' 
    AND category_id = '.$conf['back2front']['versos_cat'].'
;';
  pwg_query($query);
}

function back2front_check_storage()
{
  global $conf;
  
  if ($conf['back2front']['versos_cat'] != 0)
  {
    $query = '
SELECT COUNT(*) FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$conf['back2front']['versos_cat'].'
    AND name = "Back2Front private album"
;';
    $result = pwg_query($query);
    
    if (pwg_db_num_rows($result))
    {
      return;
    }
  }
  
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
    
  $conf['back2front']['versos_cat'] = $versos_cat['id'];
  conf_update_param('back2front', serialize($conf['back2front']));
}

function picture_exists($id)
{
  if (!preg_match('#([0-9]{1,})#', $id) || $id == '0') return false;
  
  $query = "SELECT id FROM ".IMAGES_TABLE." WHERE id = ".$id.";";
  $result = pwg_query($query);
  
  if (pwg_db_num_rows($result)) return true;
  else return false;
}

if (!function_exists('stripslashes_deep'))
{
  function stripslashes_deep($value)
  {
    return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
  }
}

?>