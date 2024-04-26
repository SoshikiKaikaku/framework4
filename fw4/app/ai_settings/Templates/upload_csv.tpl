<form id="upload_csv_form">
	
        <p class="lang">Coose a CSV File</p>
	<input type="file" name="ai_training_csv">
	<p class="error lang">{$errors['ai_training_csv']}</p>
	<p class="lang">CSV Format : First line is title. </p>
	<img src="app.php?class={$class}&function=image_sample" style="width:60%">
	
	<p class="lang">Character Code</p>
	{html_options name="code" options=$code_list selected=$post.code}
	
	<p class="lang">Upload Option</p>
	{html_options name="upload" options=$upload_list selected=$post.upload}

	<button class="ajax-link lang" data-form="upload_csv_form" data-class="{$class}" data-function="upload_csv_confirm">Update</button>
	
	<div style="height:100px;"></div>
</form>

