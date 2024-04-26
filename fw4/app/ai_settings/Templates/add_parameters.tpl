

	{if $ai_setting.type == 1}
		<h5>Add a Parameter</h5>
	    <form id="ai_settings_ai_setting_parameters_add_form0">

		<input type="hidden" name="ai_setting_id" value="{$data.ai_setting_id}">
		<div>

		    {html_options name="ai_fields_id" options=$fields_opt}
		</div>
		<div>
		    <button class="ajax-link lang" data-form="ai_settings_ai_setting_parameters_add_form0" data-class="{$class}" data-function="add_parameters_exe" data-para_type="0">Add</button>
		</div>
	    </form>
	    <form id="ai_settings_ai_setting_parameters_add_form1"  style="margin-top: 15px;">

		<input type="hidden" name="ai_setting_id" value="{$data.ai_setting_id}">
		<div>
			<p class="lang">Text:</p>
			<textarea name="text" class="wordcounter" data-counter_max="500">{$data.text}</textarea>
			<p class="error lang">{$errors['text']}</p>
		</div>
		<div>
		    <button class="ajax-link lang" data-form="ai_settings_ai_setting_parameters_add_form1" data-class="{$class}" data-function="add_parameters_exe" data-para_type="1">Add</button>
		</div>
	    </form>
	    <form id="ai_settings_ai_setting_parameters_add_form2">

		<input type="hidden" name="ai_setting_id" value="{$data.ai_setting_id}">
		<div>
			<p class="lang">Sub table:</p>
			{html_options name="subtable_id" selected=$data.subtable_id options=$subtable_opt}
		</div>
		<div>
		    <button class="ajax-link lang" data-form="ai_settings_ai_setting_parameters_add_form2" data-class="{$class}" data-function="add_parameters_exe" data-para_type="2">Add</button>
		</div>
	    </form>

	{else}
	    <form id="ai_settings_ai_setting_parameters_add_form">

	    <input type="hidden" name="ai_setting_id" value="{$data.ai_setting_id}">
		<div>
			<p class="lang">Parameter Name (For Programming):</p>

			<input type="text" name="parameter_name" value="{$data.parameter_name}">
			<p class="error lang">{$errors['parameter_name']}</p>
		</div>
		<div>
			<p class="lang">Parameter Title (For Users):</p>
			<input type="text" name="parameter_title" value="{$data.parameter_title}">

			<p class="error lang">{$errors['parameter_title']}</p>
		</div>
		<div>
			<p class="lang">Validation:</p>
			{html_options name="validation" selected=$data.validation options=$validation_opt}

			<p class="error lang">{$errors['validation']}</p>
		</div>
		<div>
			<p class="lang">Parameter Type:</p>
			{html_options name="type" selected=$data.type options=$type_opt}
		</div>
		<div>
			<p class="lang">Max bytes for textarea:</p>
			<input type="text" name="max_bytes" value="{$data.max_bytes}">
		</div>
		<div>
			<p class="lang">Default:</p>
			<input type="text" name="default" value="{$data.default}">
		</div>
		<div>
			<p class="lang">Options:</p>
			<textarea name="options" class="wordcounter" data-counter_max="500">{$data.options}</textarea>
			<p class="error lang">{$errors['options']}</p>
			<p>
				Type like the following.<br />
				1:Apple<br />
				2:Orange<br />
				3:Lemmon<br />
			</p>
		</div>
		<div>
		    <button class="ajax-link lang" data-form="ai_settings_ai_setting_parameters_add_form" data-class="{$class}" data-function="add_parameters_exe">Add</button>
		</div>

	    </form>
	{/if}


	

