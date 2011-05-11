<div class="titrePage">
	<h2>Back2Front</h2>
</div>

<form method="post" action="" class="properties" ENCTYPE="multipart/form-data"> 
	<fieldset>
		<legend>{'Display'|@translate}</legend>	  
		<ul>			
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
		</ul>
	</fieldset>
		
	<p><input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" /></p>
</form>
