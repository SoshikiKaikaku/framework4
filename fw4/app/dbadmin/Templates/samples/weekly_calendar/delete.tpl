<form id="{$class_name}_{$db_name}_delete_form_[CURLY_OPEN]$data.id}" class="">

	<input type="hidden" name="id" value="[CURLY_OPEN]$data.id}">
	<input type="hidden" name="start_date" value="[CURLY_OPEN]$post['start_date']}">
	<input type="hidden" name="end_date" value="[CURLY_OPEN]$post['end_date']}">

	{if $is_child}
		<input type="hidden" name="{$parent_db}_id" value="[CURLY_OPEN]$data.{$parent_db}_id}">
	{/if}

	<span class="lang">Delete the following {$db_name_str} Task</span>

	<p>
		<b>
			{foreach $table_settings as $element}
				{if $element['delete_flg']}

					{if $element['field_type'] == 'select' || $element['field_type'] == 'radio' || $element['field_type'] == 'checkbox'}
						[CURLY_OPEN]${$element['field_name']}_options[$data.{$element['field_name']}]}
					{else}
						[CURLY_OPEN]$data.{$element['field_name']}}
					{/if}

				{/if}
			{/foreach}
		</b>
	</p>

	<br>
	<p class="lang">If you perform this process, it will not be restored. Do you want to process it?</p>

</form>

<button class="cancel_delete lang">No</button>
<button class="ajax-link lang" data-form="{$class_name}_{$db_name}_delete_form_[CURLY_OPEN]$data.id}" data-class="[CURLY_OPEN]$class}" data-function="delete_task_step_exe">Delete</button>

<script>
	$('.cancel_delete').click(function () {
		$(this).parent().closest(".multi_dialog").children('.multi_dialog_title_area').find('.multi_dialog_close').click();
	});
</script>