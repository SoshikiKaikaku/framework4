<?php

/*
 *  YOU CAN'T CHANGE THIS PROJECT.
 *  It will be overwritten when the framework updates.
 */

class setting {

	private $ffm;
	private $arr_display_errors = [0 => "On Console", 1 => "Display to Window"];
	private $arr_smtp_secure = [0 => "false", 1 => "tls", 2 => "ssl"];
	private $arr_lang_priority = [0 => "Browser", 1 => "Default Language"];
	private $arr_lang = ["en" => "English", "jp" => "Japanese"];
	private $arr_force_testmode = ["0" => "Production Mode", "1" => "Developer mode"];

	function __construct(Controller $ctl) {
		$this->ffm = $ctl->db("setting");
		$ctl->assign("arr_customize", [0 => "Default", 1 => "Customize"]);
		$ctl->assign("arr_onoff", [0 => "On", 1 => "Off"]);
		$ctl->assign("arr_display_errors", $this->arr_display_errors);
		$ctl->assign("arr_smtp_secure", $this->arr_smtp_secure);
		$ctl->assign("arr_lang_priority", $this->arr_lang_priority);
		$ctl->assign("arr_lang", $this->arr_lang);
		$ctl->assign("arr_force_testmode", $this->arr_force_testmode);
		
		$this->currency_list = include (__DIR__."/currency.php");
		$ctl->assign("currency_list", $this->currency_list);

		$ctl->set_square();
	}

	function update(Controller $ctl) {
		$setting = $this->ffm->get(1);
		if ($setting == null) {
			$setting = array();
			$this->ffm->insert($setting);
		}
		foreach ($ctl->POST() as $key => $val) {
			$setting[$key] = $val;
		}
		if (empty($setting["rewrite_rule_root"])) {
			$setting["rewrite_rule_root"] = "login";
		}
		if (empty($setting["rewrite_rule_function"])) {
			$setting["rewrite_rule_function"] = "page";
		}
		if (empty($setting["currency"])) {
			$setting["currency"] = "JPY";
		}
		$this->ffm->update($setting);

		// Replace .htaccess
		$path_server = $_SERVER['REQUEST_URI'];
		$directoryPath = pathinfo($path_server, PATHINFO_DIRNAME);
		if(endsWith($directoryPath, "/fw4")){
			$directoryPath = substr($directoryPath,0, strlen($directoryPath)-4);
		}
		if($directoryPath == "/"){
			$directoryPath = "";
		}
		$template = file_get_contents(dirname(__FILE__) . "/Templates/htaccess.tpl");
		$template = str_replace('{$class}', $setting["rewrite_rule_root"], $template);
		$template = str_replace('{$function}', $setting["rewrite_rule_function"], $template);
		$template = str_replace('{$subpath}',$directoryPath,$template);
		file_put_contents(dirname(__FILE__) . "/../../../.htaccess", $template);

		// Replace robots.txt
		if (!empty($setting["robots"])) {
			file_put_contents(dirname(__FILE__) . "/../../../robots.txt", $setting["robots"]);
		}

		// Login Logo
		if ($ctl->is_posted_file("login_logo")) {
			$ctl->save_posted_file("login_logo", "login_logo");
		}

		// Test Mail
		if ($ctl->POST("send_test_mail") == 1) {
			$setting = $this->ffm->get(1);
			$ctl->set_session("setting", $setting);
			$to = $setting["smtp_email_test"];
			$ctl->send_mail_string($setting["smtp_from"], $to, "TEST", "This is test mail from setting.\n" . $_SERVER["HTTP_HOST"]);
		}

		$ctl->res_reload();
	}

	function page(Controller $ctl) {

		$setting = $this->ffm->get(1);

		if (empty($setting["user_type_name0"])) {
			$setting["user_type_name0"] = "User";
		}
		if (empty($setting["currency"])) {
			$setting["currency"] = "JPY";
		}

		$ctl->assign("setting", $setting);

		$ctl->show_multi_dialog("setting", "index.tpl", "Setting", 800, "_edit_button.tpl");
	}

	function json_upload(Controller $ctl) {
		$ctl->show_multi_dialog("upload", "upload.tpl", "Setting JSON Upload", 600, true, true);
	}

	function json_upload_exe(Controller $ctl) {

		$save_filename = 'system_setting.json';
		$ctl->save_posted_file('file', $save_filename);

		//get saved file path
		$file_path = $ctl->get_saved_filepath($save_filename);

		$json = file_get_contents($file_path);
		$setting = json_decode($json, true);
		$this->ffm->update($setting);

		$ctl->close_multi_dialog("upload");
		$this->page($ctl);
	}

	function json_download(Controller $ctl) {
		$setting = $this->ffm->get(1);
		$ctl->res_json($setting, $post['filename']);
	}

	function delete_login_logo(Controller $ctl) {
		$ctl->delete_saved_file("login_logo");
		$ctl->show_notification_text("Login Logo has been deleted.");
	}

	function square(Controller $ctl) {

		// Get customer informations before input credit card.
		$name = "Test";
		$mail = "info@soshiki-kaikaku.com";
		$address = "テスト住所";
		$amount = 100; // 100 Yen

		$callback_parameter_array = ["name" => $name, "mail" => $mail, "address" => $address, "amount" => $amount];

		// Show credit card dialog.
		$ctl->show_square_dialog("setting", "pay", $callback_parameter_array);
	}

	function pay(Controller $ctl) {

		// You can call set_square($square_application_id=,$square_access_token)  here to change square account.
		// $ctl->set_square("","");
		// Get parameters from the framework.
		$param = $ctl->get_square_callback_parameter_array();

		try {
			// Regist Customer SQUARE and get customer id
			$square_customer_id = $ctl->square_regist_customer($param["name"], $param["mail"], $param["address"]);

			// Regist the Card
			$card_id = $ctl->square_regist_card($square_customer_id);

			// ------------------------------------------------------------------------------
			// If you save square_customer_id and card_id, You can execute payment any time , any amount!
			// ------------------------------------------------------------------------------
			// Execute Payment
			$result = $ctl->square_payment($square_customer_id, $card_id, $param["amount"]);

			if ($result) {
				$ctl->close_square_dialog();
				$ctl->assign("msg", "SUCCESS");
				$ctl->show_multi_dialog("square_dialog", "square_result.tpl", "Square Result");
			} else {
				$ctl->close_square_dialog();
				$ctl->assign("msg", "FAIL");
				$ctl->show_multi_dialog("square_dialog", "square_result.tpl", "Square Result");
			}
		} catch (Exception $e) {
			$ctl->show_square_dialog("square_sample", "pay", $param, $e->getMessage());
		}
	}
}
