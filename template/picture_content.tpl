{combine_css path=$B2F_PATH|@cat:"template/style.css"}

{footer_script require="jquery"}
jQuery(document).ready(function () {ldelim}
  jQuery('.reverse').click(function() {ldelim}
    if (jQuery(this).attr('rel') == 'front') {ldelim}
      /* picture attributes */
      jQuery('#theImage img:first-child').attr({ldelim}
        src: '{$VERSO_URL}',
        style: 'width:;height:;',
      });
      
      /* hd link atributes */
      {if isset($VERSO_HD)}
      jQuery('#theImage a:first-child').attr({ldelim}
        href: "javascript:phpWGOpenWindow('{$VERSO_HD}','{$high.UUID}','scrollbars=yes,toolbar=no,status=no,resizable=yes')"
      });
      {/if}
      
      /* B2F link content */
      jQuery(this).html('<img src="{$B2F_PATH}template/rotate_2.png"/> {'See front'|@translate}');
      jQuery(this).attr('rel', 'back');
      
    } else if (jQuery(this).attr('rel') == 'back') {ldelim}
      jQuery('#theImage img:first-child').attr({ldelim}
        src: '{$SRC_IMG}',
        style: 'width:{$WIDTH_IMG}px;height:{$HEIGHT_IMG}px;',
      });
      
      {if isset($VERSO_HD)}
      jQuery('#theImage a:first-child').attr({ldelim}
        href: "javascript:phpWGOpenWindow('{$high.U_HIGH}','{$high.UUID}','scrollbars=yes,toolbar=no,status=no,resizable=yes')')"
      });
      {/if}
      
      jQuery(this).html('<img src="{$B2F_PATH}template/rotate_1.png"/> {'See back'|@translate}');
      jQuery(this).attr('rel', 'front');
    }
  });
});
{/footer_script}

<a class="reverse" rel="front" href="#">
  <img src="{$B2F_PATH}template/rotate_1.png"/>
  {'See back'|@translate}
</a>