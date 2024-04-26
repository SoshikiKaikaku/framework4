append_function_dialog(function (dialog_id) {

	$(document).off("change", dialog_id + "#current_start_datepicker");
	$(document).on("change", dialog_id + "#current_start_datepicker", function () {
		var fd = new FormData();
		fd.append('class', $(dialog_id + "#class_name").val());
		fd.append('function', 'page');
		fd.append('date_range', "nextweek_" + $(this).val());
		fd.append('month', 'select');
		fd.append('current_start_date', $(this).val());
		appcon('app.php', fd);
	});

	$(dialog_id + " .draggable-monthly-calendar").draggable({
		tolerance: 'fit',
		revert: true,
		scroll: true,
		start: function (event, ui) {
			$(this).css("z-index", 9999999999999);
		},
		stop: function (event, ui) {},
	});

	$(dialog_id + " .droppable-monthly-calendar").droppable({
		accept: '.draggable-monthly-calendar',
		activeClass: "droppable-grap",
		drop: function (event, ui) {
			ui.draggable.detach().appendTo($(this)).css("top", 'auto').css("left", 'auto').css("z-index", '0');
			var task_step_id = ui.draggable.data("id");

			var date = $(this).data("date");
			var fd = new FormData();
			fd.append('class', $(dialog_id + "#class_name").val());
			fd.append('function', 'update_task_steps_drag_and_drop');
			fd.append('id', task_step_id);
			fd.append('date', date);
			appcon('app.php', fd);
		}
	});

});
