<?php 
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

include_once(B2F_PATH.'functions.inc.php');

/*
 * Add verso link on picture page
 */
function Back2Front_picture_content($content, $image)
 {
  global $template, $user, $conf;

  /* search for a verso picture */
  $query = "
    SELECT 
      i.id, 
      i.path,
      i.has_high,
      i.width,
      i.height
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
    $conf['back2front'] = explode(',',$conf['back2front']);
    
    // calculation of width and height
    include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
    
    if (!empty($verso['width']))
    {
      list(
        $verso['scaled_width'],
        $verso['scaled_height']
        ) = get_picture_size(
          $verso['width'],
          $verso['height'],
          @$user['maxwidth'],
          @$user['maxheight']
        );
    }

    /* websize picture */
    $template->assign(array(
      'VERSO_URL' => $verso['path'],
      'VERSO_WIDTH' => $verso['scaled_width'],
      'VERSO_HEIGHT' => $verso['scaled_height'],
      'b2f_switch_mode' => $conf['back2front'][1],
      'b2f_transition' => $conf['back2front'][2],
    ));
    
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
    
    return $template->parse('B2F_picture_content', true).$content;  
  }
  else 
  {
    return $content;
  }
}


/*
 * Add field on picture modify page
 */
function Back2Front_picture_modify($menu)
{
  global $page, $template, $conf;
  
  if ($page['page'] != 'picture_modify') return $menu;
  $conf['back2front'] = explode(',',$conf['back2front']);
  
/* SAVE VALUES */
  if (isset($_POST['b2f_submit']))
  {
    /* catch all verso and recto ids and original categories */
    $query = "SELECT image_id, verso_id, categories
      FROM ".B2F_TABLE.";";
    $rectos = array_from_query($query, 'image_id');
    $versos = array_from_query($query, 'verso_id');
    $cats = array_from_query($query, 'categories');
    
    if (count($rectos) != 0)
    {
      $all_recto_verso = array_combine($rectos, $versos);
      $verso_cats = array_combine($versos, $cats);
    }
    else
    {
      $all_recto_verso = array(0=>0);
      $verso_cats = array(0=>NULL);
    }
    unset($rectos, $versos, $cats);
    
    /* picture is verso */
    if (isset($_POST['b2f_is_verso']))
    {      
      /* verso don't exists */
      if (!picture_exists($_POST['b2f_front_id']))
      {
        array_push($page['errors'], l10n_args(get_l10n_args('Unknown id %d for frontside picture', $_POST['b2f_front_id'])));
      }
      /* verso same as recto  */
      else if ($_POST['b2f_front_id'] == $_GET['image_id'])
      {
        array_push($page['errors'], l10n('Backside and frontside can\'t be the same picture'));
      }
      /* recto has already a verso */
      else if (in_array($_POST['b2f_front_id'], array_keys($all_recto_verso)) AND $all_recto_verso[$_POST['b2f_front_id']] != $_GET['image_id'])
      {
          $recto_current_verso['id'] = $all_recto_verso[$_POST['b2f_front_id']];
          $recto_current_verso['link'] = get_root_url().'admin.php?page=picture_modify&amp;image_id='.$recto_current_verso['id'];
          array_push($page['errors'], 
            l10n_args(get_l10n_args('The picture n°%d has already a backside : %s', 
              array($_POST['b2f_front_id'], '<a href="'.$recto_current_verso['link'].'">'.$recto_current_verso['id'].'</a>')
            ))
          );
      }
      /* recto is already a verso */
      else if (in_array($_POST['b2f_front_id'], array_values($all_recto_verso)))
      {
          $recto_is_verso['id'] = $_POST['b2f_front_id'];
          $recto_is_verso['link'] = get_root_url().'admin.php?page=picture_modify&amp;image_id='.$recto_is_verso['id'];
          array_push($page['errors'], l10n_args(get_l10n_args('The picture n°%s is already a backside', '<a href="'.$recto_is_verso['link'].'">'.$recto_is_verso['id'].'</a>')));
      }
      /* everything is fine */
      else
      {
        // move the verso - if first save
        if (isset($_POST['b2f_move_verso']) AND (!array_key_exists($_GET['image_id'], $verso_cats) OR $verso_cats[$_GET['image_id']] == NULL))
        {
          // get current categories
          $query = "SELECT category_id FROM ".IMAGE_CATEGORY_TABLE." WHERE image_id = ".$_GET['image_id'].";";
          $verso_categories = array_from_query($query, 'category_id');
          
          pwg_query("DELETE FROM ".IMAGE_CATEGORY_TABLE."
            WHERE image_id = ".$_GET['image_id'].";");
          pwg_query("INSERT INTO ".IMAGE_CATEGORY_TABLE."(image_id, category_id)
            VALUES(".$_GET['image_id'].", ".$conf['back2front'][0].");");
            
          // random representant for each categories
          set_random_representant($verso_categories);
          
          $verso_categories = isset($verso_cats[$_GET['image_id']]) ? $verso_cats[$_GET['image_id']] : implode(',',$verso_categories);
          $template->assign('B2F_MOVE_VERSO', 'checked="checked"');
        }
        // restore the verso - if precedently moved
        else if (!isset($_POST['b2f_move_verso']) AND array_key_exists($_GET['image_id'], $verso_cats) AND $verso_cats[$_GET['image_id']] != NULL)
        {
          $item['verso_id'] = $_GET['image_id'];
          $item['categories'] = $verso_cats[$_GET['image_id']];
          back2front_restaure_categories($item);
          
          $verso_categories = 'NULL';
          $template->assign('B2F_MOVE_VERSO', '');
        }
        // leave the verso
        else
        {
          $verso_categories = isset($verso_cats[$_GET['image_id']]) ? $verso_cats[$_GET['image_id']] : 'NULL';
          $template->assign('B2F_MOVE_VERSO', isset($verso_cats[$_GET['image_id']]) ? 'checked="checked"' : '');
        }
        
        // insert or update verso associations
        pwg_query("INSERT INTO ".B2F_TABLE."
          VALUES(".$_POST['b2f_front_id'].", ".$_GET['image_id'].", '".$verso_categories."')
          ON DUPLICATE KEY UPDATE image_id = ".$_POST['b2f_front_id'].", categories = ".$verso_categories.";");
      
        $template->assign(array(
          'B2F_IS_VERSO' => 'checked="checked"',
          'B2F_FRONT_ID' => $_POST['b2f_front_id'],
        ));
        
        $verso['id'] = $_POST['b2f_front_id'];
        $verso['link'] = get_root_url().'admin.php?page=picture_modify&amp;image_id='.$verso['id'];
        array_push($page['infos'], l10n_args(get_l10n_args('This picture is now the backside of the picture n°%s', '<a href="'.$verso['link'].'">'.$verso['id'].'</a>')));
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
        $item['verso_id'] = $_GET['image_id'];
        list($item['categories']) = pwg_db_fetch_row($result);
        back2front_restaure_categories($item);
        
        pwg_query("DELETE FROM ".B2F_TABLE." 
          WHERE verso_id = ".$_GET['image_id'].";");
          
        array_push($page['infos'], l10n('This picture is no longer a backside'));
      }
    }
  }
  
/* GET SAVED VALUES */
  if ($template->get_template_vars('B2F_IS_VERSO') == null)
  {
    $template->assign('B2F_MOVE_VERSO', 'checked="checked"');
    
    /* is the picture a verso ? */
    $query = "
      SELECT image_id, categories
      FROM ".B2F_TABLE."
      WHERE verso_id = ".$_GET['image_id']."
    ;";
    $result = pwg_query($query);
    
    if (pwg_db_num_rows($result))
    {
      list($recto_id, $cats) = pwg_db_fetch_row($result);
      $template->assign(array(
        'B2F_IS_VERSO' => 'checked="checked"',
        'B2F_FRONT_ID' => $recto_id,
        'B2F_MOVE_VERSO' => $cats != NULL ? 'checked="checked"' : '',
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
        $item = pwg_db_fetch_assoc($result);

        $template->assign(array(
          'B2F_VERSO_ID' => $item['verso_id'],
          'B2F_VERSO_URL' => get_root_url().'admin.php?page=picture_modify&amp;image_id='.$item['verso_id'],
        ));
      }
    }
  }
  
  $template->set_prefilter('picture_modify', 'Back2front_picture_modify_prefilter');
  
  return $menu;
}


function Back2front_picture_modify_prefilter($content, &$smarty)
{
  $search = '<form id="associations" method="post" action="{$F_ACTION}#associations">';
  $replacement = file_get_contents(B2F_PATH.'template/picture_modify.tpl')."\n".$search;
  return str_replace($search, $replacement, $content);
}

?>