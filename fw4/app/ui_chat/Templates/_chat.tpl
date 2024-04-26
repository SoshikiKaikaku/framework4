{if $MYSESSION.name != ""}
<div class="ui_chat_login_name"><span class="lang">Login:</span> {$MYSESSION.name}</div>
{/if}
<div style="clear:both;"></div>
<div class="chat-container">
	<div class="grid-menu">
		{foreach $manage_items as $item}
			{if $item.menu_name}
				<button class="ajax-link aichat-btns" data-class="{$item.class_name}" data-function="{$item.function_name}" data-table_name="{$item.table_name}" data-ai_setting_id="{$item.ai_setting_id}" data-public="{$public}">{$item.menu_name}</button>
			{/if}
		{/foreach}
	</div>

	<div id="chat_history">
		{$first_msg nofilter}
	</div>
	<div id="loading_container">
		<div id="loading" style="width: 100px;">
			<img id="loading-image" src="app.php?class={$class}&function=img&file=typing.gif&public={$public}" alt="Loading..." />
		</div>
	</div>
	<form id="chatgpt_msg_form" class="message-box">
		<input type="hidden" name="public" value="{$public}">
		<textarea name="msg" class="message-input" id="msg"></textarea>
		<button class="ajax-link lang send-button add_chat_event" id="chat_send" data-class="{$class}" data-function="chat" data-form="chatgpt_msg_form">Send</button>
	</form>
</div>
