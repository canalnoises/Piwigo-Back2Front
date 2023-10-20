{footer_script}
$(document).ready(function () {ldelim}
  $('input[name="b2f_is_verso"]').change(function () {ldelim}
     $('.frontside_param').toggle();
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
        <td><a href="{$B2F_VERSO_URL}">#{$B2F_VERSO_ID}</a></td>
      </tr>
    </table>
  {else}
    <table style="min-width:400px;">
      <tr>
	<td><input type="checkbox" name="b2f_is_verso" {if isset($B2F_IS_VERSO)}{$B2F_IS_VERSO}"{/if}></td>
        <td><b>{'This picture is a backside...'|@translate}</b></td>
      </tr>
      
      <tr class="frontside_param" {if !isset($B2F_IS_VERSO)}style="display:none;"{/if}>
        <td></td>
        <td><b>{'...of the picture n°'|@translate}</b><input type="text" size="4" name="b2f_front_id" value="{$B2F_FRONT_ID}"></td>
      </tr>
      
      <tr class="frontside_param" {if !isset($B2F_IS_VERSO)}style="display:none;"{/if}>
        <td><input type="checkbox" name="b2f_move_verso" {$B2F_MOVE_VERSO}></td>
        <td><b>{'Hide backside from albums'|@translate}</b></td>
      </tr>
    </table>

    <p style="text-align:center;">
      <input class="submit" type="submit" value="{'Submit'|@translate}" name="b2f_submit">
    </p>
  {/if}

  </fieldset>

</form>