<form id="bughistory_bug_history_edit_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">
	<input type="hidden" name="bugs_id" value="{$data.bugs_id}">
	Date: <input type="text" name="date" value="{$data.date}" class="datepicker">
	<p class="error">{$errors['date']}</p>

	Memo: <textarea name="memo" style="width: 100%;">{$data.memo}</textarea>
	<p class="error">{$errors['memo']}</p>

	Name: <input type="text" name="name" value="{$data.name}">
	<p class="error">{$errors['name']}</p>


	<div>
		<button class="ajax-link lang" data-form="bughistory_bug_history_edit_form_{$data.id}" data-class="{$class}" data-function="edit_exe">Update</button>
	</div>
</form>


