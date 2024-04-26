<form id="input_form" style="width:100%;">

    <table>
        <tr>
            <td class="lang">Name</td>
            <td><input type="text" name="name" value="{$data.name}" style="width:400px;"></td>
        </tr>

        <tr>
            <td class="lang">Login ID</td>
            <td><input type="text" name="login_id" value="{$data.login_id}" style="width:400px;">
                <p class="error">{$err_login_id}</p>
            </td>
        </tr>
        <tr>
            <td class="lang">Password</td>
            <td><input type="text" name="password" value="{$data.password}" style="width:400px;">
                <p class="error">{$err_password}</p>
            </td>
        </tr>

		<tr>
			<td class="lang">Email</td>
			<td><input type="text" name="email" value="{$data.email}" style="width:400px;">
				<p class="error">{$err_email}</p>
			</td>
		</tr>


        <tr>
            <td class="lang">Type</td>
            <td>{html_options name="type" options=$user_type_opt selected=$data.type style="width:400px;"}</td>
        </tr>
        <tr>
            <td class="lang">Workflow Status</td>
            <td>{html_options name="workflow_status" options=$workflow_status_opt selected=$data.workflow_status style="width:400px;"}</td>
        </tr>


        <p></p>

    </table>


</form>

<button class="ajax-link lang" data-class="user" data-function="append_exe" data-form="input_form">Submit</button>