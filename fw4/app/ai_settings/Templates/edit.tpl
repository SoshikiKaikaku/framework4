<form id="ai_settings_ai_setting_edit_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">
	<input type="hidden" name="type" value="{$data.type}">

	<div>
		<div class="type_form_area">
			{if $data.type == 1}
				<p class="type_db">{$type_opt[$data.type]}</p>
			{else if $data.type == 0}
				<p class="type_code">{$type_opt[$data.type]}</p>
			{else if $data.type == 2}
				<p class="type_design">{$type_opt[$data.type]}</p>
			{/if}	
		</div>
	</div>

	{if $data.type == 0}
		<div id="area_code">
			<div>
				<p class="lang" style="font-size:24px;">Code Type: {$code_type_opt[$data.code_type]}</p>
			</div>
			<div>
				<p class="lang">Class Name:</p>
				<input type="text" name="class_name" value="{$data.class_name}">

				<p class="error lang">{$errors['class_name']}</p>
			</div>
			<div>
				<p class="lang">Function Name:</p>
				<input type="text" name="function_name" value="{$data.function_name}">
				<p class="error lang">{$errors['function_name']}</p>
			</div>
			{if $data.code_type == 1}
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
			{/if}
			{if $data.code_type == 3}
				<div>
					<p class="lang">Information (The following text will be shown when this function called.):</p>
					<input type="text" name="information" value="{$data.information}">

					<p class="error lang">{$errors['information']}</p>
				</div>
			{/if}
		</div>
	{else if $data.type == 1}
		<div id="area_db">
			<div>
				<p class="lang">Table Name:</p>
				<p style="font-size:24px;margin-top:0px;">{$tables_opt[$data.ai_db_id]}</p>
				<input type="hidden" name="ai_db_id" value="{$data.ai_db_id}">
			</div>

			<div>
				<p class="lang">Database Handling:</p>
				{html_options name="handling" options=$database_handling_opt selected=$data.handling}
				<p class="error lang">{$errors['handling']}</p>
			</div>
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
			<div>
				<p class="lang">Parent Field for Add and Edit Screen:</p>
				{html_options name="parent_field" selected=$data.parent_field options=$parent_field_opt}

				<p class="error lang">{$errors['parent_field']}</p>
			</div>
		</div>
		{if $data.handling == 8}
			<div id="area_code">
				<div>
					<p class="lang">Subject:</p>
					<input type="text" name="subject" value="{$data.subject}">

					<p class="error lang">{$errors['subject']}</p>
				</div>
				<div>
					<p class="lang">Signiture:</p>
					<input type="text" name="signiture" value="{$data.signiture}">

					<p class="error lang">{$errors['signiture']}</p>
				</div>
			</div>
		{/if}
		{if $data.handling == 6}
			<div id="area_pdf">
				<div>
					<p class="lang">orientation:</p>
					{html_options name="pdf_orientation" selected=$data.pdf_orientation options=$pdf_orientation_opt}

					<p class="error lang">{$errors['pdf_orientation']}</p>
				</div>
				<div>
					<p class="lang">pagesize:</p>
					<input type="text" name="pdf_pagesize" value="{$data.pdf_pagesize}">
				</div>
				<div>
					<p class="lang">Font:</p>
					{html_options name="pdf_font" selected=$data.pdf_font options=$pdf_font_opt}

					<p class="error lang">{$errors['pdf_font']}</p>
				</div>
				<div>
					<p class="lang">page_margin_top:</p>
					<input type="text" name="page_margin_top" value="{$data.page_margin_top}">
				</div>
				<div>
					<p class="lang">page_margin_left:</p>
					<input type="text" name="page_margin_left" value="{$data.page_margin_left}">
				</div>
				<div>
					<p class="lang">page_margin_right:</p>
					<input type="text" name="page_margin_right" value="{$data.page_margin_right}">
				</div>
				<div>
					<p class="lang">page_margin_bottom:</p>
					<input type="text" name="page_margin_bottom" value="{$data.page_margin_bottom}">
				</div>
				<div>
					<p class="lang">pagenumber:</p>
					{html_options name="pagenumber" selected=$data.pagenumber options=$pagenumber_opt}

					<p class="error lang">{$errors['pagenumber']}</p>
				</div>
				<div>
					<p class="lang">pagenumber_firstpage:</p>
					{html_options name="pagenumber_firstpage" selected=$data.pagenumber_firstpage options=$pagenumber_firstpage_opt}

					<p class="error lang">{$errors['pagenumber_firstpage']}</p>
				</div>
				<div>
					<p class="lang">publish:</p>
					{html_options name="publish" selected=$data.publish options=$publish_opt}

					<p class="error lang">{$errors['publish']}</p>
				</div>
				<div>
					<p class="lang">img_grayscale:</p>
					{html_options name="img_grayscale" selected=$data.img_grayscale options=$img_grayscale_opt}

					<p class="error lang">{$errors['img_grayscale']}</p>
				</div>
				<div>
					<p class="lang">pagenumber_y_position:</p>
					<input type="text" name="pagenumber_y_position" value="{$data.pagenumber_y_position}">
				</div>
			</div>
		{/if}
	{else if $data.type == 2}
		<div id="area_code">
			<div>
				<p class="lang">Predefined Function:</p>
				<h4>{$predefined_function_opt[$data.predefined_function]}</h4>
			</div>
		</div>
		{if $data.predefined_function == 1}
			<!-- Login -->
			{include file="_after_status.tpl"}
		{else if $data.predefined_function == 2}
			<!-- Payment -->
			<div id="area_db">
				<div>
					<p class="lang">Menu Name:</p>
					<input type="text" name="menu_name" value="{$data.menu_name}">

					<p class="error lang">{$errors['menu_name']}</p>
				</div>
				<div>
					<p class="lang">Price:</p>
					<div class='flex'><input type="text" name="price" value="{$data.price}" style="width:200px;"> 
						<span style="margin: auto 5px;">{$currency}</span></div>

					<p class="error lang">{$errors['price']}</p>
				</div>
			</div>
			{include file="_after_status.tpl"}
		{elseif $data.predefined_function == 3}
			<!-- AI Training -->
			<div id="area_db">
				<div>
					<p class="lang">Title:</p>
					<input type="text" name="ai_title" value="{$data.ai_title}">

					<p class="error lang">{$errors['ai_title']}</p>
				</div>
				<div>
					<p class="lang">Text:</p>
					<textarea name="ai_text" class="wordcounter" data-counter_max="1000">{$data.ai_text}</textarea>
					<p class="error lang">{$errors['ai_text']}</p>
				</div>
			</div>
		{else if $data.predefined_function == 4}
			<!-- Create Account -->
			<div id="area_db">
				<div>
					<p class="lang">Menu Name:</p>
					<input type="text" name="menu_name" value="{$data.menu_name}">

					<p class="error lang">{$errors['menu_name']}</p>
				</div>
			</div>
				
			{include file="_after_status.tpl"}
		{/if}
	{/if}


	<!-- LIMIT BY User -->
	{include file="_limit_user.tpl"}

</form>
<div style="clear:both;"></div>
<div id="parameters_area">
	{if $data.type == 1}
		{include file="_parameters.tpl"}
	{/if}
</div>