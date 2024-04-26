append_function_dialog(function (dialog_id) {

	// Event for typing Enter key on the textarea
	$('#msg').keydown(function(event) {
		if (event.keyCode === 13) {
			event.preventDefault();
			$('#chat_send').click();
		}
	});

});


