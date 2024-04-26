<form id="ai_settings_ai_setting_parameters_delete_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">

	<span class="lang">Delete the following Ai Setting Parameters</span>
	<p>
		<b>

			{$data.id}
		</b>
	</p>

	<br>
	<p class="lang">If you perform this process, it will not be restored. Do you want to process it?</p>
</form>

<button class="ajax-link lang" data-form="ai_settings_ai_setting_parameters_delete_form_{$data.id}" data-class="{$class}" data-function="delete_parameters_exe">Delete</button>

