append_function_dialog(function (dialog_id) {
	$('.select2-multiple').select2({
		allowClear: true,
	});

	$(dialog_id + " .draggable-weekly-calendar").draggable({
		tolerance: 'fit',
		revert: true,
		scroll: true,
		start: function (event, ui) {
			$(this).css("z-index", 9999999999999);
		},
		stop: function (event, ui) {},
	});

	$(dialog_id + " .droppable-weekly-calendar").droppable({
		accept: '.draggable-weekly-calendar',
		activeClass: "droppable-grap",
		drop: function (event, ui) {
			ui.draggable.detach().appendTo($(this)).css("top", 'auto').css("left", 'auto').css("z-index", '0');
			step_id = ui.draggable.data("id");

			var task_step_id = step_id;
			var scheduled_date = $(this).data("date");
			var user_id = $(this).data("user_id");
			var start_time = $(this).data("start_time");
			var fd = new FormData();
			fd.append('class', $(dialog_id + "#class_name").val());
			fd.append('function', 'update_task_steps_drag_and_drop');
			fd.append('id', task_step_id);
			fd.append('scheduled_date', scheduled_date);
			fd.append('user_id', user_id);
			fd.append("start_time", start_time);
			fd.append('start_date', $(dialog_id + "#start_date").val());
			fd.append('end_date', $(dialog_id + "#end_date").val());
			appcon('app.php', fd);
			$(this).addClass("ui-state-highlight");

		}
	});

	$(document).off("change", dialog_id + "#search_schedule_date");
	$(document).on("change", dialog_id + "#search_schedule_date", function () {
		var fd = new FormData();
		fd.append('class', $(dialog_id + "#class_name").val());
		fd.append('function', 'page');
		fd.append('date_range', "nextweek_" + $(this).val());
		appcon('app.php', fd);
	});

});
