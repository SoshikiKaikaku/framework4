<form id="ai_dbs_ai_db_delete_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">

	<span class="lang">Delete the following Ai Setting</span>
	<p>
		<b>

			{$data.tb_name}
		</b>
	</p>

	<br>
	<p class="lang">If you perform this process, it will not be restored. Do you want to process it?</p>
</form>

<button class="ajax-link lang" data-form="ai_dbs_ai_db_delete_form_{$data.id}" data-class="{$class}" data-function="delete_exe">Delete</button>

