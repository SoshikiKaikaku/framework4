append_function_dialog(function (dialog_id) {
	
	$(dialog_id).find(".sort").sortable({
		handle: '.col_handle',
		axis: "y",
		update: function () {
			var log = $(this).sortable("toArray");
			var fd = new FormData();
			fd.append('class', 'ai_settings');
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
			fd.append('class', 'ai_settings');
			fd.append('function', 'sort_parameters');
			fd.append('log', log);
			appcon('app.php', fd);
		}
	}).disableSelection();
	
	$(dialog_id+' .ai_sub_para_form').focusout(function () {
	    var fd = new FormData(this);
	    fd.append('class', 'ai_settings');
	    fd.append('function', 'edit_subtable_exe');
	    appcon('app.php', fd);
	});


});
