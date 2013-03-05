{combine_css path=$B2F_PATH|@cat:"template/style.css"}

{if !$verso.selected_derivative->is_cached() && $current.selected_derivative->is_cached}
{combine_script id='jquery.ajaxmanager' path='themes/default/js/plugins/jquery.ajaxmanager.js' load='footer'}
{combine_script id='thumbnails.loader' path='themes/default/js/thumbnails.loader.js' require='jquery.ajaxmanager' load='footer'}
{footer_script}var error_icon = "{$ROOT_URL}{$themeconf.icon_dir}/errors_small.png"{/footer_script}
{/if}


{if $b2f_position != "toolbar"}<div>{/if}
<a class="reverse" data-what="front" rel="nofollow" class="pwg-state-default pwg-button" title="{$b2f_see_back}" {if $b2f_position == "toolbar"}style="border:none !important;"{/if}>
  <img src="{$ROOT_URL}{$B2F_PATH}template/rotate_1.png"/> {$b2f_see_back}
</a>
{if $b2f_position != "toolbar"}</div>{/if}

<img {if $verso.selected_derivative->is_cached()}src="{$verso.selected_derivative->get_url()}" {$verso.selected_derivative->get_size_htm()}{else}src="{$ROOT_URL}{$themeconf.img_dir}/ajax_loader.gif" data-src="{$verso.selected_derivative->get_url()}"{/if} alt="{$ALT_IMG}" id="theVersoImage" usemap="#map{$current.selected_derivative->get_type()}" title="{if isset($COMMENT_IMG)}{$COMMENT_IMG|@strip_tags:false|@replace:'"':' '}{else}{$current.TITLE|@replace:'"':' '} - {$ALT_IMG}{/if}">


{footer_script require="jquery"}
jQuery("#derivativeSwitchLink").hide();
jQuery(".reverse").css("display", "inline-block");
jQuery("img#theVersoImage").insertAfter(jQuery("img#theMainImage"));

{if $b2f_switch_mode == "click"}
  jQuery(".reverse").click(function() {ldelim}
    if (jQuery(this).data("what") == "front") {ldelim}
{else}
  jQuery(".reverse").hover(function() {ldelim}
{/if}

      $("img#theVersoImage").hide();
      
    {if $b2f_transition == "fade"}
      $("img#theMainImage").fadeOut(400, function() {ldelim}
        $("img#theVersoImage").fadeIn(400);
      });
    {else}
      $("img#theMainImage").hide();
      $("img#theVersoImage").show();
    {/if}
    
      $(this).data("what", "back");
      $(this).html('<img src="{$ROOT_URL}{$B2F_PATH}template/rotate_2.png"/> {$b2f_see_front}');

{if $b2f_switch_mode == "click"}
    } else if (jQuery(this).data("what") == "back") {ldelim}
{else}
  }, function() {ldelim}
{/if} 
    
      $("img#theMainImage").hide();
    
    {if $b2f_transition == "fade"}
      $("img#theVersoImage").fadeOut(400, function() {ldelim}
        $("img#theMainImage").fadeIn(400);
      });
    {else}
      $("img#theVersoImage").hide();
      $("img#theMainImage").show();
    {/if}
    
      $(this).data("what", "front");
      $(this).html('<img src="{$ROOT_URL}{$B2F_PATH}template/rotate_1.png"/> {$b2f_see_back}');

{if $b2f_switch_mode == "click"}
    }
  });
{else}
  });
{/if} 
{/footer_script}
