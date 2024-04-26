
<form id="chat_form_{$timestamp}" class="chat_input_form">

	{foreach $parameter_list as $param}

		{include file="__item_input.tpl"}

	{/foreach}

	<button class="ajax-link" data-form="chat_form_{$timestamp}" data-class="{$class}" data-function="validation" data-ai_setting_id="{$ai_setting.id}">Submit</button>
	<p class="error" style="float:right;text-align: right;width:100%;" id="error_chat_form_{$timestamp}_submitbutton_message"></p>

</form>


