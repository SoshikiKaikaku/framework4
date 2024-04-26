<form id="{$class_name}_{$db_name}_add_form">

	{if $is_child}
		<input type="hidden" name="{$parent_db}_id" value="[CURLY_OPEN]$post.{$parent_db}_id}">
	{/if}

	{foreach $table_settings as $element}
		{if !$element['add_flg']}
			{continue}
		{/if}

		<div>
			<p class="lang">{ucwords(str_replace("_", " ", $element['field_name']))}:</p>

			{if $element['field_type'] == 'text'}
				<input type="text" name="{$element['field_name']}" value="[CURLY_OPEN]$post.{$element['field_name']}}">
			{elseif $element['field_type'] == 'number' || $element['field_type'] == 'float'}
				<input type="number" name="{$element['field_name']}" value="[CURLY_OPEN]$post.{$element['field_name']}}">
			{elseif $element['field_type'] == 'textarea'}
				<textarea name="{$element['field_name']}" rows="5" style="height:auto;">[CURLY_OPEN]$post.{$element['field_name']}}</textarea>
			{elseif $element['field_type'] == 'select'}
				[CURLY_OPEN]html_options name="{$element['field_name']}" options=${$element['field_name']}_options selected=$post.{$element['field_name']}}
			{elseif $element['field_type'] == 'date'}
				<input type="text" name="{$element['field_name']}" value="[CURLY_OPEN]$post.{$element['field_name']}}" class="datepicker">
			{elseif $element['field_type'] == 'checkbox'}
				[CURLY_OPEN]html_checkboxes name="{$element['field_name']}" options=${$element['field_name']}_options selected=$post.{$element['field_name']} separator='<br />'}
			{elseif $element['field_type'] == 'radio'}
				[CURLY_OPEN]html_radios name="{$element['field_name']}" options=${$element['field_name']}_options selected=$post.{$element['field_name']} separator='<br />'}
			{elseif $element['field_type'] == 'file'}
				<input type="file" name="{$element['field_name']}">
			{/if}

			<p class="error lang">[CURLY_OPEN]$errors['{$element['field_name']}']}</p>
		</div>
	{/foreach}

	<div>
		<button class="ajax-link lang" data-form="{$class_name}_{$db_name}_add_form" data-class="[CURLY_OPEN]$class}" data-function="add_exe">Add</button>
	</div>

</form>

