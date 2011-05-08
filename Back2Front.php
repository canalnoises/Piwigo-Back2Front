<?php 
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

/*
 * Add verso link on picture page
 */
function Back2Front_picture_content($content, $image)
 {
  global $template, $conf;

  /* search for a verso picture */
  $query = "
    SELECT 
      i.id, 
      i.path,
      i.has_high
    FROM ".IMAGES_TABLE." as i
      INNER JOIN ".B2F_TABLE." as v
      ON i.id = v.verso_id
    WHERE
      v.image_id = ".$image['id']."
  ;";
  $result = pwg_query($query);

  if (pwg_db_num_rows($result)) 
  {
    $verso = pwg_db_fetch_assoc($result);

    /* websize picture */
    $template->assign('VERSO_URL', $verso['path']);
    
    /* admin link */
    if (is_admin())
    {
      $template->assign('VERSO_U_ADMIN', get_root_url().'admin.php?page=picture_modify&amp;cat_id=&amp;image_id='.$verso['id']);
      $template->set_filename('B2F_admin_button', dirname(__FILE__).'/template/admin_button.tpl');
      $template->concat('PLUGIN_PICTURE_ACTIONS', $template->parse('B2F_admin_button', true));
    }

    /* high picture */
    if ($verso['has_high'])
    {
      $template->assign('VERSO_HD', get_high_url($verso));
    }

    /* template & output */
    $template->set_filenames(array('B2F_picture_content' => dirname(__FILE__).'/template/picture_content.tpl') );
    $template->assign('B2F_PATH', B2F_PATH);
    
    return $content . $template->parse('B2F_picture_content', true);  
  }
  else 
  {
    return $content;
  }
}


/*
 * Add field on picture modify page
 */
function Back2Front_picture_modify()
{
  global $page, $template;
  
  if ($page['page'] == 'picture_modify')
  {
    /* change values */
    if (isset($_POST['b2f_submit']))
    {
      /* picture is verso */
      if (isset($_POST['b2f_is_verso']))
      {
        /* frontside exists */
        if (picture_exists($_POST['b2f_front_id']))
        {
          $query = "
            INSERT INTO ".B2F_TABLE."
            VALUES(".$_POST['b2f_front_id'].", ".$_GET['image_id'].", ".$_POST['b2f_old_level'].")
            ON DUPLICATE KEY UPDATE image_id = ".$_POST['b2f_front_id'].", old_level = ".$_POST['b2f_old_level']."
          ;";
          pwg_query($query);
          
          $query = "
            UPDATE ".IMAGES_TABLE."
            SET level = 99
            WHERE id = ".$_GET['image_id']."
          ;";
          pwg_query($query);
          
          $template->assign(array(
            'B2F_IS_VERSO' => 'checked="checked"',
            'B2F_FRONT_ID' => $_POST['b2f_front_id'],
            'B2F_OLD_LEVEL' => $_POST['b2f_old_level'],
          ));
        }
        else
        {
          $template->assign('errors', l10n('Unknown id for frontside picture'));
        }
      }
      /* picture isn't verso */
      else
      {
        $query = "
          DELETE FROM ".B2F_TABLE." 
          WHERE verso_id = ".$_GET['image_id']."
        ;";
        pwg_query($query);
        
        $query = "
          UPDATE ".IMAGES_TABLE."
          SET level = ".$_POST['b2f_old_level']."
          WHERE id = ".$_GET['image_id']."
        ;";
        pwg_query($query);
        
        $template->assign(array(
          'level_options_selected' => array($_POST['b2f_old_level']),
        ));
      }
    }
    /* get saved values */
    else
    {
      /* is the pisture a verso ? */
      $query = "
        SELECT image_id, old_level
        FROM ".B2F_TABLE."
        WHERE verso_id = ".$_GET['image_id']."
      ;";
      $result = pwg_query($query);
      
      if (pwg_db_num_rows($result))
      {
        $item = pwg_db_fetch_assoc($result);
        $template->assign(array(
          'B2F_IS_VERSO' => 'checked="checked"',
          'B2F_FRONT_ID' => $item['image_id'],
          'B2F_OLD_LEVEL' => $item['old_level'],
        ));
      }
      /* is the picture a front ? */
      else
      {
        $query = "
          SELECT verso_id
          FROM ".B2F_TABLE."
          WHERE image_id = ".$_GET['image_id']."
        ;";
        $result = pwg_query($query);
        
        if (pwg_db_num_rows($result))
        {
          include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
          
          $item = pwg_db_fetch_assoc($result);
          $query = "
            SELECT id, name, file
            FROM ".IMAGES_TABLE."
            WHERE id = ".$item['verso_id']."
          ;";
          $item = pwg_db_fetch_assoc(pwg_query($query));
          
          $template->assign(array(
            'B2F_VERSO_ID' => $item['id'],
            'B2F_VERSO_URL' => get_root_url().'admin.php?page=picture_modify&amp;cat_id=&amp;image_id='.$item['id'],
            'B2F_VERSO_NAME' => get_image_name($item['name'], $item['file']),
          ));
        }
      }
    }
    
    $template->set_filename('B2F_picture_modify', dirname(__FILE__).'/template/picture_modify.tpl');
    $template->concat('ADMIN_CONTENT', $template->parse('B2F_picture_modify', true));
  }
}

function picture_exists($id)
{
  if (!preg_match('#([0-9]{1,})#', $id) OR $id == '0') return false;
  
  $query = "SELECT id FROM ".IMAGES_TABLE." WHERE id = ".$id.";";
  $result = pwg_query($query);
  
  if (pwg_db_num_rows($result)) return true;
  else return false;
}


$versos = null; // needs to be declared outside any function for the array_filter callback ?!
/*
 * Change/remove navigation thumbnails
 */
function Back2Front_items()
{
  global $template, $page, $versos;
  
  /* search all verso ids */
  $query = "
    SELECT verso_id as id
    FROM ".B2F_TABLE."
  ;";
  $versos = array_values(array_from_query($query, 'id'));
  
  /* output */
  function remove_versos($item)
  {
    global $versos;
    return !in_array($item, $versos);
  }
  $page['items'] = array_values(array_filter($page['items'], 'remove_versos'));
}

?>