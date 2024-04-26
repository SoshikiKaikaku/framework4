

$('#left_menu .ajax-link').on("click", function (e) {
	$("body, html").animate({scrollTop: 0}, 500);
});

// Start up
var fd_startup = new FormData();
fd_startup.append("class", "base");
fd_startup.append("function", "startup");
appcon("app.php", fd_startup);

var record_status;
var recordPostDataArr;
$(document).ready(function () {

	$("#record-button").click(function () {
		record_status = 'on';
		$(".circle_border").hide();
		$("#autopilot-play").hide();
		$("#pause-autopilot").show();
		$("#pause-autopilot").addClass('autopilot-recording');
	});
	$("#pause-autopilot").click(function () {
		record_status = 'off';

		if (recordPostDataArr.length !== 0) {
			var fd_record = new FormData();
			fd_record.append("class", "base");
			fd_record.append("function", "record");
			fd_record.append("record_data", JSON.stringify(recordPostDataArr));
			appcon("app.php", fd_record);
		}
		recordPostDataArr = [];
		$(".circle_border").show();
		$("#pause-autopilot").hide();
		$("#pause-autopilot").removeClass('autopilot-recording');
		$("#autopilot-play").show();
	});

});
