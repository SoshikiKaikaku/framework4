<form id="ai_dbs_ai_db_add_form">


	<div>
		<p class="lang">Table Name:</p>
		<input type="text" name="tb_name" value="{$post.tb_name}">
		<p class="error lang">{$errors['tb_name']}</p>
	</div>

	<div>
		<p class="lang">What information do you keep in this table?(Example: "Customer Information", "Customer History")</p>
		<input type="text" name="description" value="{$post.description}">
	</div>

	<div>
		<p class="lang">Parent Table:</p>
		{html_options name="parent_tb_id" options=$parents_opt selected=$post["parent_tb_id"]}
	</div>


	<div>
		<button class="ajax-link lang" data-form="ai_dbs_ai_db_add_form" data-class="{$class}" data-function="add_exe">Add</button>
	</div>
</form>

