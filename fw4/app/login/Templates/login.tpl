<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="user-scalable=1">
		{include file="{$base_template_dir}/publicsite_header.tpl"}
		<link rel="icon" href="/images/favicon.ico" type="image/x-icon" id="favicon">
		<title>Login : Focus Business Platform</title>
	</head>
	<body>
		<article class="class_style_{$class}">


			<div id="login_area">

				<div id="form"></div>	

				<div style="clear:both;"></div>

				<p style="margin-top:50px;"></p>
				{foreach $login_list as $id=>$password}
					<div class="quick_login">
						<form method="post" action="app.php" style="overflow:hidden;height:auto;display: block;">
							<input type="hidden" name="class" value="login">
							<input type="hidden" name="function" value="check">
							<input type="hidden" name="login_id" value="{$id}">
							<input type="hidden" name="password" value="{$password}">
							<p style="display:block;width:100%;float:left;line-height: 33px;">{$id}</p>
							<div style="width:100%;">
								{html_radios name="testserver" options=$arr_testserver selected=$testserver_flg class="lang"}
							</div>
							<button class="loginbutton lang" class="button button-primary" name="cmd" style="float:right;margin-top:0px;">Login</button>
						</form>
					</div>

				{/foreach}


			</div>


		</article>
		{include file="{$base_template_dir}/publicsite_footer.tpl"}
	</body>

	<script>
		$(function () {
			var fd = new FormData();
			fd.append("class", "login");
			fd.append("function", "login_form");
			appcon("app.php", fd);
		});
	</script>

</html>


