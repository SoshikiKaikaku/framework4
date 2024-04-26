<form id="ai_settings_ai_setting_add_form">

	<input type="hidden" name="type" value="{$post.type}">

	<p class="type_code">{$type_opt[$post.type]}</p>

	<p>Please choose code type</p>

	{html_options name="code_type" options=$code_type_opt}


	<div>
		<button class="ajax-link lang" data-form="ai_settings_ai_setting_add_form" data-class="{$class}" data-function="add_step3">Next</button>
	</div>

</form>

