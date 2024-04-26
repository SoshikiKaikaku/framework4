

append_function_dialog(function (dialog_id) {

	$(dialog_id + ".sortable").sortable({
		handle: ".handle",
		axis: "y",
		update: function () {
			var log = $(this).sortable("toArray");
			var fd = new FormData();
			fd.append("class", $(this).data("class"));
			fd.append("function", "sort");
			fd.append("table_name", $(this).data("table_name"));
			fd.append("log", log);
			appcon("app.php", fd);
		}
	});

	$(dialog_id + ".droparea").sortable({
		connectWith: ".droparea",
		cancel: '.child_add_button_area',
		update: function () {
			let parent_id = $(this).data("parent_id");
			var log = $(this).sortable("toArray");
			var fd = new FormData();
			fd.append("class", $(this).data("class"));
			fd.append("function", "sort");
			fd.append("table_name", $(this).data("table_name"));
			fd.append("parent_table_name", $(this).data("parent_table_name"));
			fd.append("parent_id", parent_id);
			fd.append("log", log);
			appcon("app.php", fd);
		}
	});


	$(dialog_id + ".onchange_update").on("change", function () {
		let table_name = $(this).data("table_name");
		let id = $(this).data("id");
		let val = $(this).val();
		let c = $(this).data("class");
		let fd = new FormData();
		fd.append("class", c);
		fd.append("function", "onchange_update");
		fd.append("table_name", table_name);
		fd.append("id", id);
		fd.append("value", val);
		fd.append("name", $(this).attr("name"));
		appcon("app.php", fd);
	});


});