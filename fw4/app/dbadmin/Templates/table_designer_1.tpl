<form id="form_update{$data.id}">

	<input type="hidden" name="dbclass" value="{$post.dbclass}" />
	<input type="hidden" name="db" value="{$post.db}" />
	<input type="hidden" name="parent_class" value="{$post.parent_class}" />
	<input type="hidden" name="parent_db" value="{$post.parent_db}" />
	<input type="hidden" name="level" value="{$level}" />

	<div>
		<p>Class Name</p>
		<input type="text" name="class_name" value="{$post.class_name}">	
	</div>

	<div>
		<p>Table Name</p>
		<input type="text" name="table_name" value="{$post.table_name}">
	</div>

	{if isset($error)}
		<p style="color: red;">{$error}</p>
	{/if}

	<button class="ajax-link" data-form="form_update{$data.id}" data-class="{$class}" data-function="table_designer_1_exe">Submit</button>

</form>