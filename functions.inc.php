<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

/* $item = array('verso_id', 'categories'); */
function back2front_restaure_categories($item)
{
  global $conf;
        
  /* catch current verso categories */
  $versos_infos = pwg_query("SELECT category_id FROM ".IMAGE_CATEGORY_TABLE." WHERE image_id = ".$item['verso_id'].";");
  $item['current_verso_cats'] = array();
  while (list($verso_cat) = pwg_db_fetch_row($versos_infos))
  {
    $item['current_verso_cats'][] = $verso_cat;
  }

  /* if verso € 'versos' cat only */
  if (count($item['current_verso_cats']) == 1 AND $item['current_verso_cats'][0] == $conf['back2front'][0])
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
  
  pwg_query("DELETE FROM ".IMAGE_CATEGORY_TABLE."
    WHERE image_id = ".$item['verso_id']." AND category_id = ".$conf['back2front'][0].";");
}

function picture_exists($id)
{
  if (!preg_match('#([0-9]{1,})#', $id) OR $id == '0') return false;
  
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