
<h5>Are you sure about releasing the project?</h5>

<p>Copy from: {$appdir_test}</p>

{foreach $filelist as $file}
	<p>{$file}</p>
{/foreach}

<button class="ajax-link" data-class="release" data-function="exe" style="float:right;">Execute</button>

