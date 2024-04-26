<form id="form_delete_child_db_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">
	<input type="hidden" name="dbclass" value="{$data.parent_class}">
	<input type="hidden" name="db" value="{$data.parent_table}">
	<input type="hidden" name="level" value="{$level}">

	Delete the following data

	<p><b>{$data.child_class} \ {$data.child_table}</b></p>

</form>

<button class="ajax-link" data-form="form_delete_child_db_{$data.id}" data-class="{$class}" data-function="delete_child_db_exe">delete</button>