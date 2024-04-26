<div class="table ">
	<div class="tr">
		<span class="td" style="width: 4%;"><span class="ui-icon ui-icon-arrow-2-n-s"></span></span>

		{foreach $table_settings as $element}
			{if !$element['list_flg']}
				{continue}
			{/if}

			<span class="td lang">{ucwords(str_replace("_", " ", $element['field_name']))}</span>
		{/foreach}
		<span class="td"></span>
	</div>

	<div class="tbody sort">
		[CURLY_OPEN]foreach $items as $item}
		<form class="tr child {$class_name}_{$db_name}_inline_edit_form" id="[CURLY_OPEN]$item.id}">
			<span class="td" style="width: 4%;"><span class="ui-icon ui-icon-arrow-2-n-s"></span></span>

			{foreach $table_settings as $element}
				{if $element['field_name']=='id'}
					<input type="hidden" name="id" value="[CURLY_OPEN]$item.id}">
				{/if}
				{if !$element['list_flg']}
					{continue}
				{/if}

				{* file dont use edit *}
				<span class="td">
					{if $element['field_type'] == 'file'}
						[CURLY_OPEN]assign var="time" value=(explode("-", $item.{$element['field_name']}))}
						[CURLY_OPEN]assign var="filename" value=([CURLY_OPEN]trim(str_replace($time[0], "", str_replace("-", " ", $item.{$element['field_name']})))})}
						<a class="download-link" data-class="[CURLY_OPEN]$class}" data-function="download" data-file="[CURLY_OPEN]$item.{$element['field_name']}}" style="cursor: pointer;" data-filename="[CURLY_OPEN]$filename}">[CURLY_OPEN]$filename}</a>
					{elseif ($element['field_type'] == 'select')}
						{if $element['edit_flg']}
							[CURLY_OPEN]html_options name="{$element['field_name']}" options=${$element['field_name']}_options selected=$item.{$element['field_name']}}
						{else}
							[CURLY_OPEN]${$element['field_name']}_options[$item.{$element['field_name']}]}
						{/if}
					{elseif ($element['field_type'] == 'radio')}
						{if $element['edit_flg']}
							[CURLY_OPEN]html_radios name="{$element['field_name']}" options=${$element['field_name']}_options selected=$item.{$element['field_name']} separator='<br />'}
						{else}
							[CURLY_OPEN]${$element['field_name']}_options[$item.{$element['field_name']}]}
						{/if}
					{elseif $element['field_type'] == 'checkbox'}
						{if $element['edit_flg']}
							[CURLY_OPEN]if is_array($item.{$element['field_name']})}
							[CURLY_OPEN]assign var="check_data" value=$item.{$element['field_name']}}
							[CURLY_OPEN]else}
							[CURLY_OPEN]assign var="check_data" value=explode(", ", $item.{$element['field_name']})}
							[CURLY_OPEN]/if}
							[CURLY_OPEN]html_checkboxes name="{$element['field_name']}" options=${$element['field_name']}_options selected=$check_data separator='<br />'}
						{else}
							[CURLY_OPEN]assign var="checkbox_arr" value=explode(", ", $item.{$element['field_name']})}
							<ul>
								[CURLY_OPEN]foreach $checkbox_arr as $check_item}
								<li style="list-style-type:disc">[CURLY_OPEN]${$element['field_name']}_options[$check_item]}</li>
								[CURLY_OPEN]/foreach}
							</ul>
						{/if}
					{elseif $element['field_type'] == 'date'}
						{if $element['edit_flg']}
							<input type="text" name="{$element['field_name']}" value="[CURLY_OPEN]$item.{$element['field_name']}}" class="datepicker hasDatepicker">
						{else}
							[CURLY_OPEN]$item.{$element['field_name']}}
						{/if}
					{elseif $element['field_type'] == 'number' || $element['field_type'] == 'float'}
						{if $element['edit_flg']}
							<input type="number" name="{$element['field_name']}" value="[CURLY_OPEN]$item.{$element['field_name']}}">
						{else}
							[CURLY_OPEN]$item.{$element['field_name']}}
						{/if}
					{else}
						{if $element['edit_flg']}
							<input type="text" name="{$element['field_name']}" value="[CURLY_OPEN]$item.{$element['field_name']}}">
						{else}
							[CURLY_OPEN]$item.{$element['field_name']}}
						{/if}
					{/if}
				</span>
			{/foreach}

			<span class="td">
				{if $enable_delete}
					{* delete button *}
					<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="delete" data-id="[CURLY_OPEN]$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
					{/if}

				{if $enable_edit}
					{* edit button *}
					<button class="ajax-link listbutton" data-class="[CURLY_OPEN]$class}" data-function="edit" data-id="[CURLY_OPEN]$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
					{/if}
			</span>
		</form>
		[CURLY_OPEN]/foreach}
	</div>
</div>

<script>
	$('.{$class_name}_{$db_name}_inline_edit_form input').focusout(function () {
		var fd = new FormData($(this).closest('form').get(0));
		fd.append('class', "[CURLY_OPEN]$class}");
		fd.append('function', 'edit_exe');
		appcon('app.php', fd, function () {
			//success
		});
	});

	// append_function_dialog(function (dialog_id) {
	$(".sort").sortable({
		update: function () {
			var log = $(this).sortable("toArray");
			var fd = new FormData();
			fd.append('class', "[CURLY_OPEN]$class}");
			fd.append('function', 'sort');
			fd.append('log', log);
			appcon('app.php', fd);
		}
	}).disableSelection();
	// });

</script>
