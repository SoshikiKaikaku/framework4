
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
	
	<input type="text" name="search_{$name}" value="{$row.$name}">
	
	
{else if $type == "number"}
	{if $name != "id"}
		
		<input type="text" name="search_{$name}" value="{$row.$name}">
	{/if}
	
{else if $type == "float"}
	
	<input type="text" name="search_{$name}" value="{$row.$name}">
	
{else if $type == "textarea"}
	
	{if $field.max_bytes > 0}
		<textarea name="search_{$name}" class="wordcounter" data-counter_max="{$field.max_bytes}">{$row.$name}</textarea>
	{else}
		<textarea name="search_{$name}">{$row.$name}</textarea>
	{/if}
	
{else if $type == "textarea_links"}
	
	{if $field.max_bytes > 0}
		<textarea name="search_{$name}" class="wordcounter" data-counter_max="{$field.max_bytes}">{$row.$name}</textarea>
	{else}
		<textarea name="search_{$name}">{$row.$name}</textarea>
	{/if}
	
{else if $type == "markdown"}
	
	{if $field.max_bytes > 0}
		<textarea name="search_{$name}" class="wordcounter" data-counter_max="{$field.max_bytes}">{$row.$name}</textarea>
	{else}
		<textarea name="search_{$name}">{$row.$name}</textarea>
	{/if}
	
{else if $type == "dropdown"}
	{assign dropname "search_{$name}"}
	{html_options name=$dropname options=$field["options"] selected=$row[$name]}
	
{else if $type == "date"}
	
	{html_input_date name="search_{$name}" value="{$row.$name}"}
	
{else if $type == "year_month"}
	
	<input type="text" name="search_{$name}" value="{$row.$name}" class="year_month_picker">	
			
{else if $type == "radio"}
	{html_radios name="search_{$name}" options=$field["options"] selected=$row[$name] class="checkboxradio"}
	
{else if $type == "color"}
	
	<input type="text" name="search_{$name}" value="{$row[$name]}" class="colorpicker">
		
{else if $type == "time"}
	
	{html_input_time name="search_{$name}" value="{$row.$name}"}

{/if}

<span class="error">{$errors[$name]}</span>

</div>
