
<div class="after_workflow_status_area">
		
		{if $data.predefined_function != 1}
		<div>
			<p class="lang">Message on chat:</p>
			<input type="text" name="message_after_execute" value="{$data.message_after_execute}">

			<p class="error lang">{$errors['message_after_execute']}</p>
		</div>
		{/if}
		
		{if $data.predefined_function == 4}
		<div>
			<p class="lang">Set user type:</p>
			{html_options name="change_after_type" id="change_after_type" options=$change_after_type_opt selected=$data.change_after_type}
			{html_options name="after_type" id="after_type" options=$user_type_opt selected=$data.after_type style="margin-top:10px;"}
		<script>
			var change_after_type_checkfunc = function () {
				if ($("#change_after_type").val() == 0) {
					$("#after_type").hide();
				} else {
					$("#after_type").show();
				}
			}
			$("#change_after_type").on("change", change_after_type_checkfunc);
			change_after_type_checkfunc();
		</script>
		</div>
		{/if}
		
		<div>
			<p class="lang">Change workflow status:</p>
			{html_options name="change_after_workflow_status" id="change_after_workflow_status" options=$change_after_workflow_status_opt selected=$data.change_after_workflow_status}
			{html_options name="after_workflow_status" id="after_workflow_status" options=$workflow_status_opt selected=$data.after_workflow_status style="margin-top:10px;"}
		<script>
			var change_after_workflow_status_checkfunc = function () {
				if ($("#change_after_workflow_status").val() == 0) {
					$("#after_workflow_status").hide();
				} else {
					$("#after_workflow_status").show();
				}
			}
			$("#change_after_workflow_status").on("change", change_after_workflow_status_checkfunc);
			change_after_workflow_status_checkfunc();
		</script>
		</div>	

</div>