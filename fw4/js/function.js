
//------------------------------------
// FOCUS Business Platform
// Ver 4
//------------------------------------

sidemenu_from = "left";
sidemenu_time = 200;

//----------------------------
// 年・月 Picker
//----------------------------
(function ($) {
	$.fn.year_month_picker = function () {
		$(this).wrap('<div style="position:relative;display: inline;"></div>');
		$(this).attr('autocomplete', 'off');
		var obj = this;

		// フォーカスがあたった時の処理
		this.focus(function (e) {
			$(obj).blur();

			$(".year_month_picker_panel").remove();
			$(".selectorclose").remove();

			var html = '';
			html += '<div class="year_month_picker_panel">';
			html += '<div class="picker_year"><input type="text" class="picker_year_input">&nbsp;/&nbsp;<select class="picker_month_select">';
			html += '<option value="01">1</option>';
			html += '<option value="02">2</option>';
			html += '<option value="03">3</option>';
			html += '<option value="04">4</option>';
			html += '<option value="05">5</option>';
			html += '<option value="06">6</option>';
			html += '<option value="07">7</option>';
			html += '<option value="08">8</option>';
			html += '<option value="09">9</option>';
			html += '<option value="10">10</option>';
			html += '<option value="11">11</option>';
			html += '<option value="12">12</option>';
			html += '</select>';
			html += "</div>";
			html += '<button class="picker_set lang">Set</button><button class="picker_cancel lang">Cencel</button><button class="picker_blank lang">Delete</button>';
			html += '<div style="clear;both;"></div>'
			html += '<p class="picker_error"></p>'
			html += '</div>';
			$(this).after(html);
			var year;
			var month;
			var hiduke = new Date();
			if ($(this).val() == "") {
				year = hiduke.getFullYear();
				month = hiduke.getMonth() + 1;
			} else {
				var ex = $(this).val().split("/");
				year = ex[0];
				month = ex[1];
				if (year == undefined || isNaN(year))
					year = hiduke.getFullYear();
				if (month == undefined || isNaN(month))
					month = hiduke.getMonth() + 1;
			}
			var pad_month = ('0' + month).slice(-2);
			$(".year_month_picker_panel .picker_year_input").val(year);
			$(".year_month_picker_panel .picker_month_select").val(pad_month);
			//表示位置修正
			var target = ".year_month_picker_panel";
			var top = $(this).offset().top - $(window).scrollTop();
			var left = $(this).offset().left - $(window).scrollLeft();
			var height = $(this).outerHeight();
			var objheight = $(".year_month_picker_panel").outerHeight();
			var wh = $(window).height();
			if (top + height > wh - objheight) {
				top = wh - objheight;
			} else {
				top = top + height;
			}
			$(target).css("top", top);
			$(target).css("left", left);

			var textobj = this;
			$(".year_month_picker_panel .picker_cancel").click(function () {
				$(".year_month_picker_panel").remove();
				$(".selectorclose").remove();
				return false;
			});
			$(".year_month_picker_panel .picker_blank").click(function () {
				$(textobj).val("");
				$(".year_month_picker_panel").remove();
				$(".selectorclose").remove();
				$(obj).change();
				return false;
			});
			$(".year_month_picker_panel .picker_set").click(function () {
				year = $(".year_month_picker_panel .picker_year_input").val();
				month = $(".year_month_picker_panel .picker_month_select").val();

				//年のバリデート
				var flg = true;
				if (year.length != 4) {
					flg = false;
				}
				if (year.match(/[^0-9]+/)) {
					flg = false;
				}
				if (flg) {
					$(textobj).val(year + "/" + month);
					$(obj).change();
					$(".year_month_picker_panel").remove();
					$(".selectorclose").remove();
				} else {
					$(".year_month_picker_panel .picker_error").html("Error");
				}
				return false;
			});
		});
		return this;
	};
})(jQuery);

function get_formdata_with_strtotime(form) {
	$(form).find("input").each(function (index, element) {
		if ($(this).data("strtotime") == "1") {
			var val = $(this).val();
			var time = Date.parse(val) / 1000;
			if (isNaN(time)) {
				time = "";
			}
			$(this).val(time);
			$(this).attr("data-before", val);
		}
	});
	var fd = new FormData(form);

	//戻す
	$(form).find("input").each(function (index, element) {
		if ($(this).data("strtotime") == "1") {
			var val = $(this).attr("data-before");
			$(this).val(val);
		}
	});

	return fd;
}


var userAgent = window.navigator.userAgent.toLowerCase();
// ajax
$("body").on("click", ".ajax-link", function (event) {
	
	event.preventDefault();
	if (dialog_link_flg) {
		dialog_link_flg = false;
		setTimeout(function () {
			dialog_link_flg = true;
		}, 50);

		var form = $(this).data("form");
		if (form === undefined) {
			var fc = $(this).closest("form");
			if (fc !== undefined) {
				var f = fc.get(0);
				// 日付を数値ににしてFormDataを取得
				var fd = get_formdata_with_strtotime(f);
			} else {
				var fd = new FormData();
			}
		} else {
			var f = $("#" + form).get(0);
			// 日付を数値ににしてFormDataを取得
			var fd = get_formdata_with_strtotime(f);
		}

		var url = $(this).data("url");
		if (url === undefined) {
			url = "app.php";
		}
		var datalist = $(this).data();
		for (key in datalist) {
			fd.append(key, datalist[key]);
		}
		
		appcon(url, fd);
	}
});

// ajax-formボタン
$("body").on("click", ".form_button", function (event) {

	var formobj = $(this).parents("form");
	var fd = new FormData(formobj.get(0));
	fd.append($(this).attr("name"), $(this).attr("value"));
	var url = formobj.attr("action");
	if (url === undefined) {
		url = "app.php";
	}
	appcon(url, fd);

	event.preventDefault();
});

// ダイアログを表示する
var dialog_link_flg = true;
function set_dialog_link() {
	$("body").on("click", ".dialog-link", function (event) {

		event.preventDefault();

		if (dialog_link_flg) {
			dialog_link_flg = false;
			setTimeout(function () {
				dialog_link_flg = true;
			}, 50);
			var fd = new FormData();
			var url = $(this).data("url");
			if (url === undefined) {
				url = "app.php";
			}
			var datalist = $(this).data();
			for (key in datalist) {
				fd.append(key, datalist[key]);
			}

			appcon(url, fd);
		}
	});
}
set_dialog_link();

/*
 * ダイアログ処理
 */
$("#dialog").dialog({
	autoOpen: false,
	width: get_dialog_width(),
	resizable: true,
	modal: true,
	show: {effect: 'fade', duration: 200},
	hide: {effect: 'fade', duration: 10},
	position: {my: "center top", at: "center top", of: window},
	buttons: [
		{
			text: "Ok",
			class: "dialog-button-ok",
			click: function () {
				var formobj = $(this).find("#dialogform");
				if (formobj != null) {
					var fd = new FormData(formobj.get(0));
					var url = formobj.attr("action");
					if (url != undefined) {
						appcon(url, fd);
					} else {
						$(this).dialog("close");
					}
				}
			}
		},
		{
			text: "Cancel",
			class: "dialog-button-cancel",
			click: function () {
				$(this).dialog("close");
			}
		}
	],
	close: function () {
		//個別に非表示にしたボタンを表示させる
		$(".ui-dialog-buttonset").show();
		$(".dialog-button-ok").show();
		$(".dialog-button-cancel").show();
		$(".ui-dialog-buttonpane").show();
	}
});

/*
 * ダイアログの横幅の自動設定
 */
function get_dialog_width(userwidth) {

	var w = window.innerWidth;
	if (w < userwidth) {
		return w*0.9;
	}

	return userwidth;
}

/*
 * ダイアログの縦幅の自動設定
 */
function get_dialog_height() {

	var h = window.innerHeight * 0.8;
	return h;
}


/* 
 * アプリ用汎用通信関数
 */
var myChart = new Array(); //チャート用オブジェクト
// Array to store record data
var recordPostDataArr = [];
var record_status;
function appcon(url, fd, nextfunction) {
		
	// 同期処理のため
	var dfd = $.Deferred();

	var start_time = new Date();
	var waitTimer;
	
	// chat用loading
	$("#loading").show();
	$(".class_style_ui_chat #msg").val("");

	fd.append("windowcode", $("#windowcode").data("code"));
	fd.append("_call_from", "appcon");
	
	// Timezone
	var intl_tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
	fd.append("_timezone", intl_tz);
	var tz_offset_minutes = new Date().getTimezoneOffset();
	var utc = tz_offset_minutes / 60 * -1;
	if (utc > 0) {
		fd.append("_timezone_utc", "+" + utc);
	} else {
		fd.append("_timezone_utc", utc);
	}
	fd.append("_timezone_offset_minutes", tz_offset_minutes);

	if (url == ".php") {
		alert("appcon: URLが不正です:" + url);
		return;
	}

	// debug_window
	var debugarr = [];
	var dflg = true;
	fd.forEach((value, key) => {
		if (key == "class" && value == "lang") {
			dflg = false;
		}
		debugarr[key] = value;
	});
	if (dflg) {
		append_debug_window("POST ----> Server", debugarr, "table");
	}

	$("#download_view").show();
	$('#download_message').html("Sending data...");
	$('#download_progress').css({'width': 10 + '%'});


	waitTimer = setInterval(function () {
		var time = new Date();
		$('#download_message').html("processing...   " + Math.round((time - start_time) / 1000) + "sec");
		var load = Math.round((time - start_time) / 1000) / 10 * 100;
		while (load > 100) {
			load = load - 100;
		}
		$('#download_progress').css({'width': load + '%'});
	}, 1000);

	if (record_status === 'on') {
		//record feature
		var recordformData = fd;
		// Convert form data to a plain object
		var recordformObject = {};
		recordformData.forEach(function (value, key) {
			recordformObject[key] = value;
		});

		// Add form data to the array
		recordPostDataArr.push(recordformObject);
		console.log(recordPostDataArr);
	}

	// 送信
	$.ajax({
		async: true,
		url: url,
		type: 'POST',
		dataType: 'html',
		data: fd,
		processData: false,
		contentType: false,
		xhr: function () {
			var XHR = $.ajaxSettings.xhr();
			//進行状況表示
			if (XHR.upload) {
				XHR.upload.addEventListener('progress', function (e) {
					clearInterval(waitTimer);

					if (e.total > 0) {
						var load = (100 * e.loaded / e.total | 0) + 10;
						$('#download_message').html("uploading...   " + Math.round(e.loaded / 1000) + " / " + Math.round(e.total / 1000) + "kbyte");
						$('#download_progress').css({'width': load + '%'});
					}
				});
			}

			XHR.addEventListener('progress', function (e) {
				if (e.total > 0) {
					var load = (100 * e.loaded / e.total | 0) + 10;
					$('#download_message').html("downloading...   " + Math.round(e.loaded / 1000) + " / " + Math.round(e.total / 1000) + "kbyte");
					$('#download_progress').css({'width': load + '%'});
				}
			});

			return XHR;
		},
	}).done(function (data) {
		
		// chat用loading
		$("#loading").hide();

		clearInterval(waitTimer);
		$("#download_view").hide();
		$('#download_progress').css({'width': '0%'});

		if (data == "") {
			if (nextfunction) {
				nextfunction(res);
			}
			dfd.resolve();
			return;
		}

		try {
			var res;
		
			try{
				res = JSON.parse(data);
			} catch (e) {
				console.error(data);
				mdx = multi_dialog_zindex;
				html = "<div class=\"error error_window_message\">" + data + "</div>";
				multi_dialog("error", html, "ERROR", 1200, "error", testserver, mdx);
				multi_dialog_zindex++;
				return;
			}				

			// リロード
			if (res["reload"] != null) {
				var href = location.href;
				if (href.indexOf("windowcode") == -1) {
					href += "&windowcode=" + $("#windowcode").data("code");
				}
				location.assign(href);
				return;
			}

			// リダイレクト
			if (res["location"] != null) {
				location.assign(res["location"]);
				return;
			}

			if (res["close_all_dialog"] != null) {
				var exception = res["close_all_dialog"]["exception"];
				$("#multi_dialog .multi_dialog").each(function (index, element) {
					if ($(this).data("dialog_name") != exception) {
						$(this).remove();
					}
				});
			}

			var chatform = res['chat_form'];
			for (key in chatform) {
				var htmlstr = chatform[key];
				//console.log(htmlstr);
			}

			var reloadarea = res['reloadarea'];
			for (key in reloadarea) {
				var htmlstr = reloadarea[key];
				if ($(key).prop("tagName") == "TEXTAREA") {
					//テキストエリアはvalueに入れる
					$(key).val(htmlstr);
				} else {
					//その他はinnerHtmlに入れる
					$(key).html(htmlstr);
				}
				$(key).css({opacity: '0.5'}).animate({opacity: '1'});

				$(key).ready(function () {
					// デフォルトのJSを動かす
					var p = $(key).parents(".multi_dialog");
					if (p.length > 0) {
						var dialog_id = "#" + $(key).parents(".multi_dialog").attr("id");
						multi_dialog_functions["__all__"](dialog_id, true);
					} else {
						multi_dialog_functions["__all__"]("body", true);
					}

					// クラスのJSを動かす
					var dialog_id = "#" + $(key).parents(".multi_dialog").attr("id");
					var classname = $(key).parents(".multi_dialog_contents").attr("data-classname");
					if (classname === undefined) {
						classname = $("#page_classname").data("class");
					}
					var func = multi_dialog_functions[classname];
					if (func) {
						func(dialog_id + " ");
					}
				});
			}

			var appendarea = res['appendarea'];
			for (key in appendarea) {
				var htmlstr = appendarea[key];
				$(key).html($(key).html() + htmlstr);
				$(key).css({opacity: '0.5'})
				$(key).animate({opacity: '1'}, 'slow');

				$(key).ready(function () {

					//親のウィンドウを見つける
					var p = $(key).parents(".multi_dialog");
					if (p.length > 0) {
						var dialog_id = p.attr("id");
						set_multidialog_height(dialog_id);
					}

					// デフォルトのJSを動かす
					var p = $(key).parents(".multi_dialog");
					if (p.length > 0) {
						var dialog_id = "#" + $(key).parents(".multi_dialog").attr("id");
						multi_dialog_functions["__all__"](dialog_id, true);
					} else {
						multi_dialog_functions["__all__"]("body", true);
					}

					// クラスのJSを動かす
					var dialog_id = "#" + $(key).parents(".multi_dialog").attr("id");
					var classname = $(key).parents(".multi_dialog_contents").attr("data-classname");
					var func = multi_dialog_functions[classname];
					if (func) {
						func(dialog_id + " ");
					}

				});
			}


			if (res["chart"] != null) {
				var chartid = res["chart"]["chartid"];
				var type = res["chart"]["type"];
				var data = res["chart"]["data"];
				var options = res["chart"]["options"];
				//X軸がタイムスタンプの場合
				try {
					if (options["scales"]["xAxes"][0]["type"] == "time") {
						for (ds in data["datasets"]) {
							for (dx in data["datasets"][ds]["data"]) {
								data["datasets"][ds]["data"][dx]["x"] = new Date(data["datasets"][ds]["data"][dx]["x"] * 1000);
							}
						}
					}
				} catch (e) {
					//オプションを設定していないときにエラーがでるが無視
				}

				var chart_div = document.getElementById(chartid);
				if ((chart_div !== undefined) && (chart_div !== null)) {
					var ctx = chart_div.getContext("2d");
					if (myChart[chartid]) {
						myChart[chartid].destroy();
					}
					myChart[chartid] = new Chart(ctx, {
						type: type,
						data: data,
						options: options,
					});
				}
			}

			// Vimeo設定
			if (res["vimeo_client_id"] !== undefined) {
				vimeo_client_id = res["vimeo_client_id"];
			}
			if (res["vimeo_client_secret"] !== undefined) {
				vimeo_client_secret = res["vimeo_client_secret"];
			}
			if (res["vimeo_access_token"] !== undefined) {
				vimeo_access_token = res["vimeo_access_token"];
			}

			// Vimeo 削除
			if (res["delete_vimeo"] != null) {
				var vimeo_id = res["delete_vimeo"]["vimeo_id"];
				var arr = {};
				arr["cmd"] = "delete_vimeo";
				arr["vimeo_client_id"] = vimeo_client_id;
				arr["vimeo_client_secret"] = vimeo_client_secret;
				arr["vimeo_access_token"] = vimeo_access_token;
				arr["vimeo_id"] = vimeo_id;
				websocket.send(JSON.stringify(arr));
			}


			if (res["console_log"] != null) {
				for (var md of res["console_log"]) {
					var color_title = "color:" + md["color"] + ";font-weight:bold;"
					console.log("%c" + md["log"], color_title);
				}
			}

			// 通知(notification)
			if (res["notifications"] != null) {
				for (var md of res["notifications"]) {
					var html = md["html"];
					var width = md["width"];
					var time = md["time"];
					var classname = res["class"];
					
					width = get_dialog_width(width);

					notification(classname, html, width, time);
				}
			}

			// Sidemenu
			if (res["sidemenu"] != null) {
				for (var md of res["sidemenu"]) {
					var html = md["html"];
					var width = md["width"];
					var time = md["time"];
					var from = md["from"];
					var classname = res["class"];

					sidemenu(classname, html, width, time, from);
				}
			}

			// chat
			if (res["chat"] != null) {
				for (var md of res["chat"]) {
					var html = md["html"];
					var reload_last_message = md["reload_last_message"];
					
					if(md["type"] == "text"){
						html = chat_text(html,md["color"]);
					}
					
					if(md["type"] == "clear"){
						$("#chat_history").html("");
					}

					var multi_dialog_tag = document.createElement('div');
					$(multi_dialog_tag).addClass("chat-html");
					$(multi_dialog_tag).append(html);

					$("#chat_history").hide().append(multi_dialog_tag).fadeIn(0);
				}
				translate();
				multi_dialog_functions["__all__"]("body", true);
			}

			if (res["popup"] != null) {
				for (var md of res["popup"]) {
					var html = md["html"];
					var width = md["width"];
					var height = md["height"];
					var classname = res["class"];

					popup(classname, width, height, html);
				}
			}

			// login_node
			if (res["login_node"] != null) {
				var md = res["login_node"];
				var room_name = md["room_name"];
				var group_name = md["group_name"];
				var name = md["name"];
				websocket_login(room_name, group_name, name);
			}

			// send_to_node
			if (res["send_to_node"] != null) {

				if (websocket_logined != 1) {
					console.log("Can't send data to node.You must login to node first.");
				}

				for (var md of res["send_to_node"]) {
					//console.log(md);
					var data = md["data"];
					var room_name = md["room_name"];
					var group_name = md["group_name"];
					var user_id = res["user_id"];
					websocket_send(room_name, group_name, user_id, data);
				}
			}

			// ajax
			if (res["ajax"] != null) {
				for (var md of res["ajax"]) {
					var ajax_classname = md["class"];
					var ajax_function = md["function"];
					var post_arr = JSON.parse(md["post_arr"]);

					var fd = new FormData();
					fd.append("class", ajax_classname);
					fd.append("function", ajax_function);
					fd.append("multi_dialog_zindex", multi_dialog_zindex);
					multi_dialog_zindex++;
					if (post_arr != null) {
						for (let key in post_arr) {
							fd.append(key, post_arr[key]);
						}
					}
					appcon("app.php", fd);
				}
			}


			// robot
			if (res["robot"] != null) {
				for (var md of res["robot"]) {
					var exe_classname = md["class"];
					var dialog_name = md["dialog_name"];
					var element_id = md["element_id"];
					var data = md["data"];
					var robot_cmd = md["robot_cmd"];

					if (robot_cmd == "click") {

						var dialog_id = "#multi_dialog_" + exe_classname + "_" + dialog_name;
						var button = $(dialog_id).find("#" + element_id);
						if (button.length > 0) {
							button.click();
						} else {
							console.log("Robot Error: Can't find element.");
							console.log(md);
						}
					}
				}
			}

			// badge
			if (res["badge"] != null) {
				for (var md of res["badge"]) {
					console.log(md);
					var id = md["id"];
					var val = md["val"];
					$("#" + id).html(val);
					if (val > 0) {
						$("#" + id).show();
					} else {
						$("#" + id).hide();
					}
				}
			}

			// Google Map
			if (res["map"] != null) {
				setTimeout(function () {
					// Delay 1sec
					var tag_id = res["map"]["tag_id"];
					var lat = res["map"]["lat"];
					var lng = res["map"]["lng"];
					var zoom = res["map"]["zoom"];
					var markerData = res["map_marker"];
					draw_google_map(tag_id, lat, lng, zoom, markerData);

				}, 1000);
			}

			// マルチダイアログ
			if (res["multi_dialog"] != null) {

				for (var md of res["multi_dialog"]) {
					var dialog_name = md["dialog_name"];
					var html = md["html"];
					var title = md["title"];
					var width = md["width"];
					var cmd = md["cmd"];
					var classname = res["class"];
					var testserver = md["testserver"];
					var post_arr = md["post_arr"];
					var mdx = md["multi_dialog_zindex"];
					var fixed_bar = md["fixed_bar"];
					var options = md["options"];

					if (cmd == "close") {
						var dialog_id = "#multi_dialog_" + classname + "_" + dialog_name;
						$(dialog_id).remove();
						continue;

					} else {

						if (mdx == null) {
							mdx = multi_dialog_zindex;
						}
						multi_dialog(dialog_name, html, title, width, classname, testserver, mdx, fixed_bar, options);
						multi_dialog_zindex++;
					}
				}
			}

			// タブの追加
			if (res["add_tab"] != null) {
				for (var md of res["add_tab"]) {
					let dialog_id = "#multi_dialog_" + res["class"] + "_" + md["dialog_name"];
					// 同じtabnameのタブがすでにあるかチェックする
					var flg = true;
					$(dialog_id).find(".multi_dialog_tab_area").find(".md_tab").each(function () {
						if ($(this).data("tabname") == md["tabname"]) {
							flg = false;
						}
					});

					// 同じtabnameがなかったら、タブを追加する
					if (flg) {
						let tab = document.createElement('div');
						$(tab).addClass("md_tab");
						$(tab).attr("data-tabname", md["tabname"]);
						$(tab).html(md["title"]);
						$(dialog_id).find(".multi_dialog_tab_area").append(tab);

						if (md["selected"]) {
							$(tab).addClass("md_tab_select");
						}

						// クリックイベントを登録
						let post_arr = md["post_arr"];
						$(tab).on("click", function (e) {

							// タブの選択を全て解除
							$(dialog_id).find(".multi_dialog_tab_area").find(".md_tab").each(function () {
								$(this).removeClass("md_tab_select");
							});

							let fd = new FormData();
							for (let key in post_arr) {
								fd.append(key, post_arr[key]);
							}
							;

							$(tab).addClass("md_tab_select");

							appcon("app.php", fd);
						});
					}


				}
			}

			// メインエリア 
			if (res["work_area"] != null) {

				var md = res["work_area"];
				var dialog_name = md["dialog_name"];
				var html = md["html"];
				var title = md["title"];
				var classname = res["class"];
				var testserver = md["testserver"];
				var post_arr = md["post_arr"];

				var multi_dialog_tag = document.createElement('div');
				$(multi_dialog_tag).attr("id", "multi_dialog_" + classname + "_" + dialog_name);
				//$(multi_dialog_tag).attr("data-dialog_name", dialog_name);
				$(multi_dialog_tag).attr("data-classname", classname);
				$(multi_dialog_tag).addClass("lang_check_area");
				$(multi_dialog_tag).append('<div class="work_area_title lang">' + title + '</div>');
				$(multi_dialog_tag).append(html);
				$("#work_area").hide().html("").append(multi_dialog_tag).fadeIn(0);

				$(multi_dialog_tag).ready(function () {
					var dialog_id = "#multi_dialog_" + classname + "_" + dialog_name;

					// デフォルトのJSを動かす
					multi_dialog_functions["__all__"](dialog_id, true);

					// クラスのJSを動かす
					var func = multi_dialog_functions[classname];
					if (func) {
						func(dialog_id + " ");
					}

					// スクロールイベント
					var tag_ajax_auto = $(multi_dialog_tag).find(".ajax-auto");
					if (tag_ajax_auto.length > 0) {

						var scrollevent = function () {
							if (tag_ajax_auto.offset().top < $(window).scrollTop() + $(window).height()) {
								ajax_auto_exe(dialog_id);
							}
						}
						$(window).off("scroll", scrollevent);
						$(window).on("scroll", scrollevent);
					}
					ajax_auto_exe(dialog_id);
				});
			}

			// debug_window
			append_debug_window(res["debug_window"]);

			if (nextfunction) {
				nextfunction(res);
			}

		} catch (e) {

			append_debug_window(e, data, "error");
			mdx = multi_dialog_zindex;
			html = "<div class=\"error error_window_message\">" + data + "</div>";
			multi_dialog("error", html, "ERROR", 600, "error", testserver, mdx);
			multi_dialog_zindex++;

		}

		dfd.resolve();

	}).fail(function ($xhr) {

		data = $xhr.responseText;
		if (data != undefined) {
			//alert(data);
		}

		dfd.resolve();
	});

	return dfd.promise();
}

/*
 * 通知（Notification)
 */
function notification(classname, html, width, time) {
	var multi_dialog_tag = document.createElement('div');
	$(multi_dialog_tag).addClass("notification");
	$(multi_dialog_tag).addClass("lang_check_area");
	$(multi_dialog_tag).attr("data-classname", classname);
	$(multi_dialog_tag).css("width", width);
	$(multi_dialog_tag).append(html);
	$(multi_dialog_tag).fadeOut(0);
	$("#multi_dialog").append(multi_dialog_tag);

	// z-index
	$(multi_dialog_tag).css("z-index", "9999999999");

	translate();

	$(multi_dialog_tag).fadeIn(200, function () {
		setTimeout(function () {
			$(multi_dialog_tag).fadeOut(200, function () {
				$(this).remove();
			});
		}, time * 1000);
	});
}

function sidemenu(classname, html, width, time, from) {

	sidemenu_from = from;
	sidemenu_time = time;

	if ($('#sidemenu').length !== undefined && $('#sidemenu').length !== 0) {
		if (sidemenu_from == 'right') {
			var document_width = $(document).width();
			// $('#sidemenu').css("left", document_width+width+"px");
			$('#sidemenu').addClass('detect_outside_click').animate({'left': document_width - width + "px"}, time);
		} else {
			$('#sidemenu').addClass('detect_outside_click').animate({'left': '0'}, time);
		}
	} else {

		var multi_dialog_tag = document.createElement('div');
		$(multi_dialog_tag).addClass("sidemenu");
		$(multi_dialog_tag).attr("id", "sidemenu");
		$(multi_dialog_tag).addClass("lang_check_area");
		$(multi_dialog_tag).attr("data-classname", classname);
		$(multi_dialog_tag).css("width", width);
		$(multi_dialog_tag).append(html);
		// $(multi_dialog_tag).fadeOut(0);
		$(multi_dialog_tag).css("z-index", "9999999999");
		$(multi_dialog_tag).css("background", "white");
		$("#multi_dialog").append(multi_dialog_tag);

		translate();

		if (from == 'right') {
			var document_width = $(document).width();
			$(multi_dialog_tag).css("left", document_width + "px");
			$(multi_dialog_tag).addClass('detect_outside_click').animate({'left': document_width - width + "px"}, time);
		} else {
			$(multi_dialog_tag).css("left", "-" + width + "px");
			$(multi_dialog_tag).addClass('detect_outside_click').animate({'left': '0'}, time);
		}

	}
}

function popup(classname, width, height, html) {

	if ($('#popup').length !== undefined && $('#popup').length !== 0) {
		$('#popup').addClass('detect_outside_click').css('opacity', '1').css('display', 'block');
	} else {
		
		width = get_dialog_width(width);

		var multi_dialog_tag = document.createElement('div');
		$(multi_dialog_tag).addClass("popup");
		$(multi_dialog_tag).attr("id", "popup");
		$(multi_dialog_tag).addClass("lang_check_area");
		$(multi_dialog_tag).attr("data-classname", classname);
		$(multi_dialog_tag).css("width", width);
		$(multi_dialog_tag).css("height", height);
		$(multi_dialog_tag).append(html);
		// $(multi_dialog_tag).fadeOut(0);
		$(multi_dialog_tag).css("z-index", "9999999999");
		$(multi_dialog_tag).css("background", "white");
		$("#multi_dialog").append(multi_dialog_tag);

		translate();
		$('#popup').addClass('detect_outside_click').css('opacity', '1').css('display', 'block');


	}
}



/*
 * マルチダイアログ処理
 */
var multi_dialog_zindex = 100;
var multi_dialog_reflesh_time= Math.floor(Date.now() / 1000);
function multi_dialog(dialog_name, contents, title, width, getclassname, testserver, mdz, fixed_bar = "", options = []) {


	var exe_classname = getclassname;
	var dialog_id = "#multi_dialog_" + exe_classname + "_" + dialog_name;

	var multi_dialog_tag = null;

	var title_display = '<span class="lang">' + title + '</span>';

	if ($(dialog_id).length) {

		// 一旦非表示(更新してから３以内は非表示にしない)
		var tmp_now = Math.floor(Date.now() / 1000);
		if(tmp_now > (multi_dialog_reflesh_time + 3)){
			$(dialog_id + " .multi_dialog_contents").fadeOut({duration: 0,});
			multi_dialog_reflesh_time = tmp_now;
		}
		
		// コンテンツを入れかえる
		$(dialog_id + " .multi_dialog_contents").html(contents);

		// FIXED BARを入れる
		$(dialog_id + " .multi_dialog_fixed_bar").html(fixed_bar);

		// タイトルを入れる
		if (title != "") {
			$(dialog_id + " .multi_dialog_innder_title").html(title_display);
			$(dialog_id + " .multi_dialog_innder_title").show();
		} else {
			$(dialog_id + " .multi_dialog_innder_title").hide();
		}

		// z-indexの設定
		$(dialog_id).css("z-index", mdz);

		$(contents).ready(function () {

			$(dialog_id + " .multi_dialog_contents").fadeIn({duration: 500});

			// デフォルトのJSを動かす
			multi_dialog_functions["__all__"](dialog_id);

			// クラスのJSを動かす
			var func = multi_dialog_functions[exe_classname];
			if (func) {
				func(dialog_id + " ");
			}

			// 高さを修正
			set_multidialog_height(dialog_id);

		});

		return;

	} else {
		//-----------------------
		// 新しいウィンドウを開く
		//-----------------------

		// ダイアログ全体
		multi_dialog_tag = document.createElement('div');
		$(multi_dialog_tag).attr("id", "multi_dialog_" + exe_classname + "_" + dialog_name);
		$(multi_dialog_tag).attr("data-dialog_name", dialog_name);
		$(multi_dialog_tag).addClass("multi_dialog");

		// タイトル部分
		if (testserver) {
			var dialog_html = '<div class="multi_dialog_title_area"><div class="dialog_name">' + dialog_name + '</div><div class="multi_dialog_close">X</div></div>';
		} else {
			var dialog_html = '<div class="multi_dialog_title_area"><div class="multi_dialog_close">X</div></div>';
		}

		// タブ
		var tab_area = document.createElement('div');
		$(tab_area).addClass("multi_dialog_tab_area");
		$(tab_area).addClass("lang_check_area");
		$(tab_area).attr("data-classname", exe_classname);

		// 固定バー
		var fixed_bar_tag = $('<div class="multi_dialog_fixed_bar lang_check_area class_style_' + exe_classname + '"></div>');
		fixed_bar_tag.html(fixed_bar);

		// タブのコンテンツのコンテナ
		var container = document.createElement('div');
		$(container).addClass("tab_container");

		// ダイアログに入れていく
		$(multi_dialog_tag).append(dialog_html);
		$(multi_dialog_tag).append(tab_area);
		$(multi_dialog_tag).append(fixed_bar_tag);
		$(multi_dialog_tag).append(container);
		
		$(container).fadeOut({duration: 0,});

		// HTMLに入れる
		$("#multi_dialog").append(multi_dialog_tag);

		// Draggable
		$(multi_dialog_tag).draggable({
			handle: ".multi_dialog_title_area",
			start: function () {
				$(this).css("z-index", multi_dialog_zindex);
				multi_dialog_zindex++;
			},
			stop: function () {
				var st = $(window).scrollTop();
				if ($(this).offset().top < st) {
					$(this).offset({top: st});
				}
				if ($(this).offset().top > st + $(window).height()) {
					$(this).offset({top: st + $(window).height() - 100});
				}
			}
		});

		// Resizable
		$(multi_dialog_tag).resizable();
		$(multi_dialog_tag).on("resize", function (event, ui) {
			//スクロールエリアの高さを再設定
			set_multidialog_height(dialog_id);
		});
		// 拡大アイコンを消す
		$(multi_dialog_tag).find(".ui-resizable-handle").removeClass("ui-icon");

		// Click
		$(multi_dialog_tag).on("click", function (e) {
			$(dialog_id).css("z-index", multi_dialog_zindex);
			multi_dialog_zindex++;
		});

		// クローズイベント
		$(multi_dialog_tag).on("click", ".multi_dialog_close", function (e) {
			$(multi_dialog_tag).resizable("destroy");
			$(multi_dialog_tag).draggable("destroy");
			$(multi_dialog_tag).off("click", ".multi_dialog_close");
			$(multi_dialog_tag).remove();
		});

		// ウィンドウのサイズ
		var dialog_window_size = get_dialog_width(width);
		$(multi_dialog_tag).css("width", dialog_window_size);

		var windowwidth = $("body").width();
		//$(multi_dialog_tag).css("top", 120 + Math.random() * 20);
		$(multi_dialog_tag).css("top", 60 + Math.random() * 5);
		$(multi_dialog_tag).css("left", (windowwidth - dialog_window_size) / 2);

		// スクロールとコンテンツを入れる
		var scroll_tag = document.createElement("div");
		var contents_tag = document.createElement("div");
		$(contents_tag).addClass("multi_dialog_contents");
		$(contents_tag).addClass("lang_check_area");
		$(contents_tag).attr("data-classname", exe_classname);
		$(contents_tag).html(contents);
		$(scroll_tag).append(contents_tag);
		$(scroll_tag).addClass("multi_dialog_scroll");
		$(container).append(scroll_tag);
		
		// 表示
		$(container).fadeIn({duration: 200});

		// z-index
		$(dialog_id).css("z-index", multi_dialog_zindex);
		multi_dialog_zindex++;

		$(scroll_tag).ready(function () {
			// デフォルトのJSを動かす
			multi_dialog_functions["__all__"](dialog_id + " ");

			// クラスのJSを動かす
			var func = multi_dialog_functions[exe_classname];
			if (func) {
				func(dialog_id + " ");
			}

			// 高さの設定
			set_multidialog_height(dialog_id);
			
		});

}
}



function set_multidialog_height(dialog_id) {

	// Resizable停止
	$(dialog_id).resizable("disable");

	var title_tag = $(dialog_id).find(".multi_dialog_title_area");
	var fixed_bar_tag = $(dialog_id).find(".multi_dialog_fixed_bar");
	var scroll_tag = $(dialog_id).find(".multi_dialog_scroll");
	var inner_title_tag = $(dialog_id).find(".multi_dialog_innder_title");
	var contents_tag = $(dialog_id).find(".multi_dialog_contents");

	var h_title = title_tag.height();
	var h_fixed_bar = fixed_bar_tag.height();
	var h_contents = contents_tag.outerHeight(true);
	var h_innertitle = 50;
	if (inner_title_tag.css("display") != "none") {
		h_innertitle = 100;
	}
	var max_height = get_dialog_height();
	var h_padding_margin = 0;

	var h_total = h_title + h_fixed_bar + h_innertitle + h_contents + h_padding_margin;
	if (h_total > max_height) {
		h_total = max_height;
	}

	// 高さを設定
	$(dialog_id).height(h_total);

	// インクリメントを動かす
	ajax_auto_exe(dialog_id);

	// Resizable再開
	$(dialog_id).resizable("enable");
}


// ダウンロードリンク
$("body").on("click", ".download-link", function () {

	var form = $(this).data("form");
	if (form === undefined) {
		var fc = $(this).closest("form");
		if (fc !== undefined) {
			var f = fc.get(0);
			// 日付を数値ににしてFormDataを取得
			var fd = get_formdata_with_strtotime(f);
		} else {
			var fd = new FormData();
		}
	} else {
		var f = $("#" + form).get(0);
		// 日付を数値ににしてFormDataを取得
		var fd = get_formdata_with_strtotime(f);
	}

	var url = $(this).data("url");
	if (url == undefined) {
		url = "app.php";
	}
	var filename = $(this).data("filename");
	var datalist = $(this).data();
	for (key in datalist) {
		fd.append(key, datalist[key]);
	}
	// 同期処理
	$.when(
			modal_download(url, fd, filename)
			).done(function (data) {
	});
});


//--------------------------------------------
// プラグイン用データ送信＆ダウンロード
//--------------------------------------------
function modal_download(url, fd, fileName) {

	fd.append("windowcode", $("#windowcode").data("code"));

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.onprogress = function (evt) {
		$("#download_view").show();
		if (evt.total > 0) {
			var load = 100 * evt.loaded / evt.total;
			$('#download_message').html("Downloading...   " + Math.round(evt.loaded / 1000) + " / " + Math.round(evt.total / 1000) + "kbyte");
			$('#download_progress').css({'width': load + '%'});
		} else {
			$('#download_message').html("");
			$('#download_progress').html(Math.round(evt.loaded / 1000) + " kbyte");
			$('#download_progress').css({'width': '100%'});
		}
	};
	xhr.responseType = 'arraybuffer';
	xhr.onload = function (e) {

		var bytes = new Uint8Array(this.response);
		var blob = new Blob([bytes]);

		var a = document.createElement('a');
		a.download = fileName;

		if (userAgent.indexOf('msie') != -1) {
			window.navigator.msSaveBlob(blob, fileName);
		} else {
			a.href = window.webkitURL.createObjectURL(blob);
			a.click();
		}

		$("#download_view").hide();
		$('#download_progress').css({'width': '0%'});

	}
	xhr.send(fd);

	return false;
}


$(function () {
	$('form').attr('autocomplete', 'off');
});



//----------------------------
// コピー＆ペースト
//----------------------------
var copy_paste_status = 0;  //0:未選択 1:選択中　2:貼り付け場所指定
var copy_paste_start = "";
var copy_paste_end = "";
var copy_paste_arr = [];

(function ($) {
	$.fn.copypaste = function () {
		$(this).attr('autocomplete', 'off');
		return this;
	};

	//一括処理をスタートした時
	$("body").on("click", ".copy_paste_start", function () {
		$(".copy_paste_status_start").hide();
		$(".copy_paste_status_0").fadeIn();
		$(".copy_button").html("終点");
		copy_paste_status = 1;

		//始点の色を変える
		$("body").find(".copypaste").each(function (idx, elem) {
			if ($(elem).attr("name") == copy_paste_start) {
				$(elem).css("background", "#E0FFC3");
			}
		});

		//範囲の色を変える
		$(".copypaste").hover(function () {
			copy_paste_end = $(this).attr("name");
			flg = 0;
			check = false;
			$("body").find(".copypaste").each(function (idx, elem) {
				if ($(elem).attr("name") == copy_paste_start) {
					flg = 1;
				}
				if (flg == 1) {
					$(elem).css("background", "#E0FFC3");
				} else {
					$(elem).css("background", "#FFF");
				}

				if ($(elem).attr("name") == copy_paste_end) {
					if (flg == 1) {
						check = true;
					}
					flg = 0;
				}
			});

			if (!check) {
				$("body").find(".copypaste").each(function (idx, elem) {
					if ($(elem).attr("name") == copy_paste_start) {
						$(elem).css("background", "#E0FFC3");
					} else {
						$(elem).css("background", "#FFF");
					}
				});
			}
		});
	});

	//終了地点の選択、コピー先の選択
	$("body").on("click", ".copypaste", function () {

		if (copy_paste_status == 0) {
			$(".copy_paste_status_start").fadeIn();
			copy_paste_start = $(this).attr("name");

		} else if (copy_paste_status == 1) {

			copy_paste_end = $(this).attr("name");
			flg = 0;

			//選択した場所が正しいか確認する（始点より後でないとだめ）
			check = false;
			$("body").find(".copypaste").each(function (idx, elem) {
				if ($(elem).attr("name") == copy_paste_start) {
					flg = 1;
				}
				if ($(elem).attr("name") == copy_paste_end) {
					if (flg == 1) {
						check = true;
					}
				}
			});
			if (!check) {
				return;
			}

			$(".copypaste").off('mouseenter mouseleave');

			$(".copy_paste_status_0").hide();
			$(".copy_paste_status_1").fadeIn();
			copy_paste_status = 2;

			//元の色と選択したところのデータを保持する
			flg = 0;
			motonoiro = [];
			//motoval = [];
			$("body").find(".copypaste").each(function (idx, elem) {
				motonoiro[$(elem).attr("name")] = $(elem).css("background");
				//motoval[$(elem).attr("name")]  = $(elem).val();
				if ($(elem).attr("name") == copy_paste_start) {
					flg = 1;
				}
				if (flg == 1) {
					copy_paste_arr.push($(elem).val());
				}

				if ($(elem).attr("name") == copy_paste_end) {
					flg = 0;
				}
			});

			//選択中のホバー
			$(".copypaste").hover(function () {
				paste_here = $(this).attr("name");
				cnt = 0;
				flg = 0;
				$("body").find(".copypaste").each(function (idx, elem) {
					if ($(elem).attr("name") == paste_here) {
						flg = 1;
					}
					if (flg == 1) {
						if (cnt < copy_paste_arr.length) {
							$(elem).css("background", "#FFE97D");
							//$(elem).val(copy_paste_arr[cnt]);
						} else {
							$(elem).css("background", motonoiro[$(elem).attr("name")]);
							//$(elem).val(motoval[$(elem).attr("name")]);
						}
						cnt++;
					} else {
						$(elem).css("background", motonoiro[$(elem).attr("name")]);
						//$(elem).val(motoval[$(elem).attr("name")]);
					}
				});
			});

		} else if (copy_paste_status == 2) {

			// コピー先の場所を特定
			paste_here = $(this).attr("name");

			//データを取得する
			arr = [];
			flg = 0;
			idx = 0;
			$("body").find(".copypaste").each(function (i, elem) {
				if ($(elem).attr("name") == paste_here) {
					flg = 1;
				}
				if (flg == 1) {
					$(elem).val(copy_paste_arr[idx]);
					$(elem).css("background", "#FFE97D");
					setTimeout(function () {
						$(elem).css("background", "#FFF");
					}, 500);
					idx++;
				} else {
					$(elem).css("background", "#FFF");
				}

				if (copy_paste_arr.length == idx) {
					return false;
				}
			});

			copy_paste_cancel();
		}

	});

	//キャンセルをクリックした時
	$("body").on("click", ".copy_paste_cancel", function () {
		copy_paste_cancel();
	});

	//削除をクリックした時
	$("body").on("click", ".copy_paste_delete", function () {
		flg = 0;
		$("body").find(".copypaste").each(function (idx, elem) {
			if ($(elem).attr("name") == copy_paste_start) {
				flg = 1;
			}
			if (flg == 1) {
				$(elem).val("");
			}

			if ($(elem).attr("name") == copy_paste_end) {
				flg = 0;
			}
		});
		copy_paste_cancel();
	});

})(jQuery);

function copy_paste_cancel() {
	$(".copy_paste_status_0").hide();
	$(".copy_paste_status_1").hide();
	$(".copypaste").css("background", "#FFF");
	$(".copypaste").off('mouseenter mouseleave');

	copy_paste_arr = [];
	copy_paste_status = 0;
}

//----------------------------
// テキストボックスに３桁表示
//----------------------------
(function ($) {
	$.fn.add_number_format = function () {
		return this.each(function () {
			// Do something to each element here.
			$(this).wrap('<div class="display_number_area"></div>');
			$(this).before('<div class="display_number"></div>');
			$(this).css("text-align", "right");
			var data = $(this).val();
			if (data != "") {
				var sanketa = String(data).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');

			} else {
				var sanketa = 0;
			}
			var dn = $(this).parent().find(".display_number");
			dn.html(sanketa);

			$(this).on("keyup", function (e) {
				var data = $(this).val();
				if (data != "") {
					var sanketa = String(data).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');

				} else {
					var sanketa = 0;
				}
				var dn = $(this).parent().find(".display_number");
				dn.html(sanketa);
			});
		});
	};

})(jQuery);


//エンターキーでフォーム送信を無効
$(function () {
	$("body").on("keydown", "input", function (e) {
		if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
			return false;
		} else {
			return true;
		}
	});
});

//URLパラメーターを取得
function getURLParam(name) {
	var url = window.location.href;
	name = name.replace(/[\[\]]/g, "\\$&");
	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
	if (!results)
		return null;
	if (!results[2])
		return '';
	return decodeURIComponent(results[2].replace(/\+/g, " "));
}

//----------------------------
// テキストボックスに文字数表示
//----------------------------
(function ($) {
	$.fn.text_size_limit = function (options) {
		return this.each(function () {

			if ($(this).hasClass("wordcounter")) {

				$(this).removeClass("wordcounter");

				$(this).after('<div class="display_number"></div>');

				var f = function (obj) {
					var max = $(obj).data("counter_max");
					if (max == null) {
						max = $(obj).data("max");
						if (max == null) {
							append_debug_window("Wordcounter needs data-counter_max parameter in tag", null, "error");
						}
					}

					var count = get_utf8_bytes($(obj).val());
					count = max - count;
					$(obj).next(".display_number").text(count + " bytes");
					if (count <= 0) {
						$(obj).next(".display_number").css("color", "red");
						$(obj).next(".display_number").text("The number of characters is over.");
					} else {
						$(obj).next(".display_number").css("color", "#bbbbbb");
						$(obj).next(".display_number").text(count + " bytes");
					}
				}

				// １回実行
				f(this);

				$(this).on("keyup", function (e) {
					f(this);
				});
			}
		});
	};
})(jQuery);

//----------------------------
// UTFのバイト数を計算
//----------------------------
function get_utf8_bytes(str) {
	var count = 0;
	for (var i = 0; i < str.length; ++i) {
		var cp = str.charCodeAt(i);
		if (cp <= 0x007F) {
			// U+0000 - U+007F
			count += 1;
		} else if (cp <= 0x07FF) {
			// U+0080 - U+07FF
			count += 2;
		} else if (cp <= 0xD7FF) {
			// U+0800 - U+D7FF
			count += 3;
		} else if (cp <= 0xDFFF) {
			// U+10000 - U+10FFFF
			//
			// 0xD800 - 0xDBFF (High Surrogates)
			// 0xDC00 - 0xDFFF (Low Surrogates)
			count += 2;
		} else if (cp <= 0xFFFF) {
			// U+E000 - U+FFFF
			count += 3;
		} else {
			// undefined code point in UTF-16
			// do nothing
		}
	}
	return count;
}




var multi_dialog_functions = {};

function append_function_dialog(classname, func) {
	multi_dialog_functions[classname] = func;
}



function ajax_auto_exe(dialog_id) {
	
	var parent_obj = $(dialog_id);
	if (parent_obj.length > 0) {
		var parent_height = parent_obj.height() + 200;
		var parent_top = parent_obj.offset().top;
		var parent_position = parent_height + parent_top;
	}
	var tags = $(dialog_id + " .ajax-auto");

	//console.log("parent_height=" + parent_height + " parent_top=" + parent_top + " parent_position=" + parent_position);

	tags.each(function (index, element) {
		var top = $(element).offset().top;

		if ($(element).offset().top < $(window).scrollTop() + $(window).height()) {

			if (parent_position === undefined || parent_position > top) {
				// Dialogの中に設置した .ajax-auto タグが表示された

				//console.log("ajax_auto_exe");

				var form = $(element).data("form");
				if (form === undefined) {
					var fd = new FormData();
				} else {
					var fd = new FormData(parent_obj.find("#" + form).get(0));
				}

				var url = $(element).data("url");
				if (url === undefined) {
					url = "app.php";
				}
				var datalist = $(this).data();
				for (key in datalist) {
					fd.append(key, datalist[key]);
				}

				element.remove();

				appcon(url, fd);

			}
		}
	});

}

// デバッグ画面は使わない
$("#show_debug").hide();
$("#debug_window").hide();

function log(msg, data = "", flg_error = "") {
	append_debug_window(msg, data, flg_error);
}

function append_debug_window(msg, data = "", flg_type = "") {

	if (msg === undefined) {
		return;
	}

	if(flg_type != "error"){
		if ($("#testserver").html() != "true") {
			return;
		}
	}

	if (flg_type == "table") {
		var color_key = "color:#4BA3FF;";
		var color_sep = "color:black;";
		var color_val = "color:#FCAF3E;";
		var color_title = "color:#4E9A06;font-weight:bold;"
		console.log("%c" + msg, color_title);
		for (let key in data) {
			if(key == "class" || key == "function"){
				console.log("%c" + key + '%c: %c' + data[key], color_key + "font-weight:bold;", color_sep, color_val + "font-weight:bold;");
			}else{
				console.log("%c" + key + '%c: %c' + data[key], color_key, color_sep, color_val);
			}
			
		}
		return;
	}

	if (flg_type == "error") {
		console.error(msg);
		if (data != "") {
			console.error(data);
		}
	} else {
		console.log(msg);
		if (data != "") {
			console.log(data);
		}
	}
}


//------------------
// websocket
//------------------

var websocket;
var websocket_status = 0;
var websocket_heartbeat_pointer = null;
var websocket_open_retry_pointer = null;
var websocket_functions = [];
var websocket_logined = 0;

$(function () {
	start_websocket();
});

var websocket_heartbeat = function () {
	if (websocket_status == 1) {
		var arr = {}
		arr["cmd"] = "heartbeat";
		websocket.send(JSON.stringify(arr));
	}
}

function websocket_login(room_name, group_name, name) {
	if (websocket_status == 2) {
		return;
	} else if (websocket_status == 1) {
		var arr = {};
		arr["cmd"] = "app_login";
		arr["appcode"] = $("#appcode").html();
		arr["room_name"] = room_name;
		arr["group_name"] = group_name;
		arr["name"] = name;
		websocket.send(JSON.stringify(arr));
		websocket_logined = 1;

		append_debug_window("Websoket logined", arr, false);
	} else {
		append_debug_window("You can't login to node.js. websocket_status is " + websocket_status, "", true);
	}
}


function websocket_send(room_name = null, group_name = null, recipient_user_id = null, data) {
	if (websocket_status == 2) {
		return;
	}
	if (websocket_status == 1 && websocket_logined == 1) {
		var arr = {};
		arr["cmd"] = "app_send";
		arr["appcode"] = $("#appcode").html();
		arr["room_name"] = room_name;
		arr["group_name"] = group_name;
		arr["recipient_user_id"] = recipient_user_id;
		arr["data"] = data;
		websocket.send(JSON.stringify(arr));
		append_debug_window("Send data to websocket server", arr, false);
	} else {
		setTimeout(function () {
			websocket_send(room_name, group_name, recipient_user_id, data);
		}, 1000);
}
}


function append_websocket_event(func) {
	websocket_functions.push(func);
}


function start_websocket() {


	if (websocket_status == 1) {
		return;
	}

	websocket_status = 1;

	// 接続できるまで繰り返す
	websocket_open_retry_pointer = setInterval(start_websocket, 500);

	clearInterval(websocket_heartbeat_pointer);

	if ($("#testserver").html() == "true") {
		websocket = new WebSocket('wss://node.crayondatabase.com:8293');
	} else {
		websocket = new WebSocket('wss://node.crayondatabase.com:8292');
	}

	websocket.onopen = function (e) {
		console.log("websocket opened");
		clearInterval(websocket_open_retry_pointer);
		websocket_heartbeat_pointer = setInterval(websocket_heartbeat, 5 * 1000);
	};

	websocket.onmessage = async function (e) {
		var data = JSON.parse(e.data);
		append_debug_window("Accept data from websocket server", data, "table");

		websocket_functions.forEach(function (elem, index) {
			var func = websocket_functions[index];
			func(data);
		});
	};

	websocket.onerror = function (e) {
		//append_debug_window(e, "", true);
		//websocket_status = 2;
		//setTimeout(start_websocket, 5 * 1000);
	};

	websocket.onclose = function () {
		console.log("websocket closed");
		clearInterval(websocket_open_retry_pointer);
		websocket_status = 2;
		setTimeout(start_websocket, 500);
	};

}

//---------------------
// リンクにwindowcodeを自動でつける
//---------------------
function append_windowcode() {
	var windowcode = $("#windowcode").data("code");
	if (windowcode !== undefined && windowcode !== "") {

		$("a").each(function () {
			var obj = jQuery(this);
			var link = obj.attr("href");
			if (link !== undefined) {
				// 内部リンクだけ
				if (link.indexOf('app.php') != -1) {
					if (link.indexOf('windowcode') == -1) {
						obj.attr("href", link + "&windowcode=" + windowcode);
					}
				}
			}
		});

		$("img").each(function () {
			var obj = jQuery(this);
			var link = obj.attr("src");
			// 内部リンクだけ
			if (link !== undefined) {
				if (link.indexOf('app.php') != -1) {
					if (link.indexOf('windowcode') == -1) {
						obj.attr("src", link + "&windowcode=" + windowcode);
					}
				}
			}
		});
	}
}

$(function () {
	append_windowcode();
});


//---------------------
// 多言語対応
//---------------------
var lang_list = {};   // lang_list[英語][種類(en/jp)]
function get_lang_list() {

	//初期化
	lang_list = {};

	//サーバからリストをとってくる
	var fd = new FormData();
	fd.append("class", "lang");
	fd.append("function", "list");
	var url = "app.php";
	appcon(url, fd, function (data) {
		if (data !== undefined) {
			lang_list = data["list"];

			translate();
		} else {
			//ログイン画面
			lang_list["base"] = {
				"Login ID":
						{
							en: "Login ID",
							jp: "ログインID"
						},
				"Password":
						{
							en: "Password",
							jp: "パスワード"
						},
				"English":
						{
							en: "English",
							jp: "英語"
						},
				"Japanese":
						{
							en: "Japanese",
							jp: "日本語"
						},
				"Login":
						{
							en: "Login",
							jp: "ログイン"
						},
			}
			translate();
		}
	});
}

function translate() {


	if (Cookies.get("lang") == null) {
		return;
	}

	// // 事前準備
	$(".lang").each(function (index) {
		//タグ内に英語が設定されていない場合は lang_en に保持しておく
		var en;
		let lang_prop = '';
		en = $(this).html();
		en = en.trim();

		if ($(this).attr("lang_en") === undefined) {

			if ($(this).prop("tagName") == "SELECT") {
				$(this).removeClass("lang");
				$(this).find("option").each(function (index) {
					en = $(this).html();
					$(this).addClass("lang");
					$(this).attr("lang_prop", "html");
					$(this).attr("lang_en", en)
				});
			}

			if ($(this).prop("tagName") == "INPUT" && $(this).attr("type") == "radio") {

				var parentlabel = $(this).parent("label");
				en = parentlabel.text();
				var inputtag = parentlabel.find("input");
				parentlabel.html("");
				parentlabel.append(inputtag);
				parentlabel.append('<span>');
				var spantag = parentlabel.find("span");
				spantag.addClass("lang");
				spantag.attr("lang_prop", "html");
				spantag.attr("lang_en", en);
				spantag.html(en);
				parentlabel.wrapInner("<span>");
				$(this).removeClass("lang");

			} else {
				if (en == '') {
					en = $(this).val();
					if (en == '') {
						en = $(this).attr('placeholder');
						if (en != '') {
							lang_prop = 'placeholder';
						}
					} else {
						lang_prop = 'val';
					}
				} else {
					lang_prop = 'html';
				}
				$(this).attr("lang_en", en);
				$(this).attr("lang_prop", lang_prop);
			}

		}
	});

	// 登録用の配列
	var send_lang_list = [];

	//翻訳実行
	var selected_lang = Cookies.get("lang");
	$(".lang").each(function (index) {

		var en = $(this).attr("lang_en");

		//classnameを取得
		var parent = $(this).parents(".lang_check_area");
		var classname = "";
		if (parent.length > 0) {
			// Multi Windowの場合
			classname = parent.data("classname");
		}

		// 言語セレクターに合わせて変更
		if (lang_list[classname] === undefined || lang_list[classname][en] === undefined) {
			// データベースにない場合は登録する
			if ($("#testserver").html() == "true") {
				if (en != "") {
					send_lang_list.push({
						classname: classname,
						en: en,
					});
				}
			}

		} else {

			// 翻訳する
			var d = lang_list[classname][en];
			var transrated = d[selected_lang];

			if (transrated != "") {
				let lang_prop = $(this).attr("lang_prop");
				if (lang_prop == 'html') {
					$(this).html(transrated);
				} else if (lang_prop == 'val') {
					$(this).val(transrated);
				} else if (lang_prop == 'placeholder') {
					$(this).attr('placeholder', transrated);
				}

			}
		}
	});

	if (send_lang_list.length > 0) {
		var fd = new FormData();
		fd.append("class", "lang");
		fd.append("function", "append");
		fd.append("lang_array", JSON.stringify(send_lang_list));
		appcon("app.php", fd, function (data) {
			lang_list = data["list"];
		});
	}
}

$(function () {
	get_lang_list();

	var lang = getURLParam("lang");
	if (lang !== null) {
		if ($("#lang_selector").length > 0) {
			$("#lang_selector").val(lang);
			Cookies.set('lang', lang, {expires: 60});
		}
	}

	if ($("#testserver").html() == "true") {

		// 古いcheck_langを削除
		$(".check_lang").remove();

		// check_langを左画面下に追加
		$("BODY").append('<div class="check_lang">check_lang</div>');
		$("BODY").append('<div class="remove_check">remove</div>');

		// 設定画面 ここから
		$("BODY").append('<div id="lang_edit"><p>Class : <span id="lang_edit_classname"></span></p><p>English : <span id="lang_edit_en"></span></p><p>Japanese:&nbsp;<input type="text" id="lang_edit_jp" style="width:300px"></p><button id="lang_edit_submit" class="" style="float:right;">Submit</button><button id="lang_edit_cancel">Cancel</button></div>');

		$("#lang_edit_cancel").on("click", function () {
			$("#lang_edit").hide();
		});

		$("body").on("click", "#lang_edit_submit", function (e) {
			e.preventDefault();
			var fdlang = new FormData();
			fdlang.append("class", "lang");
			fdlang.append("function", "update");
			fdlang.append("classname", lang_edit_classname);
			fdlang.append("en", lang_edit_en);
			fdlang.append("jp", $("#lang_edit_jp").val());
			appcon("app.php", fdlang, function (e) {
				$("#lang_edit").hide();
				get_lang_list();
			});

		});


		// 設定画面　ここまで
	}

	// 初期の言語設定
	var lang;
	if ($("#lang_priority").html() == "0") {
		lang = Cookies.get("lang");
		if (lang == null) {
			if ($("#lang_selector").length > 0) {
				// 言語ドロップダウンのデータを使う
				lang = $("#lang_selector").val();
				if (lang === undefined || lang == "undefined") {
					lang = "en";
				}
			} else {
				// Browser
				lang = window.navigator.language;
				if (lang == "ja") {
					lang = "jp";
				} else {
					lang = "en";
				}
			}
		}
		Cookies.set('lang', lang, {expires: 60});
	} else {
		// Default Language
		lang = $("#lang_default").html();
		if (lang == "") {
			lang = "en";
		}
	}
	// 翻訳実行
	Cookies.set("lang", lang);
	translate();

	$("#lang_selector").val(lang);
	$("#lang_selector").off("change");
	$("#lang_selector").on("change", function () {
		Cookies.set('lang', $(this).val(), {expires: 60});
		translate();
	});
});

var noposition_left = 0;



$("body").on("click", ".remove_check", function (e) {
	$(".lang_edit_button").remove();
});

$("body").on("click", ".check_lang", function (e) {

	noposition_left = 0;
	$(".lang_edit_button").remove();

	$("body").find(".lang").each(function () {

		var en = $(this).attr("lang_en");

		if (en) {

			var parent = $(this).parents(".lang_check_area");

			var topOffset = $(this).offset().top;
			var leftOffset = $(this).offset().left;

			var lang_edit_button = $('<div class="lang_edit_button">EDIT</div>');
			if (parent.length !== 0) {
				var contents = parent.find(".multi_dialog_contents");
				if (contents.length > 0) {
					$(contents).append(lang_edit_button);
				} else {
					if (topOffset <= 0) {
//						leftOffset = noposition_left;
//						topOffset = 0;
//						noposition_left += 40;
					} else {
						$(parent).append(lang_edit_button);
					}
				}
			} else {
				alert('Please add sorrounded by <div class="lang_check_area data-classname="{$class}" : ' + en);
			}
			lang_edit_button.offset({
				top: topOffset,
				left: leftOffset
			});
			lang_edit_button.on("click", function (e) {

				var parent = $(this).parents(".lang_check_area");
				var classname = parent.data("classname");

				if (parent.length === 0) {
					classname = "";
				}

				var myclassname = classname;
				var myen = en;
				show_lang_edit(myclassname, myen);
			});
		}

		$(this).removeClass("check_lang");

	});

	$("body").find(".lang_select").each(function (index) {
		$(this).css("border", "3px yellow solid");
	});

	//$(".check_lang").remove();
});

function show_lang_edit(classname, en) {
	lang_edit_classname = classname;
	lang_edit_en = en;
	$("#lang_edit_classname").html(classname);
	$("#lang_edit_en").html(en);
	$("#lang_edit_jp").val("");
	$("#lang_edit").show();
	$("#lang_edit").css("z-index", multi_dialog_zindex + 2); //ウィンドウをクリックするので増えてしまうため
	multi_dialog_zindex += 2;
	$("#lang_edit_jp").focus();
}
var lang_edit_classname;
var lang_edit_en;



//-----------------------------------
// マルチダイアログのデフォルト関数
// execute when opening a new window
//-----------------------------------
append_function_dialog("__all__", function (dialog_id, flg_window = false) {

	var selected_lang = Cookies.get("lang");

	// World_date_time
	exec_world_datetime();


	// Datepicker
	$(dialog_id + " .datepicker").on("click", function () {

		if ($(this).hasClass("hasDatepicker")) {
			return;
		}

		$(this).prop('readOnly', true);
		var width = $(this).width();
		$(this).wrap('<div class="datepicker_area" style="width:' + width + 'px;display:block;">');
		$(this).after('<div class="datepicker_clear">x</div>');
		$(this).parent().find(".datepicker_clear").on("click", function () {
			$(this).parent().find(".datepicker").val("");
		});
		if (selected_lang == "jp") {
			$.datepicker.setDefaults({
				closeText: "閉じる",
				prevText: "&#x3C;前",
				nextText: "次&#x3E;",
				currentText: "今日",
				monthNames: ["1月", "2月", "3月", "4月", "5月", "6月",
					"7月", "8月", "9月", "10月", "11月", "12月"],
				monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月",
					"7月", "8月", "9月", "10月", "11月", "12月"],
				dayNames: ["日曜日", "月曜日", "火曜日", "水曜日", "木曜日", "金曜日", "土曜日"],
				dayNamesShort: ["日", "月", "火", "水", "木", "金", "土"],
				dayNamesMin: ["日", "月", "火", "水", "木", "金", "土"],
				weekHeader: "週",
				dateFormat: "yy/mm/dd",
				firstDay: 0,
				isRTL: false,
				showMonthAfterYear: true,
				yearSuffix: "年"
			});
		} else {
			$.datepicker.setDefaults({
				closeText: "Close",
				prevText: "&#x3C;Prev",
				nextText: "Next&#x3E;",
				currentText: "Today",
				monthNames: ["January", "February", "March", "April", "May", "June",
					"July", "August", "Septempber", "October", "November", "December"],
				monthNamesShort: ["Jan", "Feb", "Mar", "Apl", "May", "June",
					"July", "Aug", "Sept", "Oct", "Nov", "Dec"],
				dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
				dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
				dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
				weekHeader: "Week",
				dateFormat: "yy/mm/dd",
				firstDay: 0,
				isRTL: false,
				showMonthAfterYear: false,
				yearSuffix: ""
			});
		}
		var dp_txt = $(this);
		$(this).datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: "1930:+30",
			beforeShow: function (input, inst) {
				var cal = inst.dpDiv;
				var top = dp_txt.offset().top - $(window).scrollTop() + dp_txt.outerHeight();
				var left = dp_txt.offset().left;

				if (top + 300 > $(window).innerHeight()) {
					top = top - dp_txt.outerHeight() - 300;
				}

				setTimeout(function () {
					cal.css({
						'top': top,
						'left': left
					});
				}, 10);
			}
		});
		var c = $(this);
		setTimeout(function () {
			c.datepicker("show");
		}, 1);
	});

	// Timepicker
	var timepicker_change = true;
	// 二重送信を防ぐ
	$(dialog_id + ' .timepicker').on("change", function () {
		timepicker_change = false;
		setTimeout(function () {
			timepicker_change = true;
		}, 1000);
	});
	$(dialog_id + ' .timepicker').each(function () {
		var t = $(this).val();
		$(this).timepicker({
			timeFormat: 'H:mm',
			interval: 60,
			minTime: '0',
			maxTime: '23',
			defaultTime: t,
			dynamic: false,
			dropdown: true,
			scrollbar: false,
			zindex: 99999999999999,
			change: function (time) {
				// 多重送信を防ぐ
				if (timepicker_change) {
					timepicker_change = false;
					$(this).change();
					setTimeout(function () {
						timepicker_change = true;
					}, 1000);
				} else {
					return;
				}
			}
		});
	});

	//range picker (air-datepicker)
	if (selected_lang == "jp") {
		var localeAirPicker = {
			days: ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日'],
			daysShort: ['日', '月', '火', '水', '木', '金', '土'],
			daysMin: ['日', '月', '火', '水', '木', '金', '土'],
			months: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
			monthsShort: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
			today: '今日',
			clear: 'クリア',
			dateFormat: 'yyyy/MM/dd',
			timeFormat: 'HH:mm',
			firstDay: 0
		};
	} else {
		var localeAirPicker = {
			days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
			daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
			daysMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
			months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
			monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			today: 'Today',
			clear: 'Clear',
			dateFormat: 'yyyy/MM/dd',
			timeFormat: 'HH:mm',
			firstDay: 0
		};
	}


	var rangePickerOptions = {
		locale: localeAirPicker,
		language: selected_lang,
		autoClose: true,
		range: true,
		multipleDatesSeparator: ' - '
	};
	// creating date picker with locale
	// new AirDatepicker('.rangepicker', rangePickerOptions);
	$('.range_picker').each((i, el) => {
		new AirDatepicker(el, rangePickerOptions);
	});

	// YearMonth picker
	if (selected_lang == "jp") {
		$('.year_month_picker').year_month_picker();
	} else {
		$('.year_month_picker').year_month_picker();
	}

	// RADIO
	$(".checkboxradio").checkboxradio({
		icon: false
	});

	// CHECKBOX (fr_checkbox)
	$(dialog_id + ' .fr_checkbox').each(function () {
		var input = $(this).find("input");
		var unchecked = $(this).find(".unchecked");
		var checked = $(this).find(".checked");
		var unselected = $(this).find(".unselected");

		if ($(this).data("search") == "1") {
			input.hide();

			var fr_checkbox_select = function (i, unselected, unchecked, checked) {
				unselected.removeClass("on");
				unchecked.removeClass("on");
				checked.removeClass("on");
				if (i == "0") {
					unchecked.addClass("on");
				} else if (i == "1") {
					checked.addClass("on");
				} else {
					unselected.addClass("on");
				}
			}

			fr_checkbox_select(input.val(), unselected, unchecked, checked);

			unchecked.on("click", function (e) {
				input.val(0);
				fr_checkbox_select(0, unselected, unchecked, checked);
			});

			checked.on("click", function (e) {
				input.val(1);
				fr_checkbox_select(1, unselected, unchecked, checked);
			});

			unselected.on("click", function (e) {
				input.val("");
				fr_checkbox_select("", unselected, unchecked, checked);
			});


		} else {
			if (input.val() == 1) {
				unchecked.hide();
			} else {
				checked.hide();
			}
			input.hide();

			unchecked.on("click", function (e) {
				input.val(1);
				unchecked.hide();
				checked.show();
			});

			checked.on("click", function (e) {
				input.val(0);
				unchecked.show();
				checked.hide();
			});
		}

	});


	// Autocomplate Off
	$(dialog_id + ' input').prop("autocomplete", "off");

	// Scroll Event
	if (flg_window == false) {
		$(dialog_id + " .multi_dialog_scroll").off("scroll");
		$(dialog_id + " .multi_dialog_scroll").on("scroll", function (e) {
			ajax_auto_exe(dialog_id);
		});
		// ajax-linkを一回動かす
		ajax_auto_exe(dialog_id);
	} else {
		var tag_ajax_auto = $("body").find(".ajax-auto");
		if (tag_ajax_auto.length > 0) {
			var scrollevent = function () {
				if (tag_ajax_auto.offset().top < $(window).scrollTop() + $(window).height()) {
					ajax_auto_exe("body");
				}
			}
			$(window).off("scroll", scrollevent);
			$(window).on("scroll", scrollevent);
		}
	}

	// color picker
	jQuery(function ($) {
		$('.colorpicker').asColorPicker();
	});

	// 文字バイトカウンター
	$(dialog_id + " .wordcounter").text_size_limit({"max": -1});

	// windowcodeをつける
	append_windowcode();

	// 多言語
	translate();

	// ドロップダウンに検索機能
	$(dialog_id + " select").each(function (index, element) {
		if ($(this).children().length > 10) {
			$(this).select2({
				language: "ja" //日本語化
			});
		}
	});

	//Vimeo
	$(dialog_id + " .vimeo").each(function (index, element) {
		vimeo_player(this);
	});

	fr_file_upload_init();
	fr_email_verify_init();

	// Badge処理
	$(".badge").each(function (index, element) {
		var val = $(this).html();
		if (val === undefined || val == "" || val == 0) {
			$(this).hide();
		}
	});

	// geometry_location
	$(".geometry_location").on("focus", function (e) {
		$("button").each(function (index, element) {
			$(this).data("bgcolor", $(this).css("background-color"));
			$(this).css("background-color", "#CCC");
			$(this).css("pointer-events", "none");
		});
	});

	$(".geometry_location").on("focusout", function (e) {

		$('input[name="geometry_location"]').remove();
		var input_tag = document.createElement('input');
		$(input_tag).attr("name", "geometry_location");
		$(input_tag).attr("type", "hidden");
		$(this).parent().append(input_tag);

		get_geometry_location($(this), $(input_tag));

	});

	// select2のアイテムからtitle属性を削除
	$('.select2-selection__rendered').hover(function () {
		$(this).removeAttr('title');
	});

	// Vimeoの送信ボタンを消す
	$("#vimeo_send_button").hide();

	// Vimeo thumbnail
	$(".vimeo_thumbnail").each(function () {
		if ($(this).find("img").length == 0) {
			var obj = this;
			var vimeo_id = $(this).data("vimeo_id");
			var fd = new FormData();
			fd.append("class", "_VIMEO");
			fd.append("function", "_THUMBNAIL");
			fd.append("vimeo_id", vimeo_id);
			appcon("app.php", fd, function (data) {
				var img = document.createElement('img');
				$(img).attr("src", data["url"]);
				obj.append(img);
			});
		}
	});

});
// public用に１回動かす
multi_dialog_functions["__all__"]("", true);

// public用に１回動かす
$(function () {
	var exec_classname = $("#page_classname").data("class");
	if (exec_classname) {
		var func = multi_dialog_functions[exec_classname];
		if (func) {
			func("");
		}
	}
});

// public用に１回動かす
ajax_auto_exe("body");



$(function () {
	$("body").on("click", ".window_large", function (e) {
		var p = $(this).parents(".multi_dialog");
		console.log(p);
		//p.css("transform","scale(1,1)");
		p.css("opacity", "1");
	});
	$("body").on("click", ".window_small", function (e) {
		var p = $(this).parents(".multi_dialog");
		console.log(p);
		//p.css("transform","scale(0.4,0.4)");
		p.css("opacity", "0.5");
	});
});



var vimeo_client_id;
var vimeo_client_secret;
var vimeo_access_token;

//$("body").on("click","#vimeo_file",function(e){
//	var title = $("#vimeo_title").val();
//	var description = $("#vimeo_description").val();
//	if(title == "" || description == ""){
//		$("#vimeo_error").html('<span class="lang">Input the title and the description before select file.</span>');
//		e.preventDefault();
//		translate();
//	}
//});

$("body").on("change", "#vimeo_file", function (e) {

	$("#vimeo_error").html("Uploading...");

	var title = $("#vimeo_title").val();
	var description = $("#vimeo_description").val();
	if (title == "") {
		title = "Untitled";
	}
	if (description == "") {
		description = "Blank"
	}

	movie_send(title, description);
});

var k = null;
var count = 0;
var f;
var slice_size = 1024 * 10; //バッファ

// Vimeoのnodeからのイベントを登録する
append_websocket_event(function (data) {


	if (data["cmd"] == "vimeo_send_data") {

		if (k == null) {

			let size = 0;
			let splitData = null;

			f = $("#vimeo_file").prop('files')[0];
			if (f == null) {
				$("#vimeo_error").html("Error: File is not selected.");
				return;
			}
			size = f.size;
			var filename = f.name;

			count = Math.ceil(size / slice_size); //分割数を計算

			k = 0;
		}

		if (k < count) {

			splitData = f.slice(k * slice_size, (k + 1) * slice_size); //該当箇所をスライス
			k++;

			var reader = new FileReader();
			reader.onload = (event) => {
				var base64 = event.target.result;
				var arr = {};
				arr["cmd"] = "vimeo_upload_data";
				arr["slicedata"] = btoa(base64);
				arr["k"] = k;
				websocket.send(JSON.stringify(arr));

				$("#vimeo_error").html("STEP1 Uploading... " + k + " / " + count + " " + Math.ceil(100 * k / count).toString() + "% ");
			};
			reader.readAsBinaryString(splitData);

		} else {

			var arr = {};
			arr["cmd"] = "send_to_vimeo";
			arr["filename"] = filename;
			websocket.send(JSON.stringify(arr));

			k = null;
			count = 0;
		}

	} else if (data["cmd"] == "vimeo_uri") {
		//URIの受け取り
		var uri_moto = data["uri"];
		var vimeo_id = uri_moto.replace('/videos/', '');
		$("#vimeo_error").html('<span class="lang">Upload is completed.</span>');
		$(".dialog-button-cancel").hide();

		//設定変更
		var title = $("#vimeo_title").val();
		var description = $("#vimeo_description").val();
		var arr = {};
		arr["cmd"] = "edit_vimeo";
		arr["vimeo_id"] = vimeo_id;
		arr["title"] = title;
		arr["description"] = description;
		websocket.send(JSON.stringify(arr));

		$("#vimeo_id").val(vimeo_id);
		$("#vimeo_send_button").show().click();

		translate();

	} else if (data["cmd"] == "vimeo_percentage") {
		//転送状況の通知
		var bytes_uploaded = data["uploaded"];
		var bytes_total = data["total"];
		var percentage = data["percentage"];
		$("#vimeo_error").html("STEP2 Sending to the Vimeo Server...  " + bytes_uploaded + "/" + bytes_total + " " + percentage + "%");

	} else if (data["cmd"] == "vimeo_error") {
		//エラーの通知
		var error = data["error"];
		$("#vimeo_error").html("Error: " + error);
	}
});


function movie_send(title, description) {

	var arr = {};
	arr["cmd"] = "app_login";
	arr["room_name"] = "vimeo_login";
	arr["client_id"] = vimeo_client_id;
	arr["secret"] = vimeo_client_secret;
	arr["token"] = vimeo_access_token;
	arr["title"] = title;
	arr["description"] = description;
	arr["time"] = new Date().getTime();
	websocket.send(JSON.stringify(arr));

}

//Viemo player
function vimeo_player(getobj) {

	var obj = getobj;

	var id = "vimeo_player_" + $(obj).data("vimeo_id");

	$(obj).attr("id", id);

	var vimeo_id = $(obj).data("vimeo_id");
	var options = {
		id: vimeo_id,
		responsive: true,
		autopause: true,
	};
	var player = new Vimeo.Player(id, options);
	player.setVolume(1);
	player.on('play', function () {

	});
	player.on('ended', function () {
		player.destroy();
		$(obj).html("");
		vimeo_player(obj);
	});
	player.on('pause', function () {
	});
}

// $('#left-sidebar-show-btn').click(function () {
// 	// $('#menu_area').css('display', 'block').css('width', '0px').css('width', '300px');
// 	multi_dialog_zindex++;
// 	$('#menu_area').addClass('detect_outside_click').css('left', '0').css("z-index",multi_dialog_zindex);
// });

$(document).on('click', '.left-sidebar-hide-btn, #sidemenu .ajax-link', function () {
	// $('#menu_area').css('display', 'block').css('width', '0px').css('width', '300px');
	// $('#menu_area').removeClass('detect_outside_click').css('left', '-360px');
	if (sidemenu_from == 'right') {
		var document_width = $(document).width();
		$('#sidemenu').removeClass('detect_outside_click').animate({'left': document_width + 'px'}, sidemenu_time);
	} else {
		var sidemenu_width = $('#sidemenu').width();
		$('#sidemenu').removeClass('detect_outside_click').animate({'left': '-' + sidemenu_width + 'px'}, sidemenu_time);
	}
});

$(document).mouseup(function (e) {
	if ($(e.target).closest(".detect_outside_click").length === 0) {
		$('#menu_area').removeClass('detect_outside_click').css('left', '-360px');
		$('#popup').removeClass('detect_outside_click').css('opacity', '0').css('display', 'none');

		if (sidemenu_from == 'right') {
			var document_width = $(document).width();
			$('#sidemenu').removeClass('detect_outside_click').animate({'left': document_width + 'px'}, sidemenu_time);
		} else {
			var sidemenu_width = $('#sidemenu').width();
			$('#sidemenu').removeClass('detect_outside_click').animate({'left': '-' + sidemenu_width + 'px'}, sidemenu_time);
		}
	}

	if (mobile_nav_menu) {
		if ($(e.target).closest(".detect_outside_click").length === 0) {
			mobile_nav_menu = false;
			$('#mobile-nav-menu').css('display', 'none');
			$('#topbar_infomation_btn_mobile').removeClass('detect_outside_click');
		}
	}
});

//$("#mobile-nav-menu").menu();

var mobile_nav_menu = false;
$('#topbar_infomation_btn_mobile').click(function () {
	if (mobile_nav_menu) {
		mobile_nav_menu = false;
		multi_dialog_zindex++;
		$('#mobile-nav-menu').removeClass('detect_outside_click').css('display', 'none');
		$('#topbar_infomation_btn_mobile').removeClass('detect_outside_click');
	} else {
		mobile_nav_menu = true;
		$('#mobile-nav-menu').addClass('detect_outside_click').css('display', 'block').css("z-index", multi_dialog_zindex);
		$('#topbar_infomation_btn_mobile').addClass('detect_outside_click');
	}
});

// topbarのメニューをコピーする
$("#mobile-nav-menu").html("");
$(".topbar_infomation_area").children().each(function (index, element) {
	var clone = $(element).clone();
	if (clone.prop("tagName") == "A") {
		clone.prependTo("#mobile-nav-menu");
		clone.wrap("<li>");

		clone.on("click", function (e) {
			$('#mobile-nav-menu').removeClass('detect_outside_click').css('display', 'none');
			$('#topbar_infomation_btn_mobile').removeClass('detect_outside_click');
		});
	}
});


// World Date Time
function exec_world_datetime() {
	$('.world_datetime').each(function (index, element) {

		// nameを取得
		var name = $(element).prop("name");

		if (element.tagName == "INPUT") {

			var viewtag = $('<div class="viewedit_world_datetime" data-name="' + name + '" style="cursor:pointer;"></div>');

			// DIVで囲う
			var inputwindow = $('<div class="inputwindow_world_datetime"></div>');
			var tag_date = $('<input type="text" class="datepicker" placeholder="yyyy/mm/dd">');
			var tag_time = $('<input type="text" class="timepicker" placeholder="hh:mm">');
			var aryIannaTimeZones = Intl.supportedValuesOf('timeZone');
			var tag_timezone_html = "<select>"
			aryIannaTimeZones.forEach(function (tz) {
				tag_timezone_html += '<option value="' + tz + '">' + tz + '</option>';
			});
			tag_timezone_html += '</select>';
			var tag_offset = $(tag_timezone_html);
			var tag_button = $("<button>OK</button>");
			tag_button.css({
				"display": "inline",
				"margin-top": "0px",
				"height": "28px",
				"background": "#ccc",
				"padding": "0px 10px",
				"color": "#000",
				"border-radius": "5px",
				"float": "inherit",
			});

			// Set the data
			var value = $(element).val();
			if (value != null) {
				var arr_value = value.split(" ");
				if (arr_value.length == 2) {
					var offset = getTimezoneOffset(arr_value[1]);

					var d = new Date(arr_value[0] * 1000 - offset);
					tag_date.val(format_date(d));

					var h = d.getHours();
					var mm = d.getMinutes();
					tag_time.val(h + ":" + mm);

					tag_offset.val(arr_value[1]);
				}
			}

			// style
			tag_date.css({width: "100px", float: "left"});
			tag_time.css({width: "70px", float: "left", "text-align": "center"});
			tag_offset.css({width: "150px", float: "left"});
			inputwindow.append(tag_date);
			inputwindow.append(tag_time);
			inputwindow.append(tag_offset);
			inputwindow.append(tag_button);
			inputwindow.hide();

			$(element).wrap(viewtag);
			viewtag = $(element).parent(".viewedit_world_datetime"); //再取得
			viewtag.append(inputwindow);

			// 表示部分
			var time = view_word_datetime($(element).val());
			viewtag.prepend($('<p class="local_datetime">' + time + '</p>'));

			$(element).hide();

		} else {
			var time = view_word_datetime($(element).html());
			$(element).html(time);
		}



	});

	// クリックイベント
	$('.viewedit_world_datetime').each(function (index, element) {
		$(this).on("click", function () {
			var inputwindow = $(this).find(".inputwindow_world_datetime");
			var top = $(this).offset().top - $(window).scrollTop() + $(this).outerHeight();
			var left = $(this).offset().left;

			if (top + 300 > $(window).innerHeight()) {
				top = top - $(this).outerHeight() - 300;
			}

			$(inputwindow).css({
				'top': top,
				'left': left
			});
			$(inputwindow).show();

			$(this).find("button").off("click");
			$(this).find("button").on("click", function (e) {
				e.preventDefault();
				var inputwindow = $(this).parent(".inputwindow_world_datetime");
				var tag_date = inputwindow.find(".datepicker");
				var tag_time = inputwindow.find(".timepicker");
				var tag_timezone = inputwindow.find("select");
				var tag_textbox = $(this).parents(".viewedit_world_datetime").find(".world_datetime");
				var tag_local_datetime = $(this).parents(".viewedit_world_datetime").find(".local_datetime");
				var value_date = tag_date.val();
				var value_time = tag_time.val();
				var value_timezone = tag_timezone.val();

				// UTF Dateにする
				var arr_date = value_date.split("/");
				var arr_time = value_time.split(":");
				var d = new Date(Date.UTC(arr_date[0], arr_date[1] - 1, arr_date[2], arr_time[0], arr_time[1]));

				// POST用にセット
				var saved_text = (d.getTime() / 1000) + " " + value_timezone;
				tag_textbox.val(saved_text);
				tag_local_datetime.html(format_world_dateitme(d, value_timezone));

				// 隠す
				inputwindow.fadeOut(100);

			});
		});
	});
}

// TIMEZONE OFFSET
const getTimezoneOffset = (timeZone, date = new Date()) => {
	const tz = date.toLocaleString("en", {timeZone, timeStyle: "long"}).split(" ").slice(-1)[0];
	const dateString = date.toString();
	const offset = Date.parse(`${dateString} UTC`) - Date.parse(`${dateString} ${tz}`);

	// return UTC offset in millis
	return offset;
}

function view_word_datetime(str) {
	var arr_str = str.split(" ");
	if (arr_str.length == 2) {
		return format_world_dateitme(new Date(arr_str[0] * 1000), arr_str[1]);
	} else {
		return '<span style="color:#ccc">Date & TIME any Timezone</span>';
	}
}

// World date time のフォーマット
function format_world_dateitme(date, timezone) {

	var offset = getTimezoneOffset(timezone);

	date = new Date(date.getTime() - offset);

	if (date.toString() == "Invalid Date") {
		return '<span style="color:#ccc">Date & TIME any Timezone</span>';
	}

	var y = date.getFullYear();
	var m = ('00' + (date.getMonth() + 1)).slice(-2);
	var d = ('00' + date.getDate()).slice(-2);
	var h = ('00' + date.getHours()).slice(-2);
	var n = ('00' + date.getMinutes()).slice(-2);
	var formatted = y + "/" + m + "/" + d + " " + h + ":" + n;
	var dd = new Date();
	var dt = dd.getTimezoneOffset() * -1 / 60;
	if (dt >= 0) {
		dt = "+" + dt;
	}
	formatted += " GMT" + dt;
	return formatted;
}

function format_date(date) {
	var y = date.getFullYear();
	var m = ('00' + (date.getMonth() + 1)).slice(-2);
	var d = ('00' + date.getDate()).slice(-2);
	var formatted = y + "/" + m + "/" + d;
	return formatted;
}

//--------------------
// file uploader with paste and drag & drop support
//--------------------

var fr_file_upload_active_div_id = '';
var fr_file_upload_active_div_number = 0;
function fr_file_upload_init() {
	var fr_file_upload_count = 0;
	$('.fr_image_paste').each(function () {
		let input_name = $(this).attr('name');
		let text = $(this).data('text');
		if (text === undefined || text == '') {
			text = "File Upload";
		}
		let divStyle = $(this).data('div_style');

		let multiple = $(this).data('multiple');
		let maxLength = $(this).data('max_length');

		fr_file_upload_count++;
		let file_upload_html;

		if (multiple === true) {
			file_upload_html = `<div class="fr_file_upload_div" id="fr_file_upload_div_${fr_file_upload_count}" data-number="${fr_file_upload_count}" style="${divStyle}">
			<div class="fr_file_upload_btn_div">
				<button class="fr_file_upload_btn lang">${text}</button>
			</div>
			<div class="fr_file_upload_input_div hidden upload__box">
				<div>
					<input type="file" name="${input_name}" multiple="" data-multiple="true" data-max_length="${maxLength}" class="fr_file_input upload_multiple_img" id="fr_file_input_${fr_file_upload_count}">
					<p class="lang" style="margin: 5px 0 0 0; font-size: 12px;">Press CTRL+V to paste file or drag & drop here</p>
				</div>
				<div class="fr_file_preview_div"></div>
                                <div class="upload__img-wrap"></div>
			</div>
		</div>`;
		} else {
			file_upload_html = `<div class="fr_file_upload_div" id="fr_file_upload_div_${fr_file_upload_count}" data-number="${fr_file_upload_count}" style="${divStyle}">
			<div class="fr_file_upload_btn_div">
				<button class="fr_file_upload_btn lang">${text}</button>
			</div>
			<div class="fr_file_upload_input_div hidden">
				<div>
					<input type="file" name="${input_name}" class="fr_file_input" id="fr_file_input_${fr_file_upload_count}">
					<p class="lang" style="margin: 5px 0 0 0; font-size: 12px;">Press CTRL+V to paste file or drag & drop here</p>
				</div>
				<div class="fr_file_preview_div"></div>
			</div>
		</div>`;
		}
		imgArray = [];
		$(this).after(file_upload_html);
		$(this).attr('data-name', input_name);
		$(this).attr('data-id', fr_file_upload_count);
		$(this).attr('name', '');
		$(this).attr('id', 'fr_old_file_name_' + fr_file_upload_count);
		$(this).addClass('hidden');
		$(this).removeClass('fr_image_paste');
	});
}
fr_file_upload_init();

$(document).on('click', '.fr_file_upload_btn', function (e) {
	e.preventDefault();
	$(this).closest('.fr_file_upload_div').children('.fr_file_upload_input_div').removeClass('hidden');
	$(this).parent().addClass('hidden');
});
var imgWrap = "";
var imgArray = [];
$(document).on('change', '.fr_file_input', function (e) {

	if (!e.target.files.length)
		return;
	var ismultiple = $(this).data('multiple');
	$(this).closest('.fr_file_upload_input_div').children('.fr_file_preview_div').html('');
	if (!ismultiple) {
		if (e.target.files[0].type.startsWith('image/')) {
			const img = document.createElement('img');
			const blob = URL.createObjectURL(e.target.files[0]);
			img.src = blob;
			img.style.width = "100px";
			$(this).closest('.fr_file_upload_input_div').children('.fr_file_preview_div').html(img);
		}
	} else {

		var max_length = $('.upload_multiple_img').data('max_length');
		var imgfiles = e.target.files;
		fr_multiple_img_add(max_length, imgfiles);

	}
});

$('body').on('click', ".upload__img-close", function (e) {
	var file = $(this).parent().data("file");
	for (var i = 0; i < imgArray.length; i++) {
		if (imgArray[i].name === file) {
			imgArray.splice(i, 1);
			break;
		}
	}
	let list = new DataTransfer();
	imgArray.forEach(function (f, index) {
		list.items.add(f);
	});
	let myFileList = list.files;
	document.getElementById("fr_file_input_" + fr_file_upload_active_div_number).files = myFileList;
	$(this).parent().parent().remove();
});

$(document).on('click', '.fr_file_upload_div', function () {
	fr_file_upload_active_div_id = this.id;
	fr_file_upload_active_div_number = $(this).data('number');
	//$('.fr_file_upload_div').css('border', 'solid 1px black');
	//$(this).css('border', 'solid 2px blue');
});

document.addEventListener('paste', async (e) => {
	// e.preventDefault();
	if (!e.clipboardData.files.length)
		return;
	$('#' + fr_file_upload_active_div_id).find('.fr_file_preview_div').html('');
	var ismultiple = $('#' + fr_file_upload_active_div_id).find('.fr_file_input').data('multiple');

	if (!ismultiple) {
		Array.from(e.clipboardData.files).forEach(async (file) => {
			if (file.type.startsWith('image/')) {
				//create image
				const img = document.createElement('img');
				const blob = URL.createObjectURL(file);
				img.src = blob;
				img.style.width = "100px";
				$('#' + fr_file_upload_active_div_id).find('.fr_file_preview_div').html(img);
			}
			//append file to input
			let list = new DataTransfer();
			list.items.add(file);
			let myFileList = list.files;
			document.getElementById("fr_file_input_" + fr_file_upload_active_div_number).files = myFileList;
		});
	} else {

		var max_length = $('.upload_multiple_img').data('max_length');
		var imgfiles = e.clipboardData.files;
		fr_multiple_img_add(max_length, imgfiles);

	}
	$('#' + fr_file_upload_active_div_id).children('.fr_file_upload_btn_div').addClass('hidden');
	$('#' + fr_file_upload_active_div_id).children('.fr_file_upload_input_div').removeClass('hidden');
	return false;
});

$(document)
		.on('dragover', '.fr_file_upload_div', function (e) {
			$(this).addClass('fr_file_upload_draggin');
			return false;
		}).on('dragleave', '.fr_file_upload_div', function (e) {
	fr_dragging = true;
	$(this).removeClass('fr_file_upload_draggin');
	return false;
}).on('drop', '.fr_file_upload_div', function (e) {
	fr_file_upload_active_div_id = this.id;
	fr_file_upload_active_div_number = $(this).data('number');
	$('#' + fr_file_upload_active_div_id).find('.fr_file_preview_div').html('');
	document.getElementById("fr_file_input_" + fr_file_upload_active_div_number).files = e.originalEvent.dataTransfer.files;
	var ismultiple = $(this).find('.fr_file_input').data('multiple');
	if (!ismultiple) {
		//create image
		if (e.originalEvent.dataTransfer.files[0].type.startsWith('image/')) {
			const img = document.createElement('img');
			const blob = URL.createObjectURL(e.originalEvent.dataTransfer.files[0]);
			img.src = blob;
			img.style.width = "100px";
			$('#' + fr_file_upload_active_div_id).find('.fr_file_preview_div').html(img);
		}
	} else {
		var max_length = $('.upload_multiple_img').data('max_length');
		var imgfiles = e.originalEvent.dataTransfer.files;
		fr_multiple_img_add(max_length, imgfiles);
	}
	$(this).children('.fr_file_upload_btn_div').addClass('hidden');
	$(this).children('.fr_file_upload_input_div').removeClass('hidden');
	return false;
});
// end - file uploader
function fr_multiple_img_add(max_length, imgfiles) {
	$('.upload_multiple_img').each(function () {
		//$(this).on('change', function (e) {
		imgWrap = $(this).closest('.upload__box').find('.upload__img-wrap');
		var maxLength = max_length;

		//var files = e.target.files;
		var files = imgfiles;
		var filesArr = Array.prototype.slice.call(files);
		var iterator = 0;
		let list = new DataTransfer();

		filesArr = imgArray.concat(filesArr);
		filesArr = filesArr.filter((item, pos) => filesArr.indexOf(item) === pos);

		if (filesArr.length > maxLength) {
			alert('You can not select more than ' + maxLength + ' files');
			//return false;
		}
		var len = 0;
		imgWrap.html('');
		imgArray = [];
		filesArr.forEach(function (f, index) {

			if (!f.type.match('image.*')) {
				return;
			}
			len++;
			if (len <= maxLength) {

				list.items.add(f);
				imgArray.push(f);

				var reader = new FileReader();
				reader.onload = function (e) {
					var html = "<div class='upload__img-box'><div style='background-image: url(" + e.target.result + ")' data-number='" + $(".upload__img-close").length + "' data-file='" + f.name + "' class='img-bg'><div class='upload__img-close'></div></div></div>";
					imgWrap.append(html);
					iterator++;
				}
				reader.readAsDataURL(f);
			}
		});
		let myFileList = list.files;
		document.getElementById("fr_file_input_" + fr_file_upload_active_div_number).files = myFileList;
		//});
	});
}
// ----------------------------
// Email verification component
// ----------------------------
var fr_email_verify_count = 0;
var fr_email_verify_active_div_id = '';
var fr_email_verify_active_div_number = 0;
var fr_email_verify_btn_enter = false;
function fr_email_verify_init() {
	$('.fr_verification_mail').each(function () {
		let input_name = $(this).attr('name');

		fr_email_verify_count++;
		let file_upload_html = `<div class="fr_email_veriry_main_div hidden" id="fr_email_verify_main_div_${fr_email_verify_count}" data-number="${fr_email_verify_count}">
			<input type="hidden" name="${input_name}" class="fr_email_veriry_hidden_field" />
			<div class="fr_email_verify_first_div ">
				<div style="display:flex;">
					<span class="ui-icon ui-icon-triangle-1-w fr_email_veriry_email_back_btn" style="transform: scale(2); margin-top:0px;"></span>
					<p class="fr_email_veriry_email_p" style="margin: 0 10px"></p>
				</div>
				<button class="fr_email_verify_send_btn" data-class="user" data-function="fr_verification_mail_send" data-email="" data-key="">Send Verify Mail</button>
			</div>
			<div class="fr_email_verify_second_div hidden">
				<div style="display:flex;">
					<input class="fr_email_verify_text" type="text" style="text-align: center;" />
				</div>
				<button class="fr_email_verify_btn" data-class="user" data-function="fr_verification_mail_send" data-email="" data-key="">Submit</button>
				<p class="fr_email_verify_error_msg hidden">Verification Faild!</p>
			</div>
			<div class="fr_email_verify_third_div hidden">
				<div style="display:flex;">
					<span class="ui-icon ui-icon-circle-check" style="transform: scale(1.5); margin:auto; cursor: pointer !important;"></span>
					<p class="fr_email_veriry_email_p" style="margin: 0 10px"></p>
				</div>
			</div>
		</div>`;
		$(this).after(file_upload_html);
		$(this).attr('data-name', input_name);
		$(this).attr('data-id', fr_email_verify_count);
		$(this).attr('name', '');
		$(this).attr('id', 'fr_email_verify_old_input_' + fr_email_verify_count);
		$(this).addClass('fr_verification_mail_new');
		$(this).removeClass('fr_verification_mail');
	});
}
fr_email_verify_init();

$(document).on('change', '.fr_verification_mail_new', function (e) {
	e.preventDefault();
	let val = $(this).val();
	let main_div_id = '#fr_email_verify_main_div_' + $(this).data('id');
	$(main_div_id).removeClass('hidden');
	$(main_div_id).find('.fr_email_veriry_email_p').html(val);
	$(this).addClass('hidden');
	$(main_div_id).attr('data-email', val);
	$(main_div_id).find('.fr_email_verify_send_btn').attr('data-email', val);
	$(main_div_id).find('.fr_email_verify_btn').attr('data-email', val);
	$('.fr_email_veriry_email_back_btn').css('cursor', 'pointer');
	$(this).val('');
});

$(document).on("keydown", '.fr_verification_mail_new', function (e) {
	if (e.which == 13) {
		fr_email_verify_btn_enter = true;
	}
	setTimeout(() => {
		fr_email_verify_btn_enter = false;
	}, 100);
});

$(document).on('click', '.fr_email_verify_send_btn', function (e) {
	e.preventDefault();
	if (!fr_email_verify_btn_enter) {
		let main_div = $(this).closest('.fr_email_veriry_main_div');
		main_div.children('.fr_email_verify_second_div').removeClass('hidden');
		$(this).parent('.fr_email_verify_first_div').addClass('hidden');
		main_div.find('.fr_email_verify_text').focus();
		let email = $(this).attr('data-email');
		let fd = new FormData();
		fd.append('class', 'user');
		fd.append('function', 'fr_verification_mail_send');
		fd.append('email', email);
		appcon('app.php', fd, function (data) {
			main_div.find('.fr_email_verify_btn').attr('data-key', data.key);
		});
	}
});

$(document).on('click', '.fr_email_verify_btn', function (e) {
	e.preventDefault();
	let main_div = $(this).closest('.fr_email_veriry_main_div');
	let key = $(this).attr('data-key');
	let code = main_div.find('.fr_email_verify_text').val();
	let email = $(this).attr('data-email');
	let fd = new FormData();
	fd.append('class', 'user');
	fd.append('function', 'fr_verification_mail_verify');
	fd.append('key', key);
	fd.append('code', code);
	appcon('app.php', fd, function (data) {
		if (data.status) {
			main_div.children('.fr_email_verify_third_div').removeClass('hidden');
			main_div.find('.fr_email_verify_second_div').addClass('hidden');
			main_div.find('.fr_email_veriry_hidden_field').val(email);
		} else {
			main_div.find('.fr_email_verify_error_msg').removeClass('hidden');
		}
	});
});

$(document).on('click', '.fr_email_veriry_email_back_btn', function (e) {
	let parent = $(this).closest('.fr_email_veriry_main_div');
	let email = parent.attr('data-email');
	let input_id = '#fr_email_verify_old_input_' + $(this).closest('.fr_email_veriry_main_div').data('number');
	$(input_id).removeClass('hidden');
	$(input_id).val(email);
	parent.addClass('hidden');
});

$(document).on('click', '.fr_email_veriry_main_div', function () {
	fr_email_verify_active_div_id = this.id;
	fr_email_verify_active_div_number = $(this).data('number');
	$('.fr_email_veriry_main_div').css('border', 'solid 1px black');
	$(this).css('border', 'solid 2px blue');
});
//end - email verification componentn

function get_geometry_location(address_tag, textbox) {
	address_tag.css("transition", "0.4s");
	var atwidth = address_tag.width() + 20;
	var address = address_tag.val();
	var color = address_tag.css("color");
	geocoder = new google.maps.Geocoder();
	geocoder.geocode({
		'address': address
	}, function (results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
			textbox.val(results[0].geometry.location);

			$('.glocation-error').remove();
			$('.glocation-success').remove();
			var locnotify = $("<p class='glocation-success' style='width:" + atwidth + "px;'>Successed to get geometry location!</p>").insertAfter(address_tag);

			setTimeout(function () {
				locnotify.css("display", "none");
			}, 2000);
		} else {
			console.log("Fail to get geometry.location");
			$('.glocation-error').remove();
			$('.glocation-success').remove();

			var locnotify = $("<p class='glocation-error' style='width:" + atwidth + "px;'>Failed to get geometry.location</p>").insertAfter(address_tag);

			setTimeout(function () {
				locnotify.css("display", "none");
			}, 2000);
		}
		$("button").each(function (index, element) {
			$(this).css("background-color", $(this).data("bgcolor"));
			$(this).css("pointer-events", "auto");
		});
	});
}

var map;
var marker = [];
var infoWindow = [];
function draw_google_map(tag_id, lat, lng, zoom, markerData) {

	if (status_map == 1) {
		draw_google_map_exe(tag_id, lat, lng, zoom, markerData);
	} else {
		var map_interval_pointer = function () {
			if (status_map == 1) {
				clearInterval(map_interval_pointer);
				draw_google_map_exe(tag_id, lat, lng, zoom, markerData);
			}
		}
		setInterval(map_interval_pointer, 1000);

	}
}

function draw_google_map_exe(tag_id, lat, lng, zoom, markerData) {

	if (!$("#" + tag_id).length) {
		console.log("There is no tag ID=" + tag_id + ".");
		return;
	}

	// size check and set
	var width = $("#" + tag_id).height();
	var height = $("#" + tag_id).width();

	if (width == 0 || height == 0) {
		$("#" + tag_id).width(500);
		$("#" + tag_id).height(500);
	}

	// 地図の作成
	map = new google.maps.Map(document.getElementById(tag_id), {// #sampleに地図を埋め込む
		center: {// 地図の中心を指定
			lat: lat, // 緯度
			lng: lng // 経度
		},
		zoom: zoom  // 地図のズームを指定
	});

	var bounds = new google.maps.LatLngBounds();

	// マーカー毎の処理
	if (markerData !== undefined) {
		for (var i = 0; i < markerData.length; i++) {

			var loc = markerData[i]["location"];

			var markerLatLng = new google.maps.LatLng({lat: loc["lat"], lng: loc["lng"]}); // 緯度経度のデータ作成
			marker[i] = new google.maps.Marker({// マーカーの追加
				position: markerLatLng, // マーカーを立てる位置を指定
				map: map // マーカーを立てる地図を指定
			});

			bounds.extend(markerLatLng);

			//吹き出しデータの作成
			infoWindow[i] = new google.maps.InfoWindow({// 吹き出しの追加
				content: '<div class="map_info_window">' + markerData[i]['html'] + '</div>' // 吹き出しに表示する内容
			});

			markerEvent(i); // マーカーにクリックイベントを追加

		}

		if (zoom == 0)
			map.fitBounds(bounds);

	}

	//init autocomplete
	$(".geometry_location").each(function (index, element) {
		// var locinput = document.getElementsByClassName('geometry_location');
		var locinput = $(this)[0];
		var autocomplete = new google.maps.places.Autocomplete(locinput);
		autocomplete.addListener('place_changed', function () {
			var place = autocomplete.getPlace();
			// locinput.value = JSON.stringify(place.address_components);
			$(".geometry_location").focusout();
		});

	});

}

// マーカーにクリックイベントを追加
function markerEvent(i) {
	marker[i].addListener('click', function () { // マーカーをクリックしたとき
		infoWindow[i].open(map, marker[i]); // 吹き出しの表示
	});
}

// first appcon for DISPLAY
$(function () {
	var fd = new FormData();
	fd.append("class", "_DISPLAY");
	fd.append("function", "_ARR");
	appcon("app.php", fd, function (data) {
	});
});

function chat_text(txt,color){
	var html='<div class="color-dot" id="last_chat"><div class="chat-dot" style="background-color:' + color + '"></div><p>' + txt +'</p></div>';
	return html;
}