
<div class="flex">
		{foreach $manage_items as $item}
			<button class="ajax-link aichat-btns" data-class="{$item.class_name}" data-function="{$item.function_name}" data-table_name="{$item.table_name}" data-ai_setting_id={$item.ai_setting_id}>{$item.menu_name}</button>
		{/foreach}
</div>
