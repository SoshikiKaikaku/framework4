
<form id="{$class}_{$dialog_name}_{$timestamp}">
    <input type="hidden" name="_form_id" value="{$class}_{$dialog_name}_{$timestamp}">
	{foreach $add_items as $name}
		<div class="add_item">
			{include file="__item_viewer_edit.tpl"}
			<p class="error error_{$name}"></p>
		</div>
	{/foreach}

</form>

