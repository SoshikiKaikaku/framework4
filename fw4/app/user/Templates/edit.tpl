<form id="edit_form">

	<p class="lang">Login ID</p>
	<h5>{$data.login_id}</h5>

	<p class="lang">Status</p>
	{html_options name="status" options=$arr_status selected={$data.status}}

	<p class="lang">Name</p>
	<input type="text" name="name" value="{$data.name}">

	<p class="lang">Password</p>
	<input type="text" name="password" value="{$data.password}">
	<p class="error">{$err_password}</p>

	<p class="lang">email</p>
	<input type="text" name="email" value="{$data.email}">
	<p class="error">{$err_email}</p>
	
	<p class="lang">Type</p>
	{html_options name="type" options=$user_type_opt selected=$data.type style="width:200px;"}
	
	<p class="lang">Workflow Status</p>
	{html_options name="workflow_status" options=$workflow_status_opt selected=$data.workflow_status style="width:200px;"}
</form>

<button class="ajax-link lang" data-class="user" data-function="edit_exe" data-form="edit_form" data-id="{$data.id}">Submit</button>	