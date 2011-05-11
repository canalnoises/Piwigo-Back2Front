{combine_css path=$B2F_PATH|@cat:"template/style.css"}

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
        jQuery(this).animate({ldelim}
          width:'{$VERSO_WIDTH}px',
          height:'{$VERSO_HEIGHT}px'
        }, 200, function() {ldelim}
            jQuery(this).attr('src', '{$VERSO_URL}');
            jQuery(this).animate({ldelim}
              opacity:1
            }, 400);
        });
      });
    {else}
      jQuery('img[alt="{$ALT_IMG}"]').attr({ldelim}
        src: '{$VERSO_URL}',
        style: 'width:{$VERSO_WIDTH}px;height:{$VERSO_HEIGHT}px;',
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
      jQuery(this).html('<img src="{$B2F_PATH}template/rotate_2.png"/> {'See front'|@translate}');
      jQuery(this).attr('rel', 'back');
    {/if}
      
{if $b2f_switch_mode == 'click'}
    } else if (jQuery(this).attr('rel') == 'back') {ldelim}
{else}
  }, function() {ldelim}
{/if} 

    {if $b2f_transition == 'fade'}
      jQuery('img[alt="{$ALT_IMG}"]').animate({ldelim}
        opacity:0
      }, 400, function() {ldelim}
        jQuery(this).animate({ldelim}
          width:'{$WIDTH_IMG}px',
          height:'{$HEIGHT_IMG}px'
        }, 200, function() {ldelim}
            jQuery(this).attr('src', '{$SRC_IMG}');
            jQuery(this).animate({ldelim}
              opacity:1
            }, 400);
        });
      });
    {else}
      jQuery('img[alt="{$ALT_IMG}"]').attr({ldelim}
        src: '{$SRC_IMG}',
        style: 'width:{$WIDTH_IMG}px;height:{$HEIGHT_IMG}px;',
      });
    {/if}
      
    {if $b2f_switch_mode == 'click'}
      {if isset($high.U_HIGH)}
      jQuery('img[alt="{$ALT_IMG}"]').parent().attr({ldelim}
        href: "javascript:phpWGOpenWindow('{$high.U_HIGH}','{$high.UUID}','scrollbars=yes,toolbar=no,status=no,resizable=yes')"
      });
      {/if}
      
      jQuery(this).html('<img src="{$B2F_PATH}template/rotate_1.png"/> {'See back'|@translate}');
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

<img src="{$VERSO_URL}" style="display:none;"> {* <!-- force preload the verso --> *}

<a class="reverse" rel="front" name="verso-link" {if $b2f_switch_mode == 'hover'}href="javascript:phpWGOpenWindow('{$VERSO_HD}','{$high.UUID}','scrollbars=yes,toolbar=no,status=no,resizable=yes')"{/if}>
  <img src="{$B2F_PATH}template/rotate_1.png"/> {'See back'|@translate}
</a>
<br/>