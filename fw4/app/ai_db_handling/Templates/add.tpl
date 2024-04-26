<form id="form_{$timestamp}" class="chatform">
    {if $parent_f}
	<div>
	    <p class="lang">{$parent_tb}:</p>
	    {html_options name="parent_field" selected=$data.parent_field options=$parent_field_opt}

	    <p class="error lang">{$errors['parent_field']}</p>	
	</div>
    {/if}
	{foreach $fields as $field}
	    {if $field["parameter_name"] != 'parent_field'}
		<div>
	<!--		<p class="lang">{$field["parameter_title"]}:</p>-->
	<!--		<input type="text" name="{$field["parameter_name"]}" value="{$data[$field["parameter_name"]]}">-->
			{include file="{$base_template_dir}/__item_edit.tpl"}	
		</div>
		{/if}
	{/foreach}

	<div>
		<button class="ajax-link lang" data-form="form_{$timestamp}" data-class="{$class}" data-function="add_exe" data-ai_setting_id="{$data.ai_setting_id}">Add</button>
	</div>
</form>