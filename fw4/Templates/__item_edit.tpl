
{**
This template need the following parameters.
$field : The field format array of ai_db/ai_field.fmt
$row : array of the values.
**}

{assign name $field["parameter_name"]}
{assign type $field["type"]}
{assign title $field["parameter_title"]}

<div class="field_edit">

<h6 class="lang">{$title}</h6>
	
{if $type == "text"}
	
	<input type="text" name="{$name}" value="{$row.$name}">
	
	
{else if $type == "number"}
	{if $name != "id"}
		
		<input type="text" name="{$name}" value="{$row.$name}">
	{/if}
	
{else if $type == "float"}
	
	<input type="text" name="{$name}" value="{$row.$name}">
	
{else if $type == "textarea"}
	
	{if $field.max_bytes > 0}
		<textarea name="{$name}" class="wordcounter" data-counter_max="{$field.max_bytes}">{$row.$name}</textarea>
	{else}
		<textarea name="{$name}">{$row.$name}</textarea>
	{/if}
	
{else if $type == "textarea_links"}
	
	{if $field.max_bytes > 0}
		<textarea name="{$name}" class="wordcounter" data-counter_max="{$field.max_bytes}">{$row.$name}</textarea>
	{else}
		<textarea name="{$name}">{$row.$name}</textarea>
	{/if}
	
{else if $type == "markdown"}
	
	{if $field.max_bytes > 0}
		<textarea name="{$name}" class="wordcounter" data-counter_max="{$field.max_bytes}">{$row.$name}</textarea>
	{else}
		<textarea name="{$name}">{$row.$name}</textarea>
	{/if}
	
{else if $type == "dropdown"}
	
	{html_options name=$name options=$field["options"] selected=$row[$name]}
	
{else if $type == "date"}
	
	{html_input_date name="{$name}" value="{$row.$name}"}
	
{else if $type == "year_month"}
	
	<input type="text" name="{$name}" value="{$row.$name}" class="year_month_picker">
	
{else if $type == "checkbox"}
	{foreach $field["options"] as $key=>$option}
	    {if $row.$name}
		{if in_array($key,$row.$name)}
		    {assign is_checked checked}
		{else}
		    {assign is_checked ''}
		{/if}
	    {/if}
	<div>
		<input type="checkbox" id="{$name}_{$key}" name="{$name}[]" value="{$key}" {$is_checked}>
		<label for="{$name}_{$key}">{$option}</label>
	</div>
	{/foreach}
			
{else if $type == "radio"}
	{html_radios name="{$name}" options=$field["options"] selected=$row[$name] class="checkboxradio"}
	{*<fieldset>
		{foreach $item_setting[$name]["options"] as $key=>$val}
			<label for="{$class}_{$dialog_name}_{$name}_{$key}_{$timestamp}">{$val.value}</label>
			{if $row[$name] == $key}
				{assign checked "checked"}
			{else}
				{assign checked ""}
			{/if}
			<input type="radio" name="{$name}" value="{$key}" id="{$class}_{$dialog_name}_{$name}_{$key}_{$timestamp}" class="checkboxradio" {$checked}>
		{/foreach}
   </fieldset>*}
   
{else if $type == "file"}
	{if $row[$name]}
	    {*<button class="download-link lang download-btn-file" data-class="ai_db" data-function="download_file" data-filename="{$row[$name]}" data-id="{$data['id']}"><span class="material-symbols-outlined">cloud_download</span></button><br>*}
	{/if}
	<input type="file" name="{$name}" class="fr_image_paste" data-text="File Uploader">

{else if $type == "color"}
	
	<input type="text" name="{$name}" value="{$row[$name]}" class="colorpicker">
	
{else if $type == "image"}
	{if $row[$name]}
	<p><img src="app.php?class=ai_db&function=view_image&file={$row[$name]}&{$timestamp}" style="width:{$item_setting[$name]["params"]["thumbnail_size"]}px;max-width:100%;"></p>
	{/if}
	<input type="file" name="{$name}" class="fr_image_paste" data-text="Image Uploader">
	
{else if $type == "vimeo"}
    
    
    {if $row[$name] != ""}
	<p><div class="vimeo" data-vimeo_id="{$row[$name]}"></div></p>
    {/if}
    
	<div class="vimeo_upload_area">
		<input type="hidden" name="vimeo_title" value="from system" id="vimeo_title">
		<input type="hidden" name="vimeo_description" value="from system" id="vimeo_description">
		<p class="lang">You can upload a video here or put the ID that you had uploaded from vimeo site.</p>
		<div style="width:50%;float: left;">
			<p style="margin-top:0px;">Upload a video</p>
			<input id="vimeo_file" type="file" />
		</div>
		<div style="width:50%;float: left;">
			<p style="margin-top:0px;">Vimeo ID</p>
			<input type="text" id="vimeo_id" name="{$name}" value="{$row[$name]}" style="margin-top:0px;">
		</div>
		<p class="error" id="vimeo_error"></p>
	</div>
	<script>
		if(vimeo_client_id == null){
			var fd = new FormData();
			fd.append("class","_SETTING");
			fd.append("function","_VIMEO");
			appcon("app.php",fd);
		}
	</script>
	
{else if $type == "time"}
	
	{html_input_time name="{$name}" value="{$row.$name}"}

{/if}

<span class="error">{$errors[$name]}</span>

</div>
