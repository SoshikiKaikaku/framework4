<?php

class ui_chat {

	private $fmt_ai_setting;
	private $fmt_ai_setting_parameters;
	private $fmt_ai_db;
	private $database_handling_function_name = [
	    1 => "add",
	    2 => "edit",
	    3 => "delete",
	    4 => "search",
	    5 => "table",
	    6 => "pdf",
	    7 => "csv",
	    8 => "mail",
	];
	private $fmt_user;
	private $flg_public = false;
	private $ajax_option;
	private static $ctl;

	function __construct(Controller $ctl) {
		
		if ($ctl->GET("function") == "img") {
			$ctl->set_check_login(false);
			return;
		}

		self::$ctl = $ctl;

		// This class is common management side and pubic side
		if ($ctl->GET("public") == "true" || $ctl->POST("public") == "true") {
			$this->flg_public = true;
			$ctl->set_check_login(false);
			$this->ajax_option = ["public" => "true"];
			$ctl->assign("public", "true");
		} else {
			$this->ajax_option = ["pubic" => "false"];
			$ctl->assign("public", "false");
		}

		// Square
		$ctl->set_square();

		
		$this->fmt_ai_setting = $ctl->db("ai_setting", "ai_settings");
		$this->fmt_ai_setting_parameters = $ctl->db("ai_setting_parameters", "ai_settings");
		$this->fmt_ai_db = $ctl->db("ai_db", "ai_db");
		$this->fmt_user = $ctl->db("user", "user");
		

	}

	//index page
	function page(Controller $ctl) {
		$post = $ctl->POST();

		// Make buttons
		$manage_items = $this->get_function_list();
		$ctl->assign("manage_items", $manage_items);

		// Reset chatGPT data
		$ctl->chatGPT_history_reset();

		$ai_setting_id = $post['ai_setting_id'];
		if ($ai_setting_id) {
			$ai_setting = $this->fmt_ai_setting->get($ai_setting_id);
			$post_arr = $this->ajax_option;
			$post_arr['ai_setting_id'] = $ai_setting_id;
			$post_arr['table_name'] = "test_parent";
			$ctl->ajax('ai_db_handling', $this->database_handling_function_name[$ai_setting['handling']], $post_arr);
		}

		if ($this->flg_public) {
			$ctl->display("public_index.tpl");
		} else {
			if ($post['from_ai']) {
				$ctl->show_multi_dialog("ui_chat", "index.tpl", "AI");
			} else {
				$ctl->show_main_area("ui_chat", "index.tpl", "AI");
			}
		}
	}

	function chat(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$prompt = $post['msg'];

		if (empty($prompt)) {
			return;
		}

		$ctl->chat_show_text($prompt, 'green');

		// Step1 Identify a function
		$manage_items = $this->get_function_list(true);
		if (count($manage_items) > 0) {
			$items = "";
			foreach ($manage_items as $f) {
				$items .= json_encode($f, JSON_PRETTY_PRINT) . "\n\n";
			}
			$ctl->assign("items", $items);
			$ret = $ctl->chatGPT("prompt_class.tpl", false, "user", 0, 10000);

			if ($ret != null) {
				$response = json_decode($ret, true);
				if ($response["type"] == "choose") {
					// A Function is choosen
					$ctl->chatGPT_history_reset();
					$ctl->chat_clear();
					$ctl->chat_show_text($response['information'], 'orange');
					$ctl->ajax($response['class'], $response['function']);
					return;
				}
			}
		}

		// Step2 (When step1 was fail)
		if (count($_SESSION['chatgpt_messages']) == 0) {
			$ai_text = $this->ai_training_text();
			$ctl->assign("ai_training_data", $ai_text);
			$prompt_ai_text = $ctl->fetch("prompt_ai_text.tpl");
			$ctl->chatGPT_history_add("system", $prompt_ai_text);
			$ctl->chatGPT_history_add("assistant", "OK");
		}
		$ret = $ctl->chatGPT($prompt, true, "user", 1, 4000);
		if ($ret != null) {
			$response = json_decode($ret, true);
			if ($response["type"] == "answer") {
				$ctl->chat_show_text($response["message"], 'orange');
			} else {
				// ChatGPT was not able to answer the question
				$ctl->chat_show_text($response["message"], 'orange');
			}
		}
	}

	function login(Controller $ctl) {

		// Cookie
		$cookie_login_id = $_COOKIE["login_id"];
		$cookie_password = $_COOKIE["password"];
		$login_id = $ctl->decrypt($cookie_login_id);
		$password = $ctl->decrypt($cookie_password);
		$ai_setting_id = $ctl->POST("ai_setting_id");
		$ctl->assign("ai_setting_id",$ai_setting_id);
		
		$ai_setting_id = $post["ai_setting_id"];
		if(!empty($ai_setting_id)){
			$ctl->assign("ai_setting_id", $ai_setting_id);
			$ai_setting = $this->fmt_ai_setting->get($ai_setting_id);
			$ctl->assign("ai_setting", $ai_setting);
		}

		// デフォルト表示
		$ctl->assign("login_id", $login_id);
		$ctl->assign("password", $password);

		$ctl->show_multi_dialog("login", "login.tpl", "Login");
	}

	function check(Controller $ctl) {

		$lang = $_POST["lang_selector"];
		$login_id = $ctl->POST("login_id");
		$password = $ctl->POST("password");
		$ai_setting_id = $ctl->POST("ai_setting_id");
		$ctl->assign("ai_setting_id",$ai_setting_id);

		//ユーザー（自分のサーバーのuserから探す）
		$user_list = $this->fmt_user->select(["login_id", "password", "status"], [$login_id, $password, 0], true);

		if (count($user_list) == 1) {
			$this->login_ok($ctl, $user_list[0], $login_id, $password,$ai_setting_id);
		} else {
			$ctl->assign("login_id", $login_id);
			$ctl->assign("err_password", "You can't login this system.");
			$ctl->show_multi_dialog("login", "login.tpl", "Login");
		}
	}

	function login_ok(Controller $ctl, $user, $login_id, $password,$ai_setting_id) {
		// mysession
		$ctl->set_session("login", true);

		foreach ($user as $key => $val) {
			if ($key == "id") {
				$ctl->set_session("user_id", $user["id"]);
			} else {
				$ctl->set_session($key, $val);
			}
		}

		//admin判定
		if ($user["type"] == 0) {
			$ctl->set_session("app_admin", true);
		}
		
		// change user workflow status
		$ai_setting = $this->fmt_ai_setting->get($ai_setting_id);
		if($ai_setting["change_after_workflow_status"]){
			$ctl->change_user_workflow_status($ai_setting["after_workflow_status"]);
		}

		//---------------
		// Cookie処理
		//---------------
		setcookie("login_id", $ctl->encrypt($login_id), strtotime('+30 days'));
		setcookie("password", $ctl->encrypt($password), strtotime('+30 days'));
		$ctl->res_redirect("app.php?class=ui_chat&function=page&public=true&windowcode=" . $ctl->POST("windowcode"));
	}

	function logout(Controller $ctl) {
		$ctl->set_session("login", false);
		$ctl->res_redirect("app.php?class=ui_chat&function=page&public=true");
	}

	function creat_acc(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign("data", $post);
		$next_step = $post["step"];
		
		$ai_setting_id = $post["ai_setting_id"];
		if(!empty($ai_setting_id)){
			$ctl->assign("ai_setting_id", $ai_setting_id);
			$ai_setting = $this->fmt_ai_setting->get($ai_setting_id);
			$ctl->assign("ai_setting", $ai_setting);
		}
		
		if (empty($post["step"])) {
			// Type an email
			$next_step = 1;
			$code = random_int(1000, 9999);
			$enc_code = $ctl->encrypt($code);
			$ctl->assign("vc", $enc_code);
		} else if ($post["step"] == 1) {
			// Send a mail to verify
			$ctl->assign("vc", $post["vc"]);
			$code = $ctl->decrypt($post["vc"]);
			$ctl->assign("code", $code); //メール用
			//重複チェック
			if (!$ctl->validate_duplicate("user", "user", "login_id", $post["email"])) {
				$ctl->assign("error", "Your email is already registered.");
				$next_step = 1;
			} else {
				try {
					$ctl->send_mail_prepared_format($post["email"], "verification_code",
						null, "Verification code", "verification_code.tpl");
					$ctl->set_session("create_account_email", $post["email"]);
					$next_step = 2;
				} catch (Exception $e) {
					$ctl->assign("error", "Please check your email again.");
					$next_step = 1;
				}
			}
		} else if ($post["step"] == 2) {
			// Verify the code
			$vc = $ctl->decrypt($post["vc"]);
			$ctl->assign("vc", $post["vc"]);
			$code = $post["verification_code"];
			if ($vc != $code) {
				$ctl->assign("error", "This code doesn't match. Please check it again.");
				$next_step = 2;
			} else {
				$next_step = 3;
			}
		} else if ($post["step"] == 3) {
			// Veryfy the name
			if (empty($post["name"])) {
				$ctl->assign("error", "Name is required.");
				$next_step = 3;
			} else {
				$ctl->set_session("create_account_name", $post["name"]);
				$ctl->set_session("create_account_type", $post["type"]);
				$next_step = 4;
			}
		} else if ($post["step"] == 4) {
			// Veryfy the password and create the account.
			$pattern = '/^[a-zA-Z0-9]+$/';
			if (!preg_match($pattern, $post["password"])) {
				$ctl->assign("error", "Only alphanumeric characters are allowed");
				$next_step = 4;
			} else {
				$ctl->set_session("create_account_password", $post["password"]);
				$ai_setting = $this->fmt_ai_setting->get($ai_setting_id);
				$c = $this->create_account($ai_setting);
				$ctl->assign("data", $c);
				$url = $_SERVER["HTTP_ORIGIN"] . dirname($_SERVER['REQUEST_URI']) . "/app.php?class=ui_chat&public=true";
				$ctl->assign("url", $url);
				$ctl->send_mail_prepared_format($c["email"], "account_created");
				return;
			}
		}

		$ctl->assign("step", $next_step);
		$ctl->show_multi_dialog("user_add", "create_account.tpl", "Add User", 800, true, true);
	}

	function create_account($ai_setting) {

		$c = [];
		$c["name"] = self::$ctl->get_session("create_account_name");
		$c["email"] = self::$ctl->get_session("create_account_email");
		$c["password"] = self::$ctl->get_session("create_account_password");
		$c["login_id"] = self::$ctl->get_session("create_account_email");
		if($ai_setting["change_after_type"] == 0){
			$c["type"] = self::$ctl->get_session("create_account_type");
		}else{
			$c["type"] = $ai_setting["after_type"];
		}
		
		if($ai_setting["change_after_workflow_status"] == 1){
			$c["workflow_status"] = $ai_setting["after_workflow_status"];
		}

		$this->fmt_user->insert($c);
		self::$ctl->close_multi_dialog("user_add");
		$msg = $ai_setting["message_after_execute"];
		self::$ctl->chat_show_text($msg, 'orange');
		return $c;
	}

	function payment_multi(Controller $ctl, $second_time = false) {
		$user_id = $ctl->get_login_user_id();
		$user = $this->fmt_user->get($user_id);

		$post = $ctl->POST();
		if (!$second_time) {
			// デフォルトでユーザー情報を入れる
			$ctl->assign("data", $user);
		}

		// Make the dropdown
		$list_ai = $this->fmt_ai_setting->select("type", 2);
		$setting = $ctl->get_setting();
		$arr = [];
		foreach ($list_ai as $key => $value) {
			if ($value['predefined_function'] == 2) {
				$arr[$value['id']] = $value['menu_name']
					. "  " . $setting["currency"]
					. " " . $value["price"];
			}
		}
		$ctl->assign("items", $arr);
		$ctl->show_multi_dialog("payment_multi", "payment_multi.tpl", "Payment", 800, true, true);
	}

	function payment(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign("data", $post);
		$pay_ai = $this->fmt_ai_setting->get($post['payment_id']);

		$flg = true;
		if (empty($post["name"])) {
			$ctl->assign("error_name", "Name is required.");
			$flg = false;
		}
		if (!isEmail($post["email"])) {
			$ctl->assign("error_email", "Email is required.");
			$flg = false;
		}
		if (empty($post["address"])) {
			$ctl->assign("error_address", "Address is required.");
			$flg = false;
		}
		if ($flg == false) {
			$this->payment_multi($ctl, true);
			return;
		}

		if ($pay_ai['price'] != 0) {
			//parameters sent to payment
			$callback_parameter_array = [
			    "name" => $post['name'],
			    "email" => $post['email'],
			    "address" => $post["address"],
			    "amount" => $pay_ai['price'],
			    "ai_setting_id" => $post['payment_id'],
			    "public" => $post["public"]
			];

			$ctl->close_multi_dialog("payment_multi");
			// Show credit card dialog.
			$ctl->show_square_dialog("ui_chat", "pay_exe", $callback_parameter_array, "", $pay_ai['price']);
		}
		//var_dump($callback_parameter_array);
	}

	public function pay_exe(Controller $ctl) {
		// Get parameters from the framework.
		$param = $ctl->get_square_callback_parameter_array();

		if (empty($param["name"])) {
			$param["name"] = $ctl->POST("name");
		}
		if (empty($param["email"])) {
			$param["email"] = $ctl->POST("email");
		}
		if (empty($param["address"])) {
			$param["address"] = $ctl->POST("address");
		}
		
		$ai_setting = $this->fmt_ai_setting->get($param["ai_setting_id"]);

		try {
			// Regist Customer SQUARE and get customer id
			$square_customer_id = $ctl->square_regist_customer($param["name"], $param["email"], $param["address"]);

			// Regist the Card
			$card_id = $ctl->square_regist_card($square_customer_id);

			// ------------------------------------------------------------------------------
			// If you save square_customer_id and card_id, You can execute payment any time , any amount!
			// ------------------------------------------------------------------------------
			// Execute Payment
			$result = $ctl->square_payment($square_customer_id, $card_id, $param["amount"]);

			if ($result) { //payment success
				$msg = $ai_setting["message_after_execute"];
				$ctl->chat_show_text($msg, 'orange');
				
				// change user workflow status
				if($ai_setting["change_after_workflow_status"]){
					$ctl->change_user_workflow_status($ai_setting["after_workflow_status"]);
				}
				
				$ctl->close_square_dialog();
			} else {
				$ctl->show_square_dialog("ui_chat", "pay_exe", $param, "Payment failed");
				return;
			}
		} catch (Exception $e) {
			$ctl->show_square_dialog("ui_chat", "pay_exe", $param, $e->getMessage());
			return;
		}
	}

	function ai_training_text() {
		$list = $this->fmt_ai_setting->select(["type", "predefined_function"], [2, 3]);
		if ($list) {
			$jprompt2 = "";
			foreach ($list as $keyai => $p) {
				if ($this->check_security($p)) {
					$jprompt2 .= "<title>\n" . $p["ai_title"] . "\n</title>\n";
					$jprompt2 .= "<contents>\n" . $p["ai_text"] . "\n</contents>\n";
					$jprompt2 .= "\n";
				}
			}
			return $jprompt2;
		} else {
			return "";
		}
	}

	function img(Controller $ctl) {
		$image_file = $ctl->GET("file");
		$ctl->res_image("images", $image_file);
	}

	function get_function_list($for_chatgpt = false) {
		$ai_setting_list = $this->fmt_ai_setting->getall("sort", SORT_ASC);
		$flg_payment = true;
		$manage_items = [];
		foreach ($ai_setting_list as $key => $ai_setting) {

			// Check Security
			if ($this->check_security($ai_setting)) {
				if ($ai_setting["type"] == 0 && $ai_setting["code_type"] == 1) {
					// Execute Original Code
					$arr = $ai_setting;
					$arr["ai_setting_id"] = $ai_setting["id"];
					$manage_items[] = $arr;
				} else if ($ai_setting["type"] == 1) {
					// DB Handling
					$ai_db = $this->fmt_ai_db->get($ai_setting["ai_db_id"]);
					$arr = $ai_setting;
					$arr["class_name"] = "ai_db_handling";
					$arr["function_name"] = $this->database_handling_function_name[$ai_setting["handling"]];
					$arr["table_name"] = $ai_db["tb_name"];
					$arr["ai_setting_id"] = $ai_setting["id"];
					$manage_items[] = $arr;
				} else if ($ai_setting["type"] == 2) {
					// Predefined Functions
					if ($ai_setting["predefined_function"] == 2) {
						// Payment
						if ($flg_payment) {
							$arr = array();
							$arr["class_name"] = "ui_chat";
							$arr["function_name"] = "payment_multi";
							$arr["menu_name"] = "Payment";
							$arr["ai_setting_id"] = $ai_setting["id"];
							$manage_items[] = $arr;
							$flg_payment = false;
						}
					} else if ($ai_setting["predefined_function"] == 4) {
						$log_user_id = self::$ctl->get_login_user_id();
						if (!$log_user_id) {
							// Create Account
							$arr = array();
							$arr["class_name"] = "ui_chat";
							$arr["function_name"] = "creat_acc";
							$arr["menu_name"] = $ai_setting["menu_name"];
							$arr["ai_setting_id"] = $ai_setting["id"];
							$manage_items[] = $arr;
						}
					}
				}
			}

			// Login doesn't need to check security
			if ($ai_setting["type"] == 2 && $ai_setting["predefined_function"] == 1) {
				if ($this->flg_public) {
					$log_user_id = self::$ctl->get_login_user_id();
					if ($log_user_id) {
						$arr = array();
						$arr["class_name"] = "ui_chat";
						$arr["function_name"] = "logout";
						$arr["menu_name"] = "Logout";
						$manage_items[] = $arr;
					} else {
						$arr = array();
						$arr["class_name"] = "ui_chat";
						$arr["function_name"] = "login";
						$arr["menu_name"] = "Login";
						$arr["ai_setting_id"] = $ai_setting["id"];
						$manage_items[] = $arr;
					}
				}
			}
		}
		return $manage_items;
	}

	// Check Security
	function check_security($ai_setting) {

		// checking Work On
		if ($this->flg_public) {
			if (!($ai_setting["work_on"] == 1 || $ai_setting["work_on"] == 3)) {
				return false;
			}
		} else {
			if (!($ai_setting["work_on"] == 1 || $ai_setting["work_on"] == 2)) {
				return false;
			}
		}

		// checking User Type
		$my_user_type = self::$ctl->get_session("type");
		if ($ai_setting["limit_user_type"] == 1) {
			// Limited by user type
			$user_type = $ai_setting["user_type"]; //array
			$tmp_flg = false;
			foreach ($user_type as $u) {
				if ($u == $my_user_type) {
					$tmp_flg = true;
				}
			}
			if (!$tmp_flg) {
				return false;
			}
		}

		// checking Workflow
		$my_work_flow = self::$ctl->get_session("workflow_status");
		if ($ai_setting["limit_workflow"] == 1) {
			// Limit by workflow status
			$workflow = $ai_setting["workflow_status"]; //array
			$tmp_flg = false;
			foreach ($workflow as $w) {
				if ($w == $my_work_flow) {
					$tmp_flg = true;
				}
			}
			if (!$tmp_flg) {
				return false;
			}
		}

		// Passed all check
		return true;
	}
}
