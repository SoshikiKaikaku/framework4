<form id="ai_settings_ai_setting_add_form_pre">

	<input type="hidden" name="type" value="{$post.type}">
	<input type="hidden" name="code_type" value="{$post.code_type}">
	<input type="hidden" name="predefined_function" value="{$post.predefined_function}">

	<div>			
		<div class="type_form_area">
			<p class="type_design">{$predefined_function_opt[$post.predefined_function]}</p>
		</div>
	</div>
		
	{if $post.predefined_function == 5}
	    <div id="area_db">
			<div>
				<p class="lang">Manual Crawl:</p>
				{html_options name="manual_crawl" options=$manual_crawl_opt}

				<p class="error lang">{$errors['manual_crawl']}</p>
			</div>
			<div>
				<p class="lang">Sitemap:</p>
				{html_options name="sitemap" options=$sitemap_opt}

				<p class="error lang">{$errors['sitemap']}</p>
			</div>
	    </div>
	{/if}

	{if $post.predefined_function == 2}
		<div id="area_db">
			<div>
				<p class="lang">Menu Name:</p>
				<input type="text" name="menu_name" value="{$data.menu_name}">

				<p class="error lang">{$errors['menu_name']}</p>
			</div>
			<div>
				<p class="lang">Price:</p>
				<div class='flex'><input type="text" name="price" value="{$post.price}" style="width:200px;"> 
					<span style="margin: auto 5px;">{$currency}</span></div>

				<p class="error lang">{$errors['price']}</p>
			</div>
		</div>
	{/if}
	{if $post.predefined_function == 3}
		<div id="area_db">
			<div>
				<p class="lang">Title:</p>
				<input type="text" name="ai_title" value="{$post.ai_title}">

				<p class="error lang">{$errors['ai_title']}</p>
			</div>
			<div>
				<p class="lang">Text:</p>
				<textarea name="ai_text" class="wordcounter" data-counter_max="1000">{$post.ai_text}</textarea>
				<p class="error lang">{$errors['ai_text']}</p>
			</div>
		</div>
	{/if}
	{if $post.predefined_function == 4}
		<div id="area_db">
			<div>
				<p class="lang">Menu Name:</p>
				<input type="text" name="menu_name" value="{$data.menu_name}">

				<p class="error lang">{$errors['menu_name']}</p>
			</div>
			<div>
				<p class="lang">Message after execute.:</p>
				<input type="text" name="message_after_execute" value="{$data.message_after_execute}">

				<p class="error lang">{$errors['message_after_execute']}</p>
			</div>
		</div>
	{/if}
	
	<!-- LIMIT BY User -->
	{include file="_limit_user.tpl"}

	<div>
		<button class="ajax-link lang" data-form="ai_settings_ai_setting_add_form_pre" data-class="{$class}" data-function="add_exe">Add</button>
	</div>
</form>

