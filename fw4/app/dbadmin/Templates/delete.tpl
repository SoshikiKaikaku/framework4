<form id="form_delete{$data.id}">

	<input type="hidden" name="dbclass" value="{$post.dbclass}" />
	<input type="hidden" name="db" value="{$post.db}" />
	<input type="hidden" name="id" value="{$post.id}">

	Delete the following data

	<p><b>{$post.id}</b></p>

</form>

<button class="ajax-link" data-form="form_delete{$data.id}" data-class="{$class}" data-function="delete_exe">delete</button>