{combine_css path=$B2F_PATH|@cat:"template/style.css"}
{if $themeconf.id == roma}<style type="text/css">.lang_help {ldelim} background-color:#464646; }</style>{/if}

{footer_script require='jquery'}{literal}
jQuery().ready( function () {
  jQuery('select[name="lang_desc_select"]').change(function () {
    jQuery('[id^="link_name"]').hide();
    jQuery("#link_name_"+this.options[this.selectedIndex].value).show();
  });
  jQuery('[id^="link_name_"]').keyup(function () {
    arr = jQuery(this).attr("id").split("link_name_");
    id = arr[1];
    opt = jQuery('select[name="lang_desc_select"] option[id="opt_'+id+'"]');
    if (this.value != '')
      opt.html(opt.html().replace("\u2718", "\u2714"));
    else
      opt.html(opt.html().replace("\u2714", "\u2718"));
  });
});

jQuery('.lang_help').tipTip();
{/literal}{/footer_script}

<div class="titrePage">
	<h2>Back2Front</h2>
</div>

<form method="post" action="" class="properties" ENCTYPE="multipart/form-data"> 
	<fieldset>
		<legend>{'Display'|@translate}</legend>	  
		<ul>			
      <li>
        <span class="property">{'Link position'|@translate}</span>
        <label><input type="radio" name="position" value="top" {if $POSITION == 'top'}checked="checked"{/if}/> {'Top'|@translate}</label>
        <label><input type="radio" name="position" value="bottom" {if $POSITION == 'bottom'}checked="checked"{/if}/> {'Bottom'|@translate}</label>
        <label><input type="radio" name="position" value="toolbar" {if $POSITION == 'toolbar'}checked="checked"{/if}/> {'Toolbar'|@translate}</label>
      </li>
			<li>
				<span class="property">{'Switch mode'|@translate}</span>
				<label><input type="radio" name="switch_mode" value="click" {if $SWITCH_MODE == 'click'}checked="checked"{/if}/> {'Click'|@translate}</label>
				<label><input type="radio" name="switch_mode" value="hover" {if $SWITCH_MODE == 'hover'}checked="checked"{/if}/> {'Mouseover'|@translate}</label>
			</li>
			<li>
				<span class="property">{'Transition'|@translate}</span>
				<label><input type="radio" name="transition" value="none" {if $TRANSITION == 'none'}checked="checked"{/if}/> {'None'|@translate}</label>
				<label><input type="radio" name="transition" value="fade" {if $TRANSITION == 'fade'}checked="checked"{/if}/> {'Fade'|@translate}</label>
			</li>	

      <li>
        <span class="property">{'Link name'|@translate}</span>
        <select name="lang_desc_select">
          {foreach from=$link_name item=lang}
            <option value="{$lang.LANGUAGE_CODE}" id="opt_{$lang.LANGUAGE_CODE}">{if empty($lang.VALUE)}&#x2718;{else}&#x2714;{/if} &nbsp;{$lang.LANGUAGE_NAME}</option>
          {/foreach}
        </select>
        {foreach from=$link_name item=lang}
          <input type="text" size="30" name="link_name[{$lang.LANGUAGE_CODE}]" id="link_name_{$lang.LANGUAGE_CODE}" value="{$lang.VALUE}" style="{if $lang.LANGUAGE_CODE != 'default'}display:none; {/if}margin-left:10px;">
        {/foreach}

        <a class="lang_help" title="{'Seperate the two labels with the | symbol. Leave blank to use default translation.'|@translate}">i</a>
      </li>      
		</ul>
	</fieldset>
		
	<p><input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" /></p>
</form>
