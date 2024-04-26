<div style="display: flex; justify-content: space-between;">
	<p><b>Class:</b> {$post.dbclass}</p>
	<p><b>Table:</b> {$post.db}</p>
	<p><b>Field Name:</b> {$post.field_name}</p>
</div>

<form id="table_designer_2">
	<input type="hidden" name="dbclass" value="{$post.dbclass}" />
	<input type="hidden" name="db" value="{$post.db}" />
	<input type="hidden" name="field_name" value="{$post.field_name}" />
	<input type="hidden" name="field_old_name" value="{$post.field_old_name}" />

	<table style="margin-top:10px;" class="moredata" id="table_designer_tbl">
		<tr>
			<th style="width: 40%;">Value</th>
			<th style="width: 50%;">Text</th>
			<th></th>
		</tr>

		{assign var="field_count" value=0}
		{foreach $params as $key => $value}
			{assign var="field_count" value=$field_count+1}
			<tr>
				<td><input type="number" name="param_value[]" value="{$key}" data-id="{$field_count}" /></td>
				<td><input type="text" name="param_name[]" value="{$value}" data-id="{$field_count}" /></td>
				<td>
					<button class="listbutton delete" data-id="{$field_count}" style="float:right;color:black;margin:0px;width: 35px;
							height: 35px; border: solid 1px;"><span class="ui-icon ui-icon-trash"></span></button>
				</td>
			</tr>
		{/foreach}

	</table>
</form>

<hr style="clear: both;">

<p style="color: red; text-align: right;" id="table_designer_2_error">{$error}</p>


<button class="ajax-link" data-class="{$class}" data-function="code_generator_set_params_exe" data-form="table_designer_2"  style="margin-top: 10px; padding: 10px; min-width: max-content;">
	Save
</button>

<button id="add_row" style="margin-top: 10px; padding: 10px; min-width: max-content; float:left;">
	Add a Option
</button>

<script>
	var field_count = {$field_count};
	$('#add_row').click(function () {
		field_count++;
		$('#table_designer_tbl').append(`
			<tr>
				<td><input type="number" name="param_value[]" value="` + field_count + `" data-id="` + field_count + `" /></td>
				<td><input type="text" name="param_name[]" value="" data-id="` + field_count + `" /></td>
				<td>
					<button class="listbutton delete" data-id="` + field_count + `" style="float:right;color:black;margin:0px;width: 35px;
					height: 35px; border: solid 1px;"><span class="ui-icon ui-icon-trash"></span></button>
				</td>
			</tr>
		`);
	});

	$(document).on('click', '.delete', function (e) {
		e.preventDefault();
		$(this).closest('tr').remove();
	});
</script>