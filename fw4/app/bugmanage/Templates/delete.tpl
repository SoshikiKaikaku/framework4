<form id="bugmanage_bugs_delete_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">

	<span class="lang">Are you sure to delete the following Bug?</span>
	<p>
		<b>

			{$data.id}

		</b>
	</p>
	<br>
	<p class="lang">If you perform this process, it will not be restored. Do you want to process it?</p>

</form>
<button class="ajax-link lang" data-form="bugmanage_bugs_delete_form_{$data.id}" data-class="{$class}" data-function="delete_exe" style="float:right;">Delete</button>


