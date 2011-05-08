{footer_script}
$(document).ready(function () {ldelim}
  $('input[name="b2f_is_verso"]').change(function () {ldelim}
    if (this.checked) {ldelim}
      $('#frontside_id').css('visibility', '');
      $('select[name="level"]').prepend('<option label="{'Nobody (backside)'|@translate}" value="99">{'Nobody (backside)'|@translate}</option>');
      $('select[name="level"]').val('99').attr('readonly', 'readonly').css('opacity', '0.6');
      
    } else {ldelim}
      $('#frontside_id').css('visibility', 'hidden');
      $('select[name="level"] option[value="99"]').remove();
      $('select[name="level"]').val($('input[name="b2f_old_level"]').val()).removeAttr('readonly').css('opacity', '1');
    }
  });

  {if isset($B2F_IS_VERSO)}
  $('#frontside_id').css('visibility', '');
  $('select[name="level"]').prepend('<option label="{'Nobody (backside)'|@translate}" value="99">{'Nobody (backside)'|@translate}</option>');
  $('select[name="level"]').val('99').attr('readonly', 'readonly').css('opacity', '0.6');
  {else}
  $('#frontside_id').css('visibility', 'hidden');
  $('select[name="level"]').val('{$level_options_selected.0}');
  {/if}
});
{/footer_script}


<form action="{$F_ACTION}#back2front" method="post" id="back2front">

  <fieldset>
    <legend>{'Backside management'|@translate}</legend>

    <table>
    {if isset($B2F_VERSO_ID)}
      <tr>
        <td><strong>{'This picture has a backside :'|@translate}</strong></td>
        <td><a href="{$B2F_VERSO_URL}">{$B2F_VERSO_ID} - {$B2F_VERSO_NAME}</a></td>
      </tr>
    </table>
    {else}

      <tr>
        <td><strong>{'This picture is a backside...'|@translate}</strong></td>
        <td><input type="checkbox" name="b2f_is_verso" {$B2F_IS_VERSO}></td>
      </tr>

      <tr id="frontside_id">
        <td><strong>{'...of the picture n°'|@translate}</strong></td>
        <td><input type="text" size="4" name="b2f_front_id" value="{$B2F_FRONT_ID}"></td>
      </tr>

    </table>

    <p style="text-align:center;">
      <input type="hidden" name="b2f_old_level" value="{if isset($B2F_OLD_LEVEL)}{$B2F_OLD_LEVEL}{else}{$level_options_selected.0}{/if}">
      <input class="submit" type="submit" value="{'Submit'|@translate}" name="b2f_submit">
    </p>
    {/if}

  </fieldset>

</form>