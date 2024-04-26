<form id="form_{$timestamp}">
    <input type="hidden" value="{$data['id']}" name="id">
	{foreach $fields as $field}
		<div>
			{include file="{$base_template_dir}/__item_edit.tpl"}	
		</div>
	{/foreach}

	<div>
		<button class="ajax-link lang" data-form="form_{$timestamp}" data-class="{$class}" data-function="edit_exe" data-ai_setting_id="{$data.ai_setting_id}">Update</button>
	</div>
</form>