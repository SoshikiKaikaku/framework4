<form id="email_format_email_format_add_form">


	<div>
		<p class="lang">Template Name:</p>
		<input type="text" name="template_name" value="{$post.template_name}">

		<p class="error lang">{$errors['template_name']}</p>
	</div>
	<div>
		<p class="lang">Key:</p>
		<input type="text" name="key" value="{$post.key}">

		<p class="error lang">{$errors['key']}</p>
	</div>
	<div>
		<p class="lang">Subject:</p>

		<input type="text" name="subject" value="{$post.subject}">
		<p class="error lang">{$errors['subject']}</p>
	</div>

	<div>
		<p class="lang">Body:</p>
		<textarea name="body">{$post.body}</textarea>

		<p class="error lang">{$errors['body']}</p>
	</div>

	<div>
		<button class="ajax-link lang btn_blue_style" data-form="email_format_email_format_add_form" data-class="{$class}" data-function="add_exe">Add</button>
	</div>
</form>

