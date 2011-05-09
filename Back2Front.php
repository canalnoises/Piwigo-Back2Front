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
      $template->assign('VERSO_U_ADMIN', get_root_url().'admin.php?page=picture_modify&amp;image_id='.$verso['id']);
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
  global $page, $template, $conf;
  $conf['back2front'] = explode(',',$conf['back2front']);
  
  if ($page['page'] != 'picture_modify')
  {
    return;
  }
  
/* SAVE VALUES */
  if (isset($_POST['b2f_submit']))
  {
    /* picture is verso */
    if (isset($_POST['b2f_is_verso']))
    {
      /* catch all verso and recto ids */
      $query = "SELECT image_id, verso_id
        FROM ".B2F_TABLE.";";
      $rectos = array_from_query($query, 'image_id');
      $versos = array_from_query($query, 'verso_id');
      if (count($rectos) != 0)
      {
        $all_recto_verso = array_combine($rectos, $versos);
      }
      else
      {
        $all_recto_verso = array(0=>0);
      }
      unset($rectos, $versos);
      
      /* verso don't exists */
      if (!picture_exists($_POST['b2f_front_id']))
      {
        $template->append('errors', l10n('Unknown id for frontside picture : ').$_POST['b2f_front_id']);
      }
      /* verso same as recto  */
      else if ($_POST['b2f_front_id'] == $_GET['image_id'])
      {
        $template->append('errors', l10n('Backside and frontside can\'t be the same picture'));
      }
      /* recto has already a verso */
      else if (in_array($_POST['b2f_front_id'], array_keys($all_recto_verso)))
      {
          $recto_current_verso['id'] = $all_recto_verso[$_POST['b2f_front_id']];
          $recto_current_verso['link'] = get_root_url().'admin.php?page=picture_modify&amp;image_id='.$recto_current_verso['id'];
          $template->append('errors', l10n('This picture has already a backside : ').'<a href="'.$recto_current_verso['link'].'">'.$recto_current_verso['id'].'</a>');
      }
      /* recto is already a verso */
      else if (in_array($_POST['b2f_front_id'], array_values($all_recto_verso)))
      {
          $recto_is_verso['id'] = $_POST['b2f_front_id'];
          $recto_is_verso['link'] = get_root_url().'admin.php?page=picture_modify&amp;image_id='.$recto_is_verso['id'];
          $template->append('errors', l10n('This picture is already a backside : ').'<a href="'.$recto_is_verso['link'].'">'.$recto_is_verso['id'].'</a>');
      }
      /* everything is fine */
      else
      {
        // get current categories
        $query = "SELECT category_id FROM ".IMAGE_CATEGORY_TABLE." WHERE image_id = ".$_GET['image_id'].";";
        $verso_categories = array_from_query($query, 'category_id');
        
        // insert or update verso associations
        pwg_query("INSERT INTO ".B2F_TABLE."
          VALUES(".$_POST['b2f_front_id'].", ".$_GET['image_id'].", '".implode(',',$verso_categories)."')
          ON DUPLICATE KEY UPDATE image_id = ".$_POST['b2f_front_id'].";");
        
        // move the verso ?
        if (isset($_POST['b2f_move_verso']))
        {
          pwg_query("DELETE FROM ".IMAGE_CATEGORY_TABLE."
            WHERE image_id = ".$_GET['image_id'].";");
            
          pwg_query("INSERT INTO ".IMAGE_CATEGORY_TABLE."
            VALUES(".$_GET['image_id'].", ".$conf['back2front'][0].", NULL);");
            
          // random representant for each categories
          set_random_representant($verso_categories);
        }
      
        $template->assign(array(
          'B2F_IS_VERSO' => 'checked="checked"',
          'B2F_FRONT_ID' => $_POST['b2f_front_id'],
        ));
        
        $template->append('infos', l10n('This picture is now the backside of the picture n° ').$_POST['b2f_front_id']);
      }
    }
    /* picture isn't verso */
    else
    {
      /* search if it was a verso */
      $query = "SELECT categories
        FROM ".B2F_TABLE."
        WHERE verso_id = ".$_GET['image_id'].";";
      $result = pwg_query($query);
      
      /* it must be restored to its original categories (see criteria on maintain.inc) */
      if (pwg_db_num_rows($result))
      {
        // original categories
        list($item['categories']) = pwg_db_fetch_row($result);
        // catch current categories
        $versos_infos = pwg_query("SELECT category_id FROM ".IMAGE_CATEGORY_TABLE." WHERE image_id = ".$_GET['image_id'].";");
        while (list($verso_cat) = pwg_db_fetch_row($versos_infos))
        {
          $current_verso_cats[] = $verso_cat;
        }
        
        /* if verso € 'versos' cat only */
        if (count($current_verso_cats) == 1 AND $current_verso_cats[0] == $conf['back2front'][0])
        {
          foreach (explode(',',$item['categories']) as $cat)
          {
            $datas[] = array(
              'image_id' => $_GET['image_id'],
              'category_id' => $cat,
              );
          }
          if (isset($datas))
          {
            mass_inserts(
              IMAGE_CATEGORY_TABLE,
              array('image_id', 'category_id'),
              $datas
              );
          }
        }
        
        pwg_query("DELETE FROM ".IMAGE_CATEGORY_TABLE."
          WHERE image_id = ".$_GET['image_id']." AND category_id = ".$conf['back2front'][0].";");
        
        pwg_query("DELETE FROM ".B2F_TABLE." 
          WHERE verso_id = ".$_GET['image_id'].";");
          
        $template->append('infos', l10n('This picture is no longer a backside'));
      }
    }
  }
  
/* GET SAVED VALUES */
  if ($template->get_template_vars('B2F_IS_VERSO') == null)
  {
    /* is the picture a verso ? */
    $query = "
      SELECT image_id
      FROM ".B2F_TABLE."
      WHERE verso_id = ".$_GET['image_id']."
    ;";
    $result = pwg_query($query);
    
    if (pwg_db_num_rows($result))
    {
      list($recto_id) = pwg_db_fetch_row($result);
      $template->assign(array(
        'B2F_IS_VERSO' => 'checked="checked"',
        'B2F_FRONT_ID' => $recto_id,
      ));
    }
    /* is the picture a front ? */
    else
    {
      $query = "SELECT verso_id
        FROM ".B2F_TABLE."
        WHERE image_id = ".$_GET['image_id'].";";
      $result = pwg_query($query);
      
      if (pwg_db_num_rows($result))
      {
        include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
        
        $item = pwg_db_fetch_assoc($result);
        $query = "SELECT id, name, file
          FROM ".IMAGES_TABLE."
          WHERE id = ".$item['verso_id'].";";
        $item = pwg_db_fetch_assoc(pwg_query($query));
        
        $template->assign(array(
          'B2F_VERSO_ID' => $item['id'],
          'B2F_VERSO_URL' => get_root_url().'admin.php?page=picture_modify&amp;image_id='.$item['id'],
          'B2F_VERSO_NAME' => get_image_name($item['name'], $item['file']),
        ));
      }
    }
  }
  
  $template->set_filename('B2F_picture_modify', dirname(__FILE__).'/template/picture_modify.tpl');
  $template->concat('ADMIN_CONTENT', $template->parse('B2F_picture_modify', true));
}

function picture_exists($id)
{
  if (!preg_match('#([0-9]{1,})#', $id) OR $id == '0') return false;
  
  $query = "SELECT id FROM ".IMAGES_TABLE." WHERE id = ".$id.";";
  $result = pwg_query($query);
  
  if (pwg_db_num_rows($result)) return true;
  else return false;
}

?>