<?php

class base {

	private $ffm_category;
	private $ffm_link;
	private $fmt_operation_record;
	private $fmt_ai_setting;

	function __construct(Controller $ctl) {
		$login_type = $ctl->get_login_type();
		$ctl->assign("login_type", $login_type);

		$this->ffm_category = $ctl->db("menu", "menu");
		$this->ffm_link = $ctl->db("link", "menu");
		$this->fmt_operation_record = $ctl->db("operation_record", "base");
		$this->fmt_ai_setting = $ctl->db("ai_setting", "ai_settings");
	}

	function page(Controller $ctl) {

		//プロジェクトのCSSとjsを全て読み込む
		$dirs = new Dirs();
		$css_class_list = array();
		$js_class_list = array();

		$base_projectlist = scandir($dirs->appdir_fw);
		foreach ($base_projectlist as $key => $c) {
			if ($c == "." || $c == ".." || $c == ".htaccess" || $c == "base") {
				continue;
			}
			try {
				if (is_file($dirs->get_class_dir($c) . "/script.js")) {
					$js_class_list[] = $c;
				}
			} catch (Exception $e) {
				
			}
		}

		$base_projectlist = scandir($dirs->appdir_user);
		foreach ($base_projectlist as $key => $c) {
			if ($c == "." || $c == ".." || $c == ".htaccess" || $c == "base") {
				continue;
			}
			try {
				if (!is_file($dirs->get_class_dir($c) . "/public")) {
					if (is_file($dirs->get_class_dir($c) . "/script.js")) {
						$js_class_list[] = $c;
					}
				}
			} catch (Exception $e) {
				
			}
		}

		$ctl->assign("js_class_list", $js_class_list);

		//アプリのメニューを読み込む
		$menutpl = $ctl->get_userdir() . "/common/menu.tpl";
		if (is_file($menutpl)) {
			$menu_html = $ctl->fetch($menutpl);
		}
		$ctl->assign("menu_html", $menu_html);

		
		// 初期のConstant Arraryを設定
		if(!$ctl->is_constant_array("user_type_opt")){
			$ctl->add_constant_array("user_type_opt", "0", "Admin","#ccc");
			$ctl->add_constant_array("user_type_opt", "1", "User","#ffe390");
		}
		if(!$ctl->is_constant_array("workflow_status_opt")){
			$ctl->add_constant_array("workflow_status_opt", "0", "Registered","#C8DFFB");
		}
		
		// 初期のメールテンプレートを入れる
		$ffm_email_format = $ctl->db("email_format", "email_format");
		$email_format_list = $ffm_email_format->select("key", "account_created");
		if(count($email_format_list) ==0){
			$txt = file_get_contents(dirname(__FILE__) . "/Templates/default_email.tpl");
			$arr = array();
			$arr["key"] = "account_created";
			$arr["template_name"] = "Account Created";
			$arr["subject"] = "Your New Account Details";
			$arr["body"] = $txt;
			$ffm_email_format->insert($arr);
		}

		// Bugmanage
		$sec = $ctl->encrypt("login");
		$ctl->assign("bug_sec", $sec);

		// メインエリア自動読み込み
		$alma = $ctl->get_session("__AUTO_LOAD_MAIN_AREA");
		if (!empty($alma)) {
			$ctl->ajax($alma["class"], $alma["function"], $alma);
		}

		$ctl->assign("pagetitle", "FOCUS Business Platform");
		$ctl->display("index.tpl");
	}

	function startup(Controller $ctl) {
		// スタートアップ
		$setting = $ctl->get_setting();
		$class = $setting["startup_class1"];
		$function = $setting["startup_function1"];
		if (empty($class) || empty($function)) {
			// Nothing
		} else {
			$ctl->ajax($class, $function);
		}
	}

	function record(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$ctl->show_multi_dialog("add_operation_name", "operation_name.tpl", "Record Name", 600, true, true);
	}

	function record_exe(Controller $ctl) {
		$post = $ctl->POST();
		$user_id = $ctl->get_login_user_id();
		$post['user_id'] = $user_id;

		if ($post['record_data']) {
			$jsonarr = $post['record_data'];
			$filename = uniqid() . '_record.json';
			$resjson = $ctl->save_file($filename, $jsonarr);
			$post['record_data'] = $filename;
		}

		$ctl->assign('post', $post);
		//validation
		$errors = $this->validate_record_data($ctl, $post, "record");
		if (count($errors)) {
			$ctl->assign('errors', $errors);
			$this->record($ctl);
			return;
		}
		$post['created_at'] = time();
		$post['updated_at'] = time();
		$id = $this->fmt_operation_record->insert($post);
		$ctl->close_multi_dialog("add_operation_name");
	}

	//validation
	function validate_record_data(Controller $ctl, $post, $page) {
		$errors = [];
		if (!$post['operation_name']) {
			$errors['operation_name'] = 'Please enter name for this record.';
		}

		return $errors;
	}

	function playback(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$user_id = $ctl->get_login_user_id();
		$operation_records = $this->fmt_operation_record->select(['user_id'], [$user_id], false, 'AND', 'id', SORT_DESC, $max, $is_last);
		//var_dump($operation_records);
		$ctl->assign('items', $operation_records);
		$ctl->show_multi_dialog("view_records", "view_records.tpl", "Records", 600, true, true);
	}

	function record_play(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_operation_record->get($id);
		$filepath = $ctl->get_saved_filepath($data['record_data']);
		$jsonString = file_get_contents($filepath);
		$record_arr = json_decode($jsonString, true);

		foreach ($record_arr as $record) {
			$new_post = $record;
			foreach ($record as $key => $post_arr) {

				if ($key == "class" || $key == "function" || $key == "cmd" || $key == 'windowcode' || $key == '_call_from') {
					unset($new_post[$key]);
				}
			}

			$ctl->ajax($record['class'], $record['function'], $new_post);
		}
		//$ctl->close_multi_dialog("view_records");
	}

	function record_edit(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_operation_record->get($id);
		$ctl->assign("data", $data);
		$filepath = $ctl->get_saved_filepath($data['record_data']);
		$jsonString = file_get_contents($filepath);
		$record_arr = json_decode($jsonString, true);
		$json2 = json_encode($record_arr, JSON_PRETTY_PRINT);

		$ctl->assign("json", $json2);
		$ctl->show_multi_dialog("record_edit", "record_edit.tpl", "Edit", 500, true, true);
	}

	function record_edit_exe(Controller $ctl) {
		$post = $ctl->POST();
		$new_post = $post;
		$data = $this->fmt_operation_record->get($post['record_id']);
		//$ctl->assign("data", $data);
		$filepath = $ctl->get_saved_filepath($data['record_data']);
		$jsonString = file_get_contents($filepath);
		$record_arr = json_decode($jsonString, true);

		//Save the file.
		file_put_contents($filepath, $new_post['json_string']);
		$ctl->close_multi_dialog("record_edit");
	}

	//view delete page
	function record_delete(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_operation_record->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("record_delete", "record_delete.tpl", "Delete", 500, true, true);
	}

	//delete data form database
	function record_delete_exe(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->fmt_operation_record->get($id);
		//remove file
		$ctl->remove_saved_file($data['record_data']);

		$this->fmt_operation_record->delete($id);
		$ctl->close_multi_dialog("delete");
		$this->page($ctl);
	}

	function show_left_sidemenu(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$ctl->show_sidemenu("side_menu.tpl", 300, 200, "left");
	}

	function show_right_sidemenu(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$ctl->show_sidemenu("side_menu.tpl", 300, 200, "right");
	}

	function img(Controller $ctl) {
		$ctl->res_image("images", $ctl->GET("file"));
	}
}
