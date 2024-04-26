

<button class="ajax-link lang" data-class="{$class}" data-function="json_upload" style="margin-top: 0px;">JSON Upload</button>
<button class="download-link lang" data-filename="system_setting.json" data-class="{$class}" data-function="json_download" style="margin-top: 0px;">JSON Download</button>  

<form id="setting_form">

	<h5>Mode</h5>
	<table>
		<tr>
			<td>{html_options name="force_testmode" options=$arr_force_testmode selected=$setting.force_testmode}</td>
		</tr>
	</table>


	<h5 style="margin-top:20px;">Rewrite for Root Access</h5>
	<table>
		<tr>
			<td>Class Name (default:login)<input type="text" name="rewrite_rule_root" value="{$setting.rewrite_rule_root}" style="width:200px;"></td>
			<td>Function:<input type="text" name="rewrite_rule_function" value="{$setting.rewrite_rule_function}" style="width:200px;"></td>
		</tr>
	</table>
	Class Name (default:login) &nbsp;&nbsp;&nbsp;function: page

	<h5 style="margin-top:20px;">Startup Class </h5>
	<p>It will be called automatically when you login to management side.</p>
	<table>
		<tr>
			<td>Class:<input type="text" name="startup_class1" value="{$setting.startup_class1}" style="width:200px;"></td>
			<td>Function:<input type="text" name="startup_function1" value="{$setting.startup_function1}" style="width:200px;"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">Mail Server Setting</h5>
	<table>
		<tr>
			<td>Mail Address (for from)</td>
			<td><input type="text" name="smtp_from" value="{$setting.smtp_from}"></td>
		</tr>
		<tr>
			<td>Mail Server</td>
			<td><input type="text" name="smtp_server" value="{$setting.smtp_server}"></td>
		</tr>
		<tr>
			<td>Mail Port</td>
			<td><input type="text" name="smtp_port" value="{$setting.smtp_port}"></td>
		</tr>
		<tr>
			<td>Mail User</td>
			<td><input type="text" name="smtp_user" value="{$setting.smtp_user}"></td>
		</tr>
		<tr>
			<td>Mail Password</td>
			<td><input type="text" name="smtp_password" value="{$setting.smtp_password}"></td>
		</tr>
		<tr>
			<td>SMTPSecure</td>
			<td>{html_options name="smtp_secure" options=$arr_smtp_secure selected=$setting.smtp_secure}</td>
		</tr>
		<tr>
			<td>Email for testing</td>
			<td><input type="text" name="smtp_email_test" value="{$setting.smtp_email_test}"></td>
		</tr>

	</table>
	<button class="ajax-link lang" data-class="setting" data-function="update" data-form="setting_form" data-send_test_mail="1">Submit and send a test mail</button>

	<h5 style="margin-top:20px;">Vimeo Setting</h5>
	<table>
		<tr>
			<td>Client_id</td>
			<td><input type="text" name="vimeo_client_id" value="{$setting.vimeo_client_id}"></td>
		</tr>
		<tr>
			<td>Client Secret</td>
			<td><input type="text" name="vimeo_client_secret" value="{$setting.vimeo_client_secret}"></td>
		</tr>
		<tr>
			<td>Access Token</td>
			<td><input type="text" name="vimeo_access_token" value="{$setting.vimeo_access_token}"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">SQUARE Setting</h5>
	<table>
		<tr>
			<td>Application ID</td>
			<td><input type="text" name="square_application_id" value="{$setting.square_application_id}"></td>
		</tr>
		<tr>
			<td>Access Token</td>
			<td><input type="text" name="square_access_token" value="{$setting.square_access_token}"></td>
		</tr>
		<tr>
			<td>Location ID</td>
			<td><input type="text" name="square_location_id" value="{$setting.square_location_id}"></td>
		</tr>
		<tr>
			<td>Currency</td>
			<td>{html_options name="currency" options=$currency_list selected=$setting.currency}</td>
		</tr>
	</table>
	<p class="ajax-link lang" data-class="setting" data-function="square" style="color:blue;text-decoration: underline;">Square Test (100 Yen)</p>

	<h5 style="margin-top:20px;">SVN Setting</h5>
	<table>
		<tr>
			<td>SVN Repo URL</td>
			<td><input type="text" name="svn_repo_url" value="{$setting.svn_repo_url}"></td>
		</tr>
		<tr>
			<td>SVN Username</td>
			<td><input type="text" name="svn_username" value="{$setting.svn_username}"></td>
		</tr>
		<tr>
			<td>SVN Password</td>
			<td><input type="text" name="svn_password" value="{$setting.svn_password}"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">Google Settings</h5>
	<table>
		<tr>
			<td>API Key</td>
			<td><input type="text" name="api_key_map" value="{$setting.api_key_map}"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">Chat GPT Settings</h5>
	<table>
		<tr>
			<td>API Key</td>
			<td><input type="text" name="chatgpt_api_key" value="{$setting.chatgpt_api_key}"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">Language</h5>
	<table>
		<tr>
			<td>Priority</td>
			<td>{html_options name="lang_priority" options=$arr_lang_priority selected=$setting.lang_priority}</td>
		</tr>
		<tr>
			<td>Default Language</td>
			<td>{html_options name="lang_default" options=$arr_lang selected=$setting.lang_default}</td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">Bug Report Setting</h5>
	<table>
		<tr>
			<td>Bug report server</td>
			<td><input type="text" name="bug_report_server" value="{$setting.bug_report_server}"></td>
		</tr>

	</table>

	<h5 style="margin-top:20px;">Encrypt/Decrypt</h5>
	<table>
		<tr>
			<td>Secret</td>
			<td><input type="text" name="secret" value="{$setting.secret}"></td>
		</tr>
		<tr>
			<td>IV</td>
			<td><input type="text" name="iv" value="{$setting.iv}"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">Robots.txt</h5>
	<table>
		<tr>
			<td>robots.txt</td>
			<td><textarea name="robots">{$setting.robots}</textarea></td>
		</tr>
	</table>
		
	<h5 style="margin-top:20px;">Viewport</h5>
	<table>
		<tr>
			<td>Management Side</td>
			<td><input type="text" name="viewport_base" value="{$setting.viewport_base}"></td>
		</tr>
		<tr>
			<td>Public Side</td>
			<td><input type="text" name="viewport_public" value="{$setting.viewport_public}"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">System Name</h5>
	<table>
		<tr>
			<td>System Name</td>
			<td><input type="text" name="system_name" value="{$setting.system_name}"></td>
		</tr>
		<tr>
			<td>System Tag Line</td>
			<td><input type="text" name="system_tag_line" value="{$setting.system_tag_line}"></td>
		</tr>
		<tr>
			<td>Login Logo</td>
			<td><input type="file" name="login_logo" class="fr_image_paste" data-text="Image Uploader">
				<br /><button class="ajax-link" data-class="{$class}" data-function="delete_login_logo" style="margin-top:0px;">Delete Login Logo</button>
			</td>
		</tr>
	</table>

</form>


<div style="clear:both;margin-bottom:30px;"></div>