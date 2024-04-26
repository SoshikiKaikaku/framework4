<form id="bughistory_bug_history_add_form">

	<input type="hidden" name="bugs_id" value="{$post.bugs_id}">
	Date: <input type="text" name="date" value="{$post.date}" class="datepicker">
	<p class="error">{$errors['date']}</p>

	Memo: <textarea name="memo" style="width: 100%;">{$post.memo}</textarea>
	<p class="error">{$errors['memo']}</p>

	Name: <input type="text" name="name" value="{$post.name}">
	<p class="error">{$errors['name']}</p>

	<div>
		<button class="ajax-link lang" data-form="bughistory_bug_history_add_form" data-class="{$class}" data-function="add_exe">Add</button>
	</div>

</form>

