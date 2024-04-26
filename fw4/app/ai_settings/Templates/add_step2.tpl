<form id="ai_settings_ai_setting_add_form">

	<input type="hidden" name="type" value="{$post.type}">
	<input type="hidden" name="code_type" value="{$post.code_type}">

	<div>
		<div class="type_form_area">
			{if $post.type == 1}
				<p class="type_db">{$type_opt[$post.type]}</p>
			{else if $post.type == 0}
				<p class="type_code">{$type_opt[$post.type]}</p>
			{else if $post.type == 2}
				<p class="type_design">{$type_opt[$post.type]}</p>
			{/if}
		</div>
	</div>
	{if $post.type == 0}
		<div id="area_code">
			<div>
				<p class="lang" style="font-size:24px;">Code Type: {$code_type_opt[$post.code_type]}</p>
			</div>
			<div>
				<p class="lang">Class Name:</p>
				<input type="text" name="class_name" value="{$post.class_name}">

				<p class="error lang">{$errors['class_name']}</p>
			</div>
			<div>
				<p class="lang">Function Name:</p>

				<input type="text" name="function_name" value="{$post.function_name}">
				<p class="error lang">{$errors['function_name']}</p>
			</div>
		</div>
			

		{if $post.code_type == 3}
			<div>
				<p class="lang">Information (The following text will be shown when this function called.):</p>
				<input type="text" name="information" value="{$data.information}">

				<p class="error lang">{$errors['information']}</p>
			</div>
		{/if}

	{else if $post.type == 1}
		<div id="area_db">
			<div>
				<p class="lang">Table Name:</p>
				{html_options name="ai_db_id" options=$tables_opt selected=$post.ai_db_id}
				<p class="error lang">{$errors['ai_db_id']}</p>
			</div>

			<div>
				<p class="lang">Database Handling:</p>
				{html_options name="handling" options=$database_handling_opt selected=$post.handling}
				<p class="error lang">{$errors['handling']}</p>
			</div>
		</div>

	{else if $post.type == 2}
		<div id="area_code">
			<div>
				<p class="lang">Select Predefined Function:</p>
				{html_options name="predefined_function" options=$predefined_function_opt selected=$post.predefined_function}
			</div>
		</div>
	{/if}


	{if ($post.type == 0 && $post.code_type == 1) || $post.type == 1 }
		<div>
			<p class="lang">Description (Type description of this function to notice chatGPT.):</p>
			<textarea name="description" class="wordcounter" data-counter_max="500">{$data.description}</textarea>
			<p class="error lang">{$errors['description']}</p>
		</div>

		<div>
			<p class="lang">Information (The following text will be shown when this function called.):</p>
			<input type="text" name="information" value="{$data.information}">

			<p class="error lang">{$errors['information']}</p>
		</div>
		<div>
			<p class="lang">Menu Name:</p>
			<input type="text" name="menu_name" value="{$data.menu_name}">

			<p class="error lang">{$errors['menu_name']}</p>
		</div>

		<!-- LIMIT BY User -->
		{include file="_limit_user.tpl"}
	{/if}

	{if $post.type == 2}
		<div>
			<button class="ajax-link lang" data-form="ai_settings_ai_setting_add_form" data-class="{$class}" data-function="add_pre_func">Add</button>
		</div>
	{else}
		<div>
			<button class="ajax-link lang" data-form="ai_settings_ai_setting_add_form" data-class="{$class}" data-function="add_exe">Add</button>
		</div>
	{/if}
</form>

