{if $data.para_type==2}
		<div>
			<p class="lang">Sub table: {$subtable['tb_name']}</p>
		</div>
{/if}
<form id="ai_settings_ai_setting_parameters_edit_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">
	<input type="hidden" name="parameter_name" value="{$selected_field.parameter_name}">

	{if $ai_setting.type == 1}
	    {if $data.para_type==0}

		{html_options name="ai_fields_id" options=$fields_opt selected=$data.ai_fields_id}
	    {/if}
	    {if $data.para_type==1}
		<div>
			<p class="lang">Text:</p>
			<textarea name="text" class="wordcounter" data-counter_max="500">{$data.text}</textarea>
			<p class="error lang">{$errors['text']}</p>
		</div>
	    {/if}
	    

	{else}

	{/if}
	{if $ai_setting.handling == 6}
	    {if $data.para_type != 2}
	    <div>
		<p class="lang">align:</p>
		{html_options name="align" selected=$data.align options=$align_opt}

		<p class="error lang">{$errors['align']}</p>
	    </div>
	    {/if}
	    <div>
		<p class="lang">margintop:</p>
		<input type="text" name="margintop" value="{$data.margintop}">
	    </div>
	    <div>
		<p class="lang">marginbottom:</p>
		<input type="text" name="marginbottom" value="{$data.marginbottom}">
	    </div>
	    <div>
		<p class="lang">marginleft:</p>
		<input type="text" name="marginleft" value="{$data.marginleft}">
	    </div>
	    <div>
		<p class="lang">marginright:</p>
		<input type="text" name="marginright" value="{$data.marginright}">
	    </div>
	    <div>
		<p class="lang">width:</p>
		<input type="text" name="width" value="{$data.width}">
	    </div>
	    <div>
		<p class="lang">lineheight:</p>
		<input type="text" name="lineheight" value="{$data.lineheight}">
	    </div>
	    <div>
		<p class="lang">fontsize:</p>
		<input type="text" name="fontsize" value="{$data.fontsize}">
	    </div>
	    {if $data.para_type==2}
		{include file="_sub_parameters.tpl"}
		
	    {/if}
	    {if $data.para_type != 2}
	    <div>
		<p class="lang">before:</p>
		<input type="text" name="before" value="{$data.before}">
	    </div>
	    <div>
		<p class="lang">after:</p>
		<input type="text" name="after" value="{$data.after}">
	    </div>
	    <div>
		<p class="lang">rotate:</p>
		<input type="text" name="rotate" value="{$data.rotate}">
	    </div>
	    <div>
		<p class="lang">border:</p>
		{html_options name="border" selected=$data.border options=$border_opt}

		<p class="error lang">{$errors['border']}</p>
	    </div>
	    {if $data.para_type==0 && $data.field_type=='image'}
	    <div>
		<p class="lang">img_width:</p>
		<input type="text" name="img_width" value="{$data.img_width}">
	    </div>
	    <div>
		<p class="lang">img_height:</p>
		<input type="text" name="img_height" value="{$data.img_height}">
	    </div>
	    <div>
		<p class="lang">img_x:</p>
		<input type="text" name="img_x" value="{$data.img_x}">
	    </div>
	    <div>
		<p class="lang">img_y:</p>
		<input type="text" name="img_y" value="{$data.img_y}">
	    </div>
	    {/if}
	    {/if}
	{/if}

	<div>
	
	</div>
</form>

