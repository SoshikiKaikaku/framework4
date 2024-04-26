append_function_dialog(function (dialog_id) {
	$(dialog_id).find(".sort").sortable({
		handle: '.col_handle',
		axis: "y",
		update: function () {
			var log = $(this).sortable("toArray");
			var fd = new FormData();
			fd.append('class', 'ai_db');
			fd.append('function', 'sort');
			fd.append('log', log);
			appcon('app.php', fd);
		}
	}).disableSelection();

	$(dialog_id).find(".sort_parameters").sortable({
		handle: '.col_handle',
		axis: "y",
		update: function () {
			var log = $(this).sortable("toArray");
			var fd = new FormData();
			fd.append('class', 'ai_db');
			fd.append('function', 'sort_fields');
			fd.append('log', log);
			appcon('app.php', fd);
		}
	}).disableSelection();

	$(dialog_id + "#type_event").on("change", function () {
		var type = $(dialog_id + " #type_event").val();
		if (type == "dropdown" || type == "checkbox" || type == "radio") {
			$(dialog_id + " #area_option").show();
		} else {
			$(dialog_id + " #area_option").hide();
		}

		var fd = new FormData();
		fd.append("class", "ai_db");
		fd.append("function", "get_default_length");
		fd.append("type", type);
		appcon("app.php", fd, function (data) {
			var length = data["length"];
			$(dialog_id + ".recommended_length").html("Recommended length for " + type + " is " + length);
			if ($(dialog_id + ".field_length").val() == "") {
				$(dialog_id + ".field_length").val(length);
			}
			if ($(dialog_id + ".field_length").val() != length) {
				$(dialog_id + ".field_length").css("color", "red");
			} else {
				$(dialog_id + ".field_length").css("color", "black");
			}
		});
	});
	$(dialog_id + "#type_event").change();
});
