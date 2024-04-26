<form id="bugmanage_bugs_add_form">

	<div class="addstatus">
		<p class="lang">Status:</p>
		{html_options name="status" options=$status selected=$post.status class="lang"}

		<p class="error lang">{$errors['status']}</p>
	</div>

	<div class="addprio">
		<p class="lang">Priority:</p>
		{html_options name="priority" options=$priority selected=$post.priority class="lang"}

		<p class="error lang">{$errors['priority']}</p>
	</div>

	<div class="bugimage">
		<p class="lang">File Upload:</p><input type="file" name="image" class="fr_image_paste">
		<p class="error lang">{$errors['image']}</p>
	</div>

	<div class="desk-jp">
		<p class="lang">Japanees:</p>
		<textarea name="desk_japanees" class="wordcounter" data-counter_max="2000">{$post.desk_japanees}</textarea>
		<p class="error lang">{$errors['desk_japanees']}</p>

		<button class="ajax-link lang" data-form="bugmanage_bugs_add_form" data-class="{$class}" data-function="translate" style="float: left; margin-top:0px;">Traslate</button>

	</div>

	<div class="desk-en">
		<p class="lang">English:</p>
		<div id="desk_english"><textarea name="desk_english" class="wordcounter" data-counter_max="2000">{$post.desk_english}</textarea></div>
		<p class="error lang">{$errors['desk_english']}</p>
	</div>

	<div>

	</div>
</form>