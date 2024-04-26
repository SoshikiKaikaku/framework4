<div style="display: flex; justify-content:space-between;">
	<p><b>Class: </b>{$post.dbclass}</p>
	<p><b>Table: </b>{$post.db}</p>
</div>

<form id="table_designer_2">
	<input type="hidden" name="dbclass" value="{$post.dbclass}" />
	<input type="hidden" name="db" value="{$post.db}" />
	<input type="hidden" name="parent_class" value="{$post.parent_class}" />
	<input type="hidden" name="parent_db" value="{$post.parent_db}" />
	<input type="hidden" name="level" value="{$level}" />

	<table style="margin-top:10px;" class="moredata" id="table_designer_tbl">
		<tr>
			<th style="width: 50%;">Field Name</th>
			<th style="width: 20%;">Data Type</th>
			<th>Length</th>
			<th></th>
		</tr>

		{assign var="field_count" value=0}
		{foreach $keys as $key}
			{assign var="field_count" value=$field_count+1}
			<tr>
				<td><input type="text" name="fields[]" value="{$key.0}" data-id="{$field_count}" /></td>
				<td>
					<select name="field_type[]" class="field_type" data-id="{$field_count}">
						<option value=""></option>
						<option value="T" {((trim($key.2)=='T')?'selected':'')}>Text</option>
						<option value="N" {((trim($key.2)=='N')?'selected':'')}>Number</option>
						<option value="F" {((trim($key.2)=='F')?'selected':'')}>Float</option>
					</select>
				</td>
				<td><input type="text" name="field_length[]" value="{$key.1}" data-id="{$field_count}" class="field_length" /></td>
				<td>
					<button class="listbutton delete" data-id="{$field_count}" style="float:right;color:black;margin:0px;width: 35px;
							height: 35px; border: solid 1px;"><span class="ui-icon ui-icon-trash"></span></button>
				</td>
			</tr>
		{/foreach}

		{* add empty rows if new db *}
		{if isset($post.new)}
			{assign var="field_count" value=$field_count+1}
			<tr>
				<td><input type="text" name="fields[]" value="" data-id="{$field_count}" /></td>
				<td>
					<select name="field_type[]" class="field_type" data-id="{$field_count}">
						<option value=""></option>
						<option value="T">Text</option>
						<option value="N">Number</option>
						<option value="F">Float</option>
					</select>
				</td>
				<td><input type="text" name="field_length[]" value="" data-id="{$field_count}" class="field_length" /></td>
				<td>
					<button class="listbutton delete" data-id="{$field_count}" style="float:right;color:black;margin:0px;width: 35px;
							height: 35px; border: solid 1px;"><span class="ui-icon ui-icon-trash"></span></button>
				</td>
			</tr>

			{assign var="field_count" value=$field_count+1}
			<tr>
				<td><input type="text" name="fields[]" value="" data-id="{$field_count}" /></td>
				<td>
					<select name="field_type[]" class="field_type" data-id="{$field_count}">
						<option value=""></option>
						<option value="T">Text</option>
						<option value="N">Number</option>
						<option value="F">Float</option>
					</select>
				</td>
				<td><input type="text" name="field_length[]" value="" data-id="{$field_count}" class="field_length" /></td>
				<td>
					<button class="listbutton delete" data-id="{$field_count}" style="float:right;color:black;margin:0px;width: 35px;
							height: 35px; border: solid 1px;"><span class="ui-icon ui-icon-trash"></span></button>
				</td>
			</tr>
		{/if}

	</table>
</form>

<hr style="clear: both;">

<p style="color: red; text-align: right;" id="table_designer_2_error">{$error}</p>


<button class="ajax-link" data-class="{$class}" data-function="table_designer_2_exe" data-form="table_designer_2" data-dbclass="{$post.dbclass}" data-db="{$post.db}" style="margin-top: 10px; padding: 10px; min-width: max-content;">
	Save
</button>

<button id="add_row" style="margin-top: 10px; padding: 10px; min-width: max-content; float:left;">
	Add a Field
</button>

<script>
	var field_count = {$field_count};
	$('#add_row').click(function () {
		field_count++;
		$('#table_designer_tbl').append(`
			<tr>
				<td><input type="text" name="fields[]" value="" data-id="` + field_count + `" /></td>
				<td>
					<select name="field_type[]" class="field_type" data-id="` + field_count + `">
						<option value=""></option>
						<option value="T">Text</option>
						<option value="N">Number</option>
						<option value="F">Float</option>
					</select>
				</td>
				<td><input type="text" name="field_length[]" value="" data-id="` + field_count + `" class="field_length" /></td>
				<td>
					<button class="listbutton delete" data-id="` + field_count + `" style="float:right;color:black;margin:0px;width: 35px;
					height: 35px; border: solid 1px;"><span class="ui-icon ui-icon-trash"></span></button>
				</td>
			</tr>
		`);
	});

	$(document).on('change', '.field_type', function () {
		let id = $(this).data('id');
		let val = $(this).val();
		if (val == 'N') {
			$("[data-id='" + id + "'].field_length").val("24");
		}
		if (val == 'F') {
			$("[data-id='" + id + "'].field_length").val("24");
		}
		if (val == 'T') {
			$("[data-id='" + id + "'].field_length").val("255");
		}
	});

	$(document).on('click', '.delete', function (e) {
		e.preventDefault();
		$(this).closest('tr').remove();
	});
</script>