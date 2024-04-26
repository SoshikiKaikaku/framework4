<form id="{$class_name}_{$db_name}_edit_form_[CURLY_OPEN]$data.id}">

	<input type="hidden" name="id" value="[CURLY_OPEN]$data.id}">

	{if $is_child}
		<input type="hidden" name="{$parent_db}_id" value="[CURLY_OPEN]$data.{$parent_db}_id}">
	{/if}

	{foreach $table_settings as $element}
		{if !$element['edit_flg']}
			{continue}
		{/if}

		<div>
			<p class="lang">{ucwords(str_replace("_", " ", $element['field_name']))}:</p>

			{if $element['field_type'] == 'text'}
				<input type="text" name="{$element['field_name']}" value="[CURLY_OPEN]$data.{$element['field_name']}}">
			{elseif $element['field_type'] == 'number' || $element['field_type'] == 'float'}
				<input type="number" name="{$element['field_name']}" value="[CURLY_OPEN]$data.{$element['field_name']}}">
			{elseif $element['field_type'] == 'textarea'}
				<textarea name="{$element['field_name']}" rows="5" style="height:auto;">[CURLY_OPEN]$data.{$element['field_name']}}</textarea>
			{elseif $element['field_type'] == 'select'}
				[CURLY_OPEN]html_options name="{$element['field_name']}" options=${$element['field_name']}_options selected=$data.{$element['field_name']}}
			{elseif $element['field_type'] == 'date'}
				<input type="text" name="{$element['field_name']}" value="[CURLY_OPEN]$data.{$element['field_name']}}" class="datepicker">
			{elseif $element['field_type'] == 'checkbox'}
				[CURLY_OPEN]if is_array($data.{$element['field_name']})}
				[CURLY_OPEN]assign var="check_data" value=$data.{$element['field_name']}}
				[CURLY_OPEN]else}
				[CURLY_OPEN]assign var="check_data" value=explode(", ", $data.{$element['field_name']})}
				[CURLY_OPEN]/if}
				[CURLY_OPEN]html_checkboxes name="{$element['field_name']}" options=${$element['field_name']}_options selected=$check_data separator='<br />'}
			{elseif $element['field_type'] == 'radio'}
				[CURLY_OPEN]html_radios name="{$element['field_name']}" options=${$element['field_name']}_options selected=$data.{$element['field_name']} separator='<br />'}
			{elseif $element['field_type'] == 'file'}
				<input type="file" name="{$element['field_name']}">
			{/if}

			<p class="error lang">[CURLY_OPEN]$errors['{$element['field_name']}']}</p>
		</div>
	{/foreach}

	<div>
		<button class="ajax-link lang" data-form="{$class_name}_{$db_name}_edit_form_[CURLY_OPEN]$data.id}" data-class="[CURLY_OPEN]$class}" data-function="edit_exe">Update</button>
	</div>

</form>

{if !$combine_enabled}
	{foreach $childs as $child}
		<div style="clear:both; border-bottom:1px #acacac solid; width:100%; padding-top: 50px; padding-bottom: 10px;"></div>
		<div id="{$child.child_class}_{$child.child_table}_[CURLY_OPEN]$data.id}" style="min-height: 300px;"></div>
	{/foreach}
{/if}
