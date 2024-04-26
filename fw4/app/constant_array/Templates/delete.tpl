<form id="dx_constant_array_delete_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">


	<span class="lang">Delete the following Array</span>
	<br>
	<br>

	<p>
		<b>

			{$data.array_name}

		</b>
	</p>

	<br>
	<p class="lang">If you perform this process, it will not be restored. Do you want to process it?</p>

</form>

<button class="cancel_delete lang">No</button>
<button class="ajax-link lang" data-form="dx_constant_array_delete_form_{$data.id}" data-class="{$class}"
		data-function="delete_exe">Delete</button>

<script>
	$('.cancel_delete').click(function () {
		$(this).parent().closest(".multi_dialog").children('.multi_dialog_title_area').find('.multi_dialog_close')
				.click();
	});
</script>