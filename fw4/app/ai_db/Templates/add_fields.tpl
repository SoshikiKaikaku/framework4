<form id="ai_dbs_ai_fields_add_form">

	<input type="hidden" name="ai_db_id" value="{$post.ai_db_id}">
	<div>
		<p class="lang">Parameter Name:</p>
		<input type="text" name="parameter_name" value="{$post.parameter_name}">

		<p class="error lang">{$errors['parameter_name']}</p>
	</div>
	<div>
		<p class="lang">Parameter Title:</p>
		<input type="text" name="parameter_title" value="{$post.parameter_title}">

		<p class="error lang">{$errors['parameter_title']}</p>
	</div>	

	<div>
		<p class="lang">Parameter Type:</p>
		{html_options name="type" id="type_event" selected=$data.type options=$type_opt}
	</div>

	<div id="area_option">
		<p class="lang">Options:</p>
		{html_options name="constant_array_name" output=$constant_array_opt values=$constant_array_opt}
	</div>

	<div>
		<p class="lang">Data Length(bytes):</p>
		<input class="field_length" type="text" name="length" value="{$data.length}">
		<p class="recommended_length"></p>
		<p class="error lang">{$errors['length']}</p>
	</div>

	<div>
		<p class="lang">Validation:</p>
		{html_options name="validation" selected=$data.validation options=$validation_opt}
		<p class="error lang">{$errors['validation']}</p>
	</div>
	<div>
		<p class="lang">Default:</p>
		<input type="text" name="default_value" value="{$data.default_value}">
	</div>

	<div>
		<button class="ajax-link lang" data-form="ai_dbs_ai_fields_add_form" data-class="{$class}" data-function="add_fields_exe">Add</button>
	</div>

</form>

