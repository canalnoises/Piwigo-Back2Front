{footer_script}
$(document).ready(function () {ldelim}
  $('input[name="b2f_is_verso"]').change(function () {ldelim}
    if (this.checked) {ldelim}
      $('.frontside_id').css('visibility', '');
    } else {ldelim}
      $('.frontside_id').css('visibility', 'hidden');
    }
  });
});
{/footer_script}


<form action="{$F_ACTION}" method="post" id="back2front">

  <fieldset>
    <legend>{'Backside management'|@translate}</legend>

  {if isset($B2F_VERSO_ID)}
    <table>
      <tr>
        <td><b>{'This picture has a backside :'|@translate}</b></td>
        <td><a href="{$B2F_VERSO_URL}">{$B2F_VERSO_ID} - {$B2F_VERSO_NAME}</a></td>
      </tr>
    </table>
  {else}
    <table>
      <tr>
        <td><b>{'This picture is a backside...'|@translate}</b></td>
        <td><input type="checkbox" name="b2f_is_verso" {$B2F_IS_VERSO}></td>
      </tr>

      <tr class="frontside_id" {if !isset($B2F_IS_VERSO)}style="visibility:hidden;"{/if}>
        <td><b>{'...of the picture n°'|@translate}</b></td>
        <td><input type="text" size="4" name="b2f_front_id" value="{$B2F_FRONT_ID}"></td>
      </tr>
      
      <tr class="frontside_id" {if !isset($B2F_IS_VERSO)}style="visibility:hidden;"{/if}>
        <td><b>{'Move backside to private album'|@translate}</b></td>
        <td><input type="checkbox" name="b2f_move_verso" checked="checked"></td>
      </tr>
    </table>

    <p style="text-align:center;">
      <input class="submit" type="submit" value="{'Submit'|@translate}" name="b2f_submit">
    </p>
  {/if}

  </fieldset>

</form>