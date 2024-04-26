<div class="container" style="min-width: 600px;">
	<form id="{$class_name}_{$db_name}_add_form">

		<input type="hidden" name="month" value="select">
		<input type="hidden" name="current_start_date" value="[CURLY_OPEN]$post.current_start_date}">

		{if $is_child}
			<input type="hidden" name="{$parent_db}_id" value="[CURLY_OPEN]$post.{$parent_db}_id}">
		{/if}

		<div class="">

			<div style="display:flex;">
				<div style="width:100%;">
					<p class="lang label">Date</p>
					<input class="datepicker" type="text" name="scheduled_date" id="add_task_step_name" value="[CURLY_OPEN]$post['scheduled_date']}" style="width:100%;">
					<p class="error" id="add_task_step_name_err">[CURLY_OPEN]$errors['scheduled_date']}</p>
				</div>

				<div style="width:100%; margin-left:10px;">
					<p class="lang label">Start Time</p>
					<input class="timepicker" type="text" name="start_time" id="add_task_step_name" value="[CURLY_OPEN]$post['start_time']}" style="width:100%;">
					<p class="error" id="add_task_step_name_err">[CURLY_OPEN]$errors['start_time']}</p>
				</div>

				<div style="width:100%; margin-left:10px;">
					<p class="lang label">End Time</p>
					<input type="text" name="end_time" value="[CURLY_OPEN]$post['end_time']}" style="width:100%;" class="timepicker">
					<p class="error" id="end_time">[CURLY_OPEN]$errors['end_time']}</p>
				</div>
			</div>

		</div>

		{foreach $table_settings as $element}
			{if !$element['add_flg']}
				{continue}
			{/if}

			<div>
				<p class="lang">{ucwords(str_replace("_", " ", $element['field_name']))}:</p>

				{if $element['field_type'] == 'text'}
					<input type="text" name="{$element['field_name']}" value="[CURLY_OPEN]$post.{$element['field_name']}}">
				{elseif $element['field_type'] == 'number' || $element['field_type'] == 'float'}
					<input type="number" name="{$element['field_name']}" value="[CURLY_OPEN]$post.{$element['field_name']}}">
				{elseif $element['field_type'] == 'textarea'}
					<textarea name="{$element['field_name']}" rows="5"
							  style="height:auto;">[CURLY_OPEN]$post.{$element['field_name']}}</textarea>
				{elseif $element['field_type'] == 'select'}
					[CURLY_OPEN]html_options name="{$element['field_name']}" options=${$element['field_name']}_options
					selected=$post.{$element['field_name']}}
				{elseif $element['field_type'] == 'date'}
					<input type="text" name="{$element['field_name']}" value="[CURLY_OPEN]$post.{$element['field_name']}}"
						   class="datepicker">
				{elseif $element['field_type'] == 'checkbox'}
					[CURLY_OPEN]html_checkboxes name="{$element['field_name']}" options=${$element['field_name']}_options
					selected=$post.{$element['field_name']} separator='<br />'}
				{elseif $element['field_type'] == 'radio'}
					[CURLY_OPEN]html_radios name="{$element['field_name']}" options=${$element['field_name']}_options
					selected=$post.{$element['field_name']} separator='<br />'}
				{elseif $element['field_type'] == 'file'}
					<input type="file" name="{$element['field_name']}">
				{/if}

				<p class="error lang">[CURLY_OPEN]$errors['{$element['field_name']}']}</p>
			</div>
		{/foreach}

		<div style="display: inline-block; width:100%;margin-bottom: 15px;padding-right: 15px;">
			<button id="add_tast_step" class="ajax-link lang" data-form="{$class_name}_{$db_name}_add_form" data-class="[CURLY_OPEN]$class}" data-function="add_exe" style="margin-right:15px;">Add</button>
		</div>

	</form>
</div>

