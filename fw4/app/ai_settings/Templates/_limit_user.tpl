
<div class="security_parameters_area">
	{if $data.predefined_function == 1}
		<div>
			<p class="lang">Work on:</p>
			Public Side
		</div>	
		<input type="hidden" name="work_on" value="3">
	{else}
		{if $data.predefined_function == 4}
			<div>
				<p class="lang">Work on:</p>
				Public Side
			</div>
			<input type="hidden" name="work_on" value="3">
		{else}
			<div>
				<p class="lang">Work on:</p>
				{html_options name="work_on" selected=$data.work_on options=$workon_opt}
			</div>
		{/if}

		<!-- Limit by User Type from here-->
		<div>
			<p class="lang">Limited by user types:</p>
			{html_options id="limit_user_type" name="limit_user_type" selected=$data.limit_user_type options=$limit_user_type_opt}
		</div>
		<div id="user_type_area">
			<p class="lang">Allow User Type:</p>
			{foreach $user_type_opt as $key=>$option}
				<div>
					<input type="checkbox" id="user_type_{$key}" name="user_type[]" value="{$key}" {$is_checked}>
					<label for="user_type_{$key}">{$option}</label>
				</div>
			{/foreach}
		</div>
		<script>
			var user_type_checkfunc = function () {
				if ($("#limit_user_type").val() == 0) {
					$("#user_type_area").hide();
				} else {
					$("#user_type_area").show();
				}
			}
			$("#limit_user_type").on("change", user_type_checkfunc);
			user_type_checkfunc();
		</script>
		<!-- Limit by User Type over here-->



		<!-- Limit by Workflow from here-->
		<div>
			<p class="lang">Limited by workflow status of users:</p>
			{html_options id="limit_workflow" name="limit_workflow" selected=$data.limit_workflow options=$limit_workflow_opt}
			<p class="error lang">{$errors['limit_workflow']}</p>
		</div>
		<div id="workflow_status_area">
			{if count($workflow_status_opt) == 0}
				<p class="lang error">You haven't set workflow status. This function will not work.</p>
			{else}
				<p class="lang">Allow Workflow Status:</p>

				{foreach $workflow_status_opt as $key=>$option}
					{if $data['workflow_status'] && in_array($key,$data['workflow_status'])}
						{assign is_checked checked}
					{else}
						{assign is_checked ''}
					{/if}
					<div>
						<input type="checkbox" id="workflow_status_{$key}" name="workflow_status[]" value="{$key}" {$is_checked}>
						<label for="workflow_status_{$key}">{$option}</label>
					</div>
				{/foreach}

			{/if}


		</div>
		<script>
			var workflow_checkfunc = function () {
				if ($("#limit_workflow").val() == 0) {
					$("#workflow_status_area").hide();
				} else {
					$("#workflow_status_area").show();
				}
			}
			$("#limit_workflow").on("change", workflow_checkfunc);
			workflow_checkfunc();
		</script>
		<!-- Limit by Workflow over here -->
	{/if}
</div>