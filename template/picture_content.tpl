{combine_css path=$B2F_PATH|@cat:"template/style.css"}
{combine_script id="jquery" load="header" path = "themes/default/js/jquery.min.js"}

{footer_script require="jquery"}
jQuery(document).ready(function () {ldelim}

{if $b2f_switch_mode == 'click'}
  jQuery('.reverse').click(function() {ldelim}
    if (jQuery(this).attr('rel') == 'front') {ldelim}
{else}
  jQuery('.reverse').hover(function() {ldelim}
{/if}

      /* picture switch */
    {if $b2f_transition == 'fade'}
      jQuery('img[alt="{$ALT_IMG}"]').animate({ldelim}
        opacity:0
      }, 400, function() {ldelim}
        jQuery(this).attr({ldelim}
          src: '{$VERSO_URL}',
          style: 'max-width:{$WIDTH_IMG}px;max-height:{$HEIGHT_IMG}px;',
        });
        jQuery(this).animate({ldelim}
          opacity:1
        }, 400);
      });
    {else}
      jQuery('img[alt="{$ALT_IMG}"]').attr({ldelim}
        src: '{$VERSO_URL}',
        style: 'max-width:{$WIDTH_IMG}px;max-height:{$HEIGHT_IMG}px;',
      });
    {/if}
    
    {if $b2f_switch_mode == 'click'}
      /* hd link */
      {if isset($VERSO_HD)}
      jQuery('img[alt="{$ALT_IMG}"]').parent().attr({ldelim}
        href: "javascript:phpWGOpenWindow('{$VERSO_HD}','{$high.UUID}','scrollbars=yes,toolbar=no,status=no,resizable=yes')"
      });
      {/if}
    
      /* B2F link content */
      jQuery(this).html('<img src="{$B2F_PATH}template/rotate_2.png"/> {$b2f_see_front}');
      jQuery(this).attr('rel', 'back');
    {/if}
      
{if $b2f_switch_mode == 'click'}
    } else if (jQuery(this).attr('rel') == 'back') {ldelim}
{else}
  }, function() {ldelim}
{/if} 

      /* picture switch */
    {if $b2f_transition == 'fade'}
      jQuery('img[alt="{$ALT_IMG}"]').animate({ldelim}
        opacity:0
      }, 400, function() {ldelim}
        jQuery(this).attr({ldelim}
          src: '{$SRC_IMG}',
          style: 'width:{$WIDTH_IMG}px;height:{$HEIGHT_IMG}px;',
        });
        jQuery(this).animate({ldelim}
          opacity:1
        }, 400);
      });
    {else}
      jQuery('img[alt="{$ALT_IMG}"]').attr({ldelim}
        src: '{$SRC_IMG}',
        style: 'width:{$WIDTH_IMG}px;height:{$HEIGHT_IMG}px;',
      });
    {/if}
      
    {if $b2f_switch_mode == 'click'}
      /* hd link */
      {if isset($high.U_HIGH)}
      jQuery('img[alt="{$ALT_IMG}"]').parent().attr({ldelim}
        href: "javascript:phpWGOpenWindow('{$high.U_HIGH}','{$high.UUID}','scrollbars=yes,toolbar=no,status=no,resizable=yes')"
      });
      {/if}
      
      /* B2F link content */
      jQuery(this).html('<img src="{$B2F_PATH}template/rotate_1.png"/> {$b2f_see_back}');
      jQuery(this).attr('rel', 'front');
    {/if}
      
{if $b2f_switch_mode == 'click'}
    }
  });
{else}
  });
{/if} 

});
{/footer_script}

<img src="{$VERSO_URL}" style="display:none;"/> {* <!-- force preload the verso --> *}

{if $b2f_position != 'toolbar'}<div>{/if}
<a class="reverse" rel="front" {if $b2f_position == 'toolbar'}style="border:none !important;"{/if} 
  {if $b2f_switch_mode == 'hover' and isset($VERSO_HD)}href="javascript:phpWGOpenWindow('{$VERSO_HD}','{$high.UUID}','scrollbars=yes,toolbar=no,status=no,resizable=yes')"{/if}>
  <img src="{$B2F_PATH}template/rotate_1.png"/> {$b2f_see_back}
</a>
{if $b2f_position != 'toolbar'}</div>{/if}
