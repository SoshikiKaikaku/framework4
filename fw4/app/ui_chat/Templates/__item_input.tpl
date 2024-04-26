
{**
このテンプレートを呼ぶ前に下記を設定しておく必要がある。
$param (ai_setting_parameters.fmt)
$name : 項目名
$values : 値の配列 $values[$name]
**}

{assign name $param["parameter_name"]}
{assign type $param["type"]}
{assign title $param["parameter_title"]}

{if $type == "text"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
	<input type="text" name="{$name}" value="{$values.$name}">


{else if $type == "number"}
	{if $name != "id"}
		<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
		<input type="text" name="{$name}" value="{$values.$name}">
	{/if}

{else if $type == "float"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
	<input type="text" name="{$name}" value="{$values.$name}">

{else if $type == "textarea"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
		{if $param.max_bytes > 0}
		<textarea name="{$name}" class="wordcounter" data-counter_max="{$param.max_bytes}">{$values.$name}</textarea>
	{else}
		<textarea name="{$name}">{$values.$name}</textarea>
	{/if}

{else if $type == "textarea_links"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
		{if $param.max_bytes > 0}
		<textarea name="{$name}" class="wordcounter" data-counter_max="{$param.max_bytes}">{$values.$name}</textarea>
	{else}
		<textarea name="{$name}">{$values.$name}</textarea>
	{/if}

{else if $type == "markdown"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
		{if $param.max_bytes > 0}
		<textarea name="{$name}" class="wordcounter" data-counter_max="{$param.max_bytes}">{$values.$name}</textarea>
	{else}
		<textarea name="{$name}">{$values.$name}</textarea>
	{/if}

{else if $type == "dropdown"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
		{html_options name=$name options=$param["options"] selected=$values[$name]}

{else if $type == "date"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
		{html_input_date name="{$name}" value="{$values.$name}"}

{else if $type == "year_month"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
	<input type="text" name="{$name}" value="{$values.$name}" class="year_month_picker">

{else if $type == "checkbox"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
	<div class="fr_checkbox">
		<span class="material-symbols-outlined unchecked">check_box_outline_blank</span>
		<span class="material-symbols-outlined checked">check_box</span>
		<input type="text" name="{$name}" class="fr_checkbox" value="{$values[$name]}">
	</div>

{else if $type == "radio"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
	<fieldset>
		{foreach $item_setting[$name]["options"] as $key=>$val}
			<label for="{$class}_{$dialog_name}_{$name}_{$key}_{$timestamp}">{$val.value}</label>
			{if $values[$name] == $key}
				{assign checked "checked"}
			{else}
				{assign checked ""}
			{/if}
			<input type="radio" name="{$name}" value="{$key}" id="{$class}_{$dialog_name}_{$name}_{$key}_{$timestamp}" class="checkboxradio" {$checked}>
		{/foreach}
	</fieldset>

{else if $type == "file"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
	<input type="file" name="{$name}" class="fr_image_paste" data-text="File Uploader">

{else if $type == "color"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
	<input type="text" name="{$name}" value="{$values[$name]}" class="colorpicker">

{else if $type == "image"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
	<p><img src="app.php?class={$class}&function=image&file={$values[$name]}_th&{$timestamp}" style="width:{$item_setting[$name]["params"]["thumbnail_size"]}px;max-width:100%;"></p>
	<input type="file" name="{$name}" class="fr_image_paste" data-text="Image Uploader">

{else if $type == "vimeo"}
    <h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>

    {if $values[$name] != ""}
		<p><div class="vimeo" data-vimeo_id="{$values[$name]}"></div></p>
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
		<input type="text" id="vimeo_id" name="{$name}" value="{$values[$name]}" style="margin-top:0px;">
	</div>
	<p class="error" id="vimeo_error"></p>
</div>
<script>
	if (vimeo_client_id == null) {
		var fd = new FormData();
		fd.append("class", "_SETTING");
		fd.append("function", "_VIMEO");
		appcon("app.php", fd);
	}
</script>

{else if $type == "time"}
	<h6>{$title}<span class="error" id="error_chat_form_{$timestamp}_{$name}"></span></h6>
		{html_input_time name="{$name}" value="{$values.$name}"}

{/if}


