<form id="email_format_email_format_edit_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">

	<div>
		<p class="lang">Template Name:</p>

		<input type="text" name="template_name" value="{$data.template_name}">
		<p class="error lang">{$errors['template_name']}</p>
	</div>

	<div>
		<p class="lang">Key:</p>

		<input type="text" name="key" value="{$data.key}">
		<p class="error lang">{$errors['key']}</p>
	</div>

	<div>
		<p class="lang">Subject:</p>
		<input type="text" name="subject" value="{$data.subject}">

		<p class="error lang">{$errors['subject']}</p>
	</div>
	<div>
		<p class="lang">Body:</p>

		<textarea name="body">{$data.body}</textarea>
		<p class="error lang">{$errors['body']}</p>
	</div>

	<div>
		<button class="ajax-link lang" data-form="email_format_email_format_edit_form_{$data.id}" data-class="{$class}" data-function="edit_exe">Update</button>
	</div>

</form>

