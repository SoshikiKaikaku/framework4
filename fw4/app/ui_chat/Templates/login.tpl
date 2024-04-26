
<form id="login_form">
	<input type="hidden" name="class" value="{$class}">
	<input type="hidden" name="function" value="check">
	<input type="hidden" name="public" value="true">
	<input type="hidden" name="ai_setting_id" value="{$ai_setting_id}">
	
	<div class="form-wrap form-wrap-validation has-error">
		<p class="lang">Login ID</p>
		<input type="text" name="login_id" value="{$login_id}" autocomplete="username">
		<p class="lang" style="margin-top:10px;">Password</p>
		<input type="password" name="password" value="{$password}" autocomplete="current-password">
		<p id="err_password" class="error">{$err_password}</p>

	</div>	

	<button class="ajax-link lang" data-form="login_form" style="float:right;margin-top:18px;">Login</button>

</form>
