<?php 
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

include_once(B2F_PATH.'include/functions.inc.php');

/*
 * Add the back (verso) link on image page.
 * Terminology note:
 * Recto: means the front of something, like the right page in a book, think recto, right side.
 * Verso: means the back of something, like a coin, or the left page in a book. think reVERSO, or vice versa.
 * It is a little counter-intuitive. In english, it's easy to think of the backside one when sees recto.
 */
function back2front_picture_content($content, $element_info)
{
  global $template, $user, $conf;

  /* search for a verso picture */
  $query = '
SELECT i.*
  FROM '.IMAGES_TABLE.' as i
    INNER JOIN '.B2F_TABLE.' as v
    ON i.id = v.verso_id
    AND v.image_id = '.$element_info['id'].'
;';
  $result = pwg_query($query);

  if (pwg_db_num_rows($result)) 
  {
    $verso = pwg_db_fetch_assoc($result);
    $deriv_type = pwg_get_session_var('picture_deriv', $conf['derivative_default_size']);
    
    $verso['src_image'] = new SrcImage($verso);
    $verso['derivatives'] = DerivativeImage::get_all($verso['src_image']);
    $verso['element_path'] = get_element_path($verso);
    $verso['selected_derivative'] = $verso['derivatives'][$deriv_type];
    
    /* websize picture */
    $template->assign(array(
      'B2F_PATH' => B2F_PATH,
      'verso' => $verso,
      ));
    
    /* admin link */
    if (is_admin())
    {
      $template->assign('VERSO_U_ADMIN', get_root_url().'admin.php?page=photo-'.$verso['id']);
      $template->set_filename('B2F_admin_button', realpath(B2F_PATH.'template/admin_button.tpl'));
      $template->concat('PLUGIN_PICTURE_ACTIONS', $template->parse('B2F_admin_button', true));
    }
    
    /* link name */
    if (!empty($conf['back2front']['link_name'][$user['language']]))
    {
      if (strpos($conf['back2front']['link_name'][$user['language']], '|') !== false)
      {
        $conf['back2front']['link_name'] = explode('|', $conf['back2front']['link_name'][$user['language']]);
      }
      else
      {
        $conf['back2front']['link_name'] = array_fill(0, 2, $conf['back2front']['link_name'][$user['language']]);
      }
    }
    else if (!empty($conf['back2front']['link_name']['default']))
    {
      if (strpos($conf['back2front']['link_name']['default'], '|') != false)
      {
        $conf['back2front']['link_name'] = explode('|', $conf['back2front']['link_name']['default']);
      }
      else
      {
        $conf['back2front']['link_name'] = array_fill(0, 2, $conf['back2front']['link_name']['default']);
      }
    }
    else
    {
      $conf['back2front']['link_name'] = array(l10n('See back'), l10n('See front'));
    }
    
    if ($conf['back2front']['transition'] == 'fade' && $conf['back2front']['position'] == 'bottom')
    {
      $conf['back2front']['position'] = 'top';
    }
    

    /* template & output */
    $template->set_filename('B2F_picture_content', realpath(B2F_PATH.'template/picture_content.tpl'));    
    $template->assign(array(
      'b2f_switch_mode' => $conf['back2front']['switch_mode'],
      'b2f_transition' => $conf['back2front']['transition'],
      'b2f_position' => $conf['back2front']['position'],
      'b2f_see_back' => $conf['back2front']['link_name'][0],
      'b2f_see_front' => $conf['back2front']['link_name'][1],
    ));
    
    switch ($conf['back2front']['position'])
    {
      case 'toolbar':
        $template->concat('PLUGIN_PICTURE_ACTIONS', $template->parse('B2F_picture_content', true));
        break;
      case 'top':
        $content = $template->parse('B2F_picture_content', true)."\n".$content;
        break;
      case 'bottom':
        $content = $content."\n".$template->parse('B2F_picture_content', true);
        break;
    }    
  }
  
  return $content;
}


/*
 * Add field on picture modify page
 */
function back2front_picture_modify()
{
  global $page, $template, $conf;
  
  if ($page['page'] != 'photo') return;
  if (isset($_GET['tab']) && $_GET['tab']!='properties') return;
  
/* SAVE VALUES */
  if (isset($_POST['b2f_submit']))
  {
    /* catch all back (verso) and front (recto) ids and original categories */
    $query = 'SELECT * FROM '.B2F_TABLE.';';
    $result = pwg_query($query);
    
    $rectos = $versos = $cats = array();
    while ($row = pwg_db_fetch_assoc($result))
    {
      $rectos[] = $row['image_id'];
      $versos[] = $row['verso_id'];
      $cats[] = $row['categories'];
    }
    
    if (count($rectos) != 0)
    {
      $all_recto_verso = array_combine($rectos, $versos);
      $verso_cats = array_combine($versos, $cats);
    }
    else
    {
      $all_recto_verso = array(0=>0);
      $verso_cats = array(0=>null);
    }
    unset($rectos, $versos, $cats);
    
    /* picture is a back (verso) */
    if (isset($_POST['b2f_is_verso']))
    {      
      /* back (verso) doesn't exists */
      if (!picture_exists($_POST['b2f_front_id']))
      {
        array_push($page['errors'], sprintf(
          l10n('Unknown id %d for frontside picture'), 
          $_POST['b2f_front_id']
          ));
      }
      /* back (verso) is same as front (recto)  */
      else if ($_POST['b2f_front_id'] == $_GET['image_id'])
      {
        array_push($page['errors'], l10n('Backside and frontside can\'t be the same picture'));
      }
      /* front (recto) already has a back (verso) */
      else if (in_array($_POST['b2f_front_id'], array_keys($all_recto_verso)) && $all_recto_verso[$_POST['b2f_front_id']] != $_GET['image_id'])
      {
        $recto_current_verso['id'] = $all_recto_verso[$_POST['b2f_front_id']];
        $recto_current_verso['link'] = get_root_url().'admin.php?page=photo-'.$recto_current_verso['id'];
        
        array_push($page['errors'], sprintf(
          l10n('The picture n°%d has already a backside : %s'), 
          $_POST['b2f_front_id'], 
          '<a href="'.$recto_current_verso['link'].'">'.$recto_current_verso['id'].'</a>'
          ));
      }
      /* front (recto) is already a back (verso) */
      else if (in_array($_POST['b2f_front_id'], array_values($all_recto_verso)))
      {
        $recto_is_verso['id'] = $_POST['b2f_front_id'];
        $recto_is_verso['link'] = get_root_url().'admin.php?page=picture_modify&amp;image_id='.$recto_is_verso['id'];
        
        array_push($page['errors'],  sprintf(
          l10n('The picture n°%s is already a backside'), 
          '<a href="'.$recto_is_verso['link'].'">'.$recto_is_verso['id'].'</a>'
          ));
      }
      /* everything is fine */
      else
      {
        // move the back (verso) - if first save

        if (isset($_POST['b2f_move_verso']) && (!array_key_exists($_GET['image_id'], $verso_cats) || $verso_cats[$_GET['image_id']] == null))
        {
          // get current categories
          $query = 'SELECT category_id FROM '.IMAGE_CATEGORY_TABLE.' WHERE image_id = '.$_GET['image_id'].';';
          $verso_categories = array_from_query($query, 'category_id');
          
          pwg_query('DELETE FROM '.IMAGE_CATEGORY_TABLE.' WHERE image_id = '.$_GET['image_id'].';');
          pwg_query('INSERT INTO '.IMAGE_CATEGORY_TABLE.'(image_id, category_id) VALUES('.$_GET['image_id'].', '.$conf['back2front']['versos_cat'].');');
            
          // random representant for each categories
          set_random_representant($verso_categories);
          
          $verso_categories = isset($verso_cats[$_GET['image_id']]) ? $verso_cats[$_GET['image_id']] : implode(',',$verso_categories);
          $template->assign('B2F_MOVE_VERSO', 'checked="checked"');
        }
        // restore the back (verso) - if precedently moved
        else if (!isset($_POST['b2f_move_verso']) && array_key_exists($_GET['image_id'], $verso_cats) && $verso_cats[$_GET['image_id']] != null)
        {
          $item['verso_id'] = $_GET['image_id'];
          $item['categories'] = $verso_cats[$_GET['image_id']];
          back2front_restaure_categories($item);
          
          $verso_categories = 'NULL';
          $template->assign('B2F_MOVE_VERSO', '');
        }
        // leave the back (verso)
        else
        {
          $verso_categories = isset($verso_cats[$_GET['image_id']]) ? $verso_cats[$_GET['image_id']] : 'NULL';
          $template->assign('B2F_MOVE_VERSO', isset($verso_cats[$_GET['image_id']]) ? 'checked="checked"' : '');
        }
        
        // insert or update back (verso) associations
        $query = '
INSERT INTO '.B2F_TABLE.'
  VALUES(
    '.$_POST['b2f_front_id'].',
    '.$_GET['image_id'].',
    "'.$verso_categories.'"
  )
  ON DUPLICATE KEY UPDATE
    image_id = '.$_POST['b2f_front_id'].',
    categories = "'.$verso_categories.'"
;';
        pwg_query($query);
        
        $template->assign(array(
          'B2F_IS_VERSO' => 'checked="checked"',
          'B2F_FRONT_ID' => $_POST['b2f_front_id'],
        ));
        
        $verso['id'] = $_POST['b2f_front_id'];
        $verso['link'] = get_root_url().'admin.php?page=photo-'.$verso['id'];
        
        array_push($page['infos'], sprintf(
          l10n('This picture is now the backside of the picture n°%s'),
          '<a href="'.$verso['link'].'">'.$verso['id'].'</a>'
          ));
      }
    }
    /* picture isn't back (verso) */
    else
    {
      /* search if it was a back (verso) */
      $query = '
SELECT categories
  FROM '.B2F_TABLE.'
  WHERE verso_id = '.$_GET['image_id'].'
;';
      $result = pwg_query($query);
      
      /* it must be restored to its original categories */
      if (pwg_db_num_rows($result))
      {
        $item['verso_id'] = $_GET['image_id'];
        list($item['categories']) = pwg_db_fetch_row($result);
        
        back2front_restaure_categories($item);
        pwg_query('DELETE FROM '.B2F_TABLE.' WHERE verso_id = '.$_GET['image_id'].';');
        array_push($page['infos'], l10n('This picture is no longer a backside'));
      }
    }
  }
  
/* GET SAVED VALUES */
  if ($template->get_template_vars('B2F_IS_VERSO') == null)
  {
    
    /* is the picture a back (verso) ? */
    $query = '
SELECT image_id, categories
  FROM '.B2F_TABLE.'
  WHERE verso_id = '.$_GET['image_id'].'
;';
    $result = pwg_query($query);
    
    if (pwg_db_num_rows($result))
    {
      list($recto_id, $cats) = pwg_db_fetch_row($result);
      $backside_hidden = (isset($cats) && !empty($cats) && $cats != "NULL");
      $template->assign(array(
        'B2F_IS_VERSO' => 'checked="checked"',
        'B2F_FRONT_ID' => $recto_id,
        'B2F_MOVE_VERSO' => $backside_hidden ? 'checked="checked"' : ''
      ));
    }
    /* The image is either a front (recto) for some back (verso),
    ** or is neither a front (recto) nor a back (verso).
    */
    else
    {
      $query = '
SELECT verso_id
  FROM '.B2F_TABLE.'
  WHERE image_id = '.$_GET['image_id'].'
;';
      $result = pwg_query($query);
      
      // If there are rows, then the image is the front (recto) for some back (verso).
      // This logic will simply display the back (verso) image id# in the template.
      if (pwg_db_num_rows($result))
      {      
        $item = pwg_db_fetch_assoc($result);

        $template->assign(array(
          'B2F_VERSO_ID' => $item['verso_id'],
          'B2F_VERSO_URL' => get_root_url().'admin.php?page=photo-'.$item['verso_id'],
        ));
      } 
      // Otherwise this image is neither a front (recto) nor a back (verso).
      // This logic provides the options of making it a back (verso) to some other front (recto) in the template.
      else
      {
        $template->assign(array(
          //'B2F_IS_VERSO' => '', // The template checks if this is unset and creates an *unchecked* box for "this is a backside".
          'B2F_FRONT_ID' => '',
          'B2F_MOVE_VERSO' => '',
        ));
      }
    }
  }
  
  $template->set_prefilter('picture_modify', 'back2front_picture_modify_prefilter');
}


function back2front_picture_modify_prefilter($content)
{
  $search = '</form>';
  $replacement = $search."\n\n".file_get_contents(B2F_PATH.'template/picture_modify.tpl');
  return str_replace($search, $replacement, $content);
}


/*
 * Add mark on thumbnails list
 */
function back2front_thumbnails($tpl_thumbnails_var)
{
  global $conf, $selection;
  
  if (!isset($selection))
  {
    return $tpl_thumbnails_var;
  }

  if (!$conf['back2front']['show_thumbnail']) return $tpl_thumbnails_var;
  if (empty($tpl_thumbnails_var)) return $tpl_thumbnails_var;
    
  /* has the pictures a verso ? */
  $query = '
SELECT image_id
  FROM '.B2F_TABLE.'
  WHERE image_id IN('.implode(',', $selection).')
;';
  $ids = array_from_query($query, 'image_id');
  
  $root_path = get_absolute_root_url();
  
  foreach($tpl_thumbnails_var as &$tpl_var)
  {
    if (in_array($tpl_var['id'], $ids))
    {
      $tpl_var['NAME'].= ' <img class="has_verso" src="'.$root_path.B2F_PATH.'template/rotate_1.png" title="'.l10n('This picture has a backside :').'"/>';
    }
  }
  
  return $tpl_thumbnails_var;
}
