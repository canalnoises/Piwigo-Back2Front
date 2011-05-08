<div class="titrePage">
	<h2>Front2Back</h2>
</div>

<form method="post" action="" class="properties" ENCTYPE="multipart/form-data"> 
	<fieldset>
		<legend>{'Configuration'|@translate}</legend>	  
		<ul>			
			<li>
				<span class="property">{'f2b_path'|@translate}</span>
				<input type="text" name="path" value="{$PATH}"/>
			</li>
			<li>
				<span class="property">{'f2b_parent'|@translate}</span>
				<input type="text" name="parent" value="{$PARENT}"/>
			</li>		
			<li>
				<span class="property">{'f2b_hd_parent'|@translate}</span>
				<input type="text" name="hd_parent" value="{$HD_PARENT}"/>
			</li>
			<li>
				<span class="property">{'f2b_suffix'|@translate}</span>
				<input type="text" name="suffix" value="{$SUFFIX}"/>
			</li>
		</ul>
	</fieldset>
		
	<p><input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" /></p>
</form>

{'f2b_help'|@translate}
