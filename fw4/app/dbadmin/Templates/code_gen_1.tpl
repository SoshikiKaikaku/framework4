<form id="code_gen_step_1_form">

	<input type="hidden" name="dbclass" value="{$post.dbclass}" />
	<input type="hidden" name="db" value="{$post.db}" />
	<input type="hidden" name="parent_class" value="{$parent_class}" />
	<input type="hidden" name="parent_db" value="{$parent_db}" />
	<input type="hidden" name="level" value="{$level}" />

	<div style="display: flex; justify-content:space-between; background: aqua; padding: 5px;">
		<p><b>Level:</b> {$level} {($level>1)?"(Child of $parent_class class)":""}</p>
		<p><b>Class:</b> {$post.dbclass}</p>
		<p><b>Table:</b> {$post.db}</p>
	</div>

	<table style="margin-top:20px;" class="moredata">
		<thead>
			<tr>
				<th width="40%">Field</th>
				<th>List</th>
				<th>Add</th>
				<th>Edit</th>
				<th>Validate</th>
				<th>Search</th>
				<th>Delete</th>
				<th></th>
			</tr>
		</thead>

		<tbody>
			{assign var="field_count" value=0}
			{foreach $keys as $key}		
				{assign var="field_count" value=$field_count+1}

				{assign var="field_name" value=$key.0}
				{assign var="field_setting" value=$table_settings[$field_name]}
				{* difine field_type *}
				{if empty($field_setting['field_type'])}
					{if trim($key.2)=='N'}
						{assign var="field_type" value="number"}
					{elseif trim($key.2)=='F'}
						{assign var="field_type" value="float"}
					{else}
						{assign var="field_type" value="text"}
					{/if}
				{else}
					{assign var="field_type" value=$field_setting['field_type']}
				{/if}

				{assign var="element_disabled" value=false}
				{assign var="delete_disabled" value=false}
				{if strpos($field_name, '_id') !== false || strpos($field_name, 'sort') !== false}
					{assign var="element_disabled" value=true}
					{assign var="delete_disabled" value=true}
				{/if}
				{if $field_name=='id'}
					{assign var="delete_disabled" value=true}
				{/if}
				{if $options['page_style']=='weekly_calendar' || $options['page_style']=='monthly_calendar'}
					{if strpos($field_name, 'start_time') !== false || strpos($field_name, 'end_time') !== false || strpos($field_name, 'scheduled_date') !== false}
						{assign var="element_disabled" value=true}
						{assign var="delete_disabled" value=true}
					{/if}
				{/if}

				<tr data-id="{$field_count}" class="field_tr">
					{* <input type="hidden" name="fields[]" value="{$field_name}"> *}
					<td>
						<div>
							{if !$element_disabled && $field_name!='id'}
								<div style="display:flex; justify-content:space-between; border: #baa1a1 solid 1px;">
									<div style="width:50%;">
										<input type="text" name="fields[]" value="{((strpos($field_name, 'new_codegen_') !== false)?"":$field_name)}" style="" class="field_name_input" data-field_name_input="{$field_name}" />
									</div>
									<div style="width:25%;">
										<select name="types[{$field_name}]" data-element="{$field_name}" style="width:100%; padding-bottom:6px;" class="element_type" data-id="{$field_count}">
											<option value="text" {($field_type=='text')?'selected':''}>Text</option>
											<option value="textarea" {($field_type=='textarea')?'selected':''}>Textarea</option>
											<option value="number" {($field_type=='number')?'selected':''}>Number</option>
											<option value="float" {($field_type=='float')?'selected':''}>Float</option>
											<option value="select" {($field_type=='select')?'selected':''}>Select</option>
											<option value="date" {($field_type=='date')?'selected':''}>Date</option>
											<option value="checkbox" {($field_type=='checkbox')?'selected':''}>Checkbox</option>
											<option value="radio" {($field_type=='radio')?'selected':''}>Radio</option>
											<option value="file" {($field_type=='file')?'selected':''}>File</option>
										</select>
									</div>
									<div style="width:25%;">
										<input type="text" name="field_length[]" value="{$key.1}" data-id="{$field_count}" class="field_length" />
									</div>
								</div>
							{else}
								<p style="padding: 5px;">{$field_name} ({$key.2})</p>
								<input type="hidden" name="fields[]" value="{$field_name}" />
								<input type="hidden" name="field_type[]" value="{trim($key.2)}" />
								<input type="hidden" name="field_length[]" value="{$key.1}" />
							{/if}
						</div>

						{if !$element_disabled && $field_name!='id'}


							<div style="width:100%; margin-top: 5px;" class="{($field_type=='select'||$field_type=='checkbox'||$field_type=='radio')?'':'hidden'}">

								{* add params *}
								<button type="button" class="list-large-btn add_param_btn dontFocusOut" data-class="{$class}" data-function="code_generator_set_params" data-dbclass="{$post.dbclass}" data-db="{$post.db}" data-field_name="{$field_name}" data-field_old_name="{$field_name}" style="float: left;"><span class="ui-icon ui-icon-pencil"></span></button>

								{assign var="params_str" value=''}
								{foreach $params[$field_name] as $k => $param}
									{assign var="params_str" value="`$params_str` `$k`:`$param` &nbsp; &nbsp"}
								{/foreach}

								<p class="element_params" id="element_params_{{$field_name}}" style="padding-left:40px;">
									{* {if !$params_exist[$field_name]}
									<option value="" disabled selected>Add Params</option>
									{/if} *}
									{substr($params_str,0,200) nofilter}
								</p>

							</div>
						{/if}
					</td>
					<td>
						<input type="checkbox" name="list[{$field_name}]" value="{$field_name}" {($field_setting['list_flg'])?'checked':''} {($element_disabled)?"disabled":""}>
					</td>
					<td>
						<input type="checkbox" name="add[{$field_name}]" value="{$field_name}" {($field_name=='id')?"disabled":""} {($element_disabled)?"disabled":""} {($field_setting['add_flg'])?'checked':''}>
					</td>
					<td>
						<input type="checkbox" name="edit[{$field_name}]" value="{$field_name}" {($field_name=='id')?"disabled":""} {($field_setting['edit_flg'])?'checked':''} {($element_disabled)?"disabled":""}>
					</td>
					<td>
						<input type="checkbox" name="validation[{$field_name}]" value="{$field_name}" class="validation" {($field_name=='id')?"disabled":""} {($field_setting['validate_flg'])?'checked':''} {($element_disabled)?"disabled":""}>
						<select name="file_types[{$field_name}]" id="" style="width:70%;" class="validation_options {($field_setting['validate_flg']&&($field_setting['field_type']=='file'))?'':'hidden'}">
							<option value="any" {($field_setting['validation_file_types']=='any')?'selected':''}>Any</option>
							<option value="image" {($field_setting['validation_file_types']=='image')?'selected':''}>Images</option>
							<option value="pdf" {($field_setting['validation_file_types']=='pdf')?'selected':''}>PDF</option>
							<option value="video" {($field_setting['validation_file_types']=='video')?'selected':''}>Videos</option>
						</select>
						<select name="text_types[{$field_name}]" id="" style="width:70%;" class="validation_options {($field_setting['validate_flg']&&($field_setting['field_type']=='text'))?'':'hidden'}">
							<option value="any" {($field_setting['validation_text_types']=='any')?'selected':''}>Any</option>
							<option value="number" {($field_setting['validation_text_types']=='number')?'selected':''}>Number</option>
							<option value="email" {($field_setting['validation_text_types']=='email')?'selected':''}>EMail</option>
							<option value="text" {($field_setting['validation_text_types']=='text')?'selected':''}>Text Only</option>
						</select>
					</td>
					<td>
						<input type="checkbox" name="search[{$field_name}]" value="{$field_name}" {($field_setting['search_flg'])?'checked':''} {($element_disabled)?"disabled":""}>
					</td>
					<td>
						<input type="radio" name="delete" value="{$field_name}" style="width: 20px; height: 15px;" {($field_setting['delete_flg'])?'checked':''} {($element_disabled)?"disabled":""} data-delete_name="{$field_name}">
					</td>
					<td>
						{* check all *}
						<button type="button" class="listbutton col_name" data-id="{$field_count}" style="float:right;color:black;margin:0px;width: 30px; height: 30px; border: solid 1px #9797b6;" data-col_name="{$field_name}" id=""><span class="ui-icon ui-icon-circle-check"></span></button>

						{* delete *}
						{if !$delete_disabled}
							<button class="listbutton delete_field" data-id="{$field_count}" style="float:right;color:black;margin:0px;width: 30px; height: 30px; border: solid 1px #9797b6; margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
							{/if}
					</td>
				</tr>
			{/foreach}

		</tbody>
	</table>

	<div>
		<button class="ajax-link" data-class="{$class}" data-function="add_new_field" data-form="code_gen_step_1_form" style="color:black;margin:0px;border: solid 1px #9797b6; background:#95af51; float: left; margin: 10px;" alt="Add New Column">Add New Column</button>
	</div>

	<hr style="clear:both;" />

	<div style="background: #efefef; padding: 10px; margin-top: 10px;">
		<p style="text-align:center; font-weight:bold; margin-bottom: 5px;"><u>Options</u></p>

		<div style="display:flex; justify-content:space-around;">
			<div>
				<label>
					<input class="db_options_radio" type="radio" name="page_style" value="edit_page" style="width: 20px; height: 15px;" {(($options['page_style']=='edit_page')?'checked':'')}> List & Edit Window
					<br>
					<img style="height:150px;" src="/app/images/codegen_list.png" alt="">	
				</label>
			</div>
			<div>
				<label>
					<input class="db_options_radio" type="radio" name="page_style" value="edit_inline" style="width: 20px; height: 15px;" {(($options['page_style']=='edit_inline')?'checked':'')}> Edit Inline
					<br>
					<img style="height:150px;" src="/app/images/codegen_inline.png" alt="">	
				</label>
			</div>
			{if $level == 1}
				<div>
					<label>
						<input class="db_options_radio" type="radio" name="page_style" value="drag_drop" style="width: 20px; height: 15px;" {(($options['page_style']=='drag_drop')?'checked':'')}> Drag & Drop
						<br>
						<img style="height:150px;" src="/app/images/codegen_combine.png" alt="">	
					</label>
				</div>
				<div>
					<label>
						<input class="db_options_radio" type="radio" name="page_style" value="weekly_calendar" style="width: 20px; height: 15px;" {(($options['page_style']=='weekly_calendar')?'checked':'')}> Weekly Calendar
						<br>
						<img style="height:150px;" src="/app/images/weekly_calendar.png" alt="">	
					</label>
				</div>
				<div>
					<label>
						<input class="db_options_radio" type="radio" name="page_style" value="monthly_calendar" style="width: 20px; height: 15px;" {(($options['page_style']=='monthly_calendar')?'checked':'')}> Monthly Calendar
						<br>
						<img style="height:150px;" src="/app/images/monthly_calendar.png" alt="">	
					</label>
				</div>
			{/if}
		</div>

		{if $level == 1}
			<hr style="margin-top: 10px; margin-bottom: 10px;">
			<div style="display:flex; justify-content:unset;">
				<label style="margin-left:13px;"><input type="checkbox" name="server" value="1" style="width: 20px; height: 15px;"> Add to Server</label>
				<label style="margin-left:13px;"><input type="checkbox" name="svn" value="1" style="width: 20px; height: 15px;"> Add to Server & Make SVN Commit</label>
				<label style="margin-left:13px;"><input type="checkbox" name="download" value="1" style="width: 20px; height: 15px;" checked> Download Files</label>
			</div>
		{/if}
	</div>

	{* <div style="margin-top:10px;">
	<label>
	<input type="checkbox" name="enable_delete" value="{$key.0}" checked>
	Enable Delete Button In The List
	</label>
	</div> *}

	<hr style="margin-top: 10px; margin-bottom: 1px;">
	<div style="display: flex; justify-content:space-between;">
		<button id="add_child_db" class="ajax-link" data-class="{$class}" data-function="table_designer_1" data-form="code_gen_step_1_form" data-parent_class="{$post.dbclass}" data-parent_db="{$post.db}" data-level="{$level}" data-save="true">Add Child Table</button>

		<p style="color: red; margin-top: 20px;" id="codegen_error"></p>

		<div>
			{if $level == 1}
				<button id="generate-btn" class="ajax-link" data-form="code_gen_step_1_form" data-class="{$class}" data-function="code_generator_step_two" style="background-color: #902c2c;">Generate</button>
			{else}
				<button class="ajax-link" data-form="code_gen_step_1_form" data-class="{$class}" data-function="code_generator_save_settings" data-parent_class="{$parent_class}" data-parent_db="{$parent_db}" data-level="{$level}" data-back="true" style="background: black;">Back</button>
			{/if}

			<button id="code_generator_save_settings_btn" class="ajax-link" data-form="code_gen_step_1_form" data-class="{$class}" data-function="code_generator_save_settings" data-parent_class="{$parent_class}" data-parent_db="{$parent_db}" data-level="{$level}" data-back="false" style="visibility:hidden;">Save</button>
		</div>
	</div>

</form>

<table style="margin-top: 20px;">
	{if !empty($child_tables)}
		<tr>
			<th>Child Class Name</th>
			<th>Child Table (DB) Name</th>
			<th></th>
		</tr>
	{/if}
	{foreach $child_tables as $table}
		<tr>
			<td>{$table.child_class}</td>
			<td>{$table.child_table}</td>
			<td>
				{* delete child *}
				<button class="ajax-link listbutton" data-class="{$class}" data-function="delete_child_db" data-id="{$table.id}" data-level="{$level}" style="float:right;color:black;margin-right:5px;" data-save="true"><span class="ui-icon ui-icon-trash"></span></button>

				{* edit child *}
				<button class="ajax-link listbutton" data-class="{$class}" data-function="code_generator_step_one" data-form="code_gen_step_1_form" data-dbclass="{$table.child_class}" data-db="{$table.child_table}" data-parent_class="{$table.parent_class}" data-parent_db="{$table.parent_table}" data-id="{$table.id}" style="float:right;color:black;" data-level="{$level}" data-save="true"><span class="ui-icon ui-icon-pencil"></span></button>
			</td>
		</tr>
	{/foreach}
</table>

<script>
	$('.validation').change(function () {
		var element = $(this).val();
		$('[name="file_types[' + element + ']"]').addClass('hidden');
		$('[name="text_types[' + element + ']"]').addClass('hidden');
		if ($('[name="types[' + element + ']"]').val() == 'file') {
			if ($(this).is(":checked"))
				$('[name="file_types[' + element + ']"]').removeClass('hidden');
			else
				$('[name="file_types[' + element + ']"]').addClass('hidden');
		} else if ($('[name="types[' + element + ']"]').val() == 'text') {
			if ($(this).is(":checked"))
				$('[name="text_types[' + element + ']"]').removeClass('hidden');
			else
				$('[name="text_types[' + element + ']"]').addClass('hidden');
		}
	});

	$('.element_type').change(function () {
		var element = $(this).data('element');
		let value = $(this).val();
		$('[name="file_types[' + element + ']"]').addClass('hidden');
		$('[name="text_types[' + element + ']"]').addClass('hidden');
		if (value == 'file') {
			if ($('[name="validation[' + element + ']"]').is(":checked"))
				$('[name="file_types[' + element + ']"]').removeClass('hidden');
			else
				$('[name="file_types[' + element + ']"]').addClass('hidden');
		} else if (value == 'text') {
			if ($('[name="validation[' + element + ']"]').is(":checked"))
				$('[name="text_types[' + element + ']"]').removeClass('hidden');
			else
				$('[name="text_types[' + element + ']"]').addClass('hidden');
		}

		if (value == 'select' || value == 'checkbox' || value == 'radio')
			$('#element_params_' + element).closest('div').removeClass('hidden');
		else
			$('#element_params_' + element).closest('div').addClass('hidden');

		//change field lenght
		let id = $(this).data('id');
		if (value == 'number' || value == 'float' || value == 'select' || value == 'radio') {
			$("[data-id='" + id + "'].field_length").val("24");
		} else if (value == 'textarea') {
			$("[data-id='" + id + "'].field_length").val("1000");
		} else {
			$("[data-id='" + id + "'].field_length").val("255");
		}

	});

	$('#generate-btn').click(function () {
		$('#codegen_error').html("");
	});

	$('.col_name').click(function () {
		let col_name = $(this).attr('data-col_name');
		$("[name='list[" + col_name + "]']").click();
		$("[name='add[" + col_name + "]']").click();
		$("[name='edit[" + col_name + "]']").click();
		$("[name='validation[" + col_name + "]']").click();
		$("[name='search[" + col_name + "]']").click();
	});

	var field_name_input = "";
	$('.field_name_input').change(function () {
		field_name_input = $(this).val();
	});
	$('.field_name_input').change(function () {
		// alert('field_name_input');
		var val = $(this).val();
		var field_name_input = $(this).attr("data-field_name_input"); //old anme
		$(this).attr("data-field_name_input", val);

		$("[data-col_name='" + field_name_input + "']").attr('data-col_name', val);
		$("[name='types[" + field_name_input + "]']").attr('name', "types[" + val + "]");
		$("[name='file_types[" + field_name_input + "]']").attr('name', "file_types[" + val + "]");
		$("[name='text_types[" + field_name_input + "]']").attr('name', "text_types[" + val + "]");

		$("[data-delete_name='" + field_name_input + "']").val(val);
		$("[data-delete_name='" + field_name_input + "']").attr('data-delete_name', val);

		//params
		$("[data-field_name='" + field_name_input + "']").attr('data-field_name', val);
		// $("[id='element_params_"+field_name_input+"']").attr('id', "element_params_"+val);

		$("[name='list[" + field_name_input + "]']").val(val);
		$("[name='add[" + field_name_input + "]']").val(val);
		$("[name='edit[" + field_name_input + "]']").val(val);
		$("[name='validation[" + field_name_input + "]']").val(val);
		$("[name='search[" + field_name_input + "]']").val(val);
		$("[name='list[" + field_name_input + "]']").attr("name", "list[" + val + "]");
		$("[name='add[" + field_name_input + "]']").attr("name", "add[" + val + "]");
		$("[name='edit[" + field_name_input + "]']").attr("name", "edit[" + val + "]");
		$("[name='validation[" + field_name_input + "]']").attr("name", "validation[" + val + "]");
		$("[name='search[" + field_name_input + "]']").attr("name", "search[" + val + "]");

		//change setting table
		var fd = new FormData();
		fd.append('class', "dbadmin");
		fd.append('function', 'change_field_name_realtime');
		fd.append('dbclass', "{$post.dbclass}");
		fd.append('db', "{$post.db}");
		fd.append('field_new_name', $(this).val());
		fd.append('field_old_name', field_name_input);
		appcon('app.php', fd);

		$('#code_generator_save_settings_btn').click();
	});

	$(document).on('click', '.delete_field', function () {
		let id = $(this).data('id');
		$("[data-id='" + id + "'].field_tr").remove();
		$('#code_generator_save_settings_btn').click();
	});

	var ignore_form_focusout = false;
	$('#code_gen_step_1_form').focusout(function () {
		if (ignore_form_focusout) {
			ignore_form_focusout = false;
			return;
		}

		$('#code_generator_save_settings_btn').click();
	});

	$('.add_param_btn').click(function () {
		ignore_form_focusout = true;
		var fd = new FormData();
		fd.append('class', $(this).attr('data-class'));
		fd.append('function', $(this).attr('data-function'));
		fd.append('dbclass', $(this).attr('data-dbclass'));
		fd.append('db', $(this).attr('data-db'));
		fd.append('field_name', $(this).attr('data-field_name'));
		fd.append('field_old_name', $(this).attr('data-field_old_name'));
		appcon('app.php', fd);
	});

	$('.db_options_radio').change(function () {
		var val = $(this).val();
		if (val == 'weekly_calendar' || val == 'monthly_calendar') {
			const form = document.getElementById('code_gen_step_1_form');
			var fd = new FormData(form);
			fd.append('class', "dbadmin");
			fd.append('function', 'add_default_colums_to_weekly_calendar');
			appcon('app.php', fd);
		}
	});

	$('#add_child_db').click(function () {
		ignore_form_focusout = true;
	});

</script>