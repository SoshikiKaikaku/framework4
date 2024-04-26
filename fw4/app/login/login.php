<?php

class login {

	private $ffm_user;

	function __construct(Controller $ctl) {
		$ctl->set_check_login(false);
		$this->ffm_user = $ctl->db("user", "user");
	}

	function page(Controller $ctl) {		
		//ログイン画面を表示
		$ctl->display("login.tpl");
	}

	function login_form(Controller $ctl) {

		//ユーザが登録されているか確認
		$list = $this->ffm_user->getall();
		if(count($list) == 0){
			$user = array();
			$user["login_id"] = "admin";
			$user["password"] = str_random(8);
			$user["name"] = "Admin";
			$user["type"] = 0;
			$user["email"] = "";
			$user["workflow_status"] = 0;
			$this->ffm_user->insert($user);
			$ctl->assign("user", $user);
			$ctl->reload_area("#form", $ctl->fetch("form.tpl"));
			return;
		}
		
		// Cookie
		$cookie_login_id = $_COOKIE["login_id"];
		$cookie_password = $_COOKIE["password"];
		$cookie_login_status = $_COOKIE["login_status"];

		// Logo check
		if ($ctl->is_saved_file("login_logo")) {
			$ctl->assign("flg_login_logo", true);
		}

		// -------------
		// Cookieがログイン情報を持っている場合、チェックしてログイン処理
		// 持っていない場合は、ログインフォームを表示
		// -------------
		$login_id = $ctl->decrypt($cookie_login_id);
		$password = $ctl->decrypt($cookie_password);
		$user_list = $this->ffm_user->select(["login_id", "password", "status"], [$login_id, $password, 0], true);
		if (count($user_list) == 1 && $cookie_login_status == "logined") {
			$this->login_ok($ctl, $user_list[0], $login_id, $password);
		} else {
			// デフォルト表示
			$ctl->assign("login_id", $login_id);
			$ctl->assign("password", $password);

			// Cookie ログインリスト
			$cookie_login_list = $_COOKIE["login_list"];
			if (!empty($cookie_login_list)) {
				$login_list_json = $ctl->decrypt($cookie_login_list);
				$login_list = json_decode($login_list_json, true);
				$ctl->assign("login_list", $login_list);
			}

			$ctl->reload_area("#form", $ctl->fetch("form.tpl"));
		}
	}

	function check(Controller $ctl) {
		$lang = $_POST["lang_selector"];
		$login_id = $ctl->POST("login_id");
		$password = $ctl->POST("password");

		//ユーザー（自分のサーバーのuserから探す）
		$user_list = $this->ffm_user->select(["login_id", "password", "status"], [$login_id, $password, 0], true);

		if (count($user_list) == 1) {
			$this->login_ok($ctl, $user_list[0], $login_id, $password);
		} else {
			$ctl->assign("login_id", $login_id);
			$ctl->assign("err_password", "You can't login this system.");
			$ctl->reload_area("#form", $ctl->fetch("form.tpl"));
		}
	}

	function login_ok(Controller $ctl, $user, $login_id, $password) {
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

		//---------------
		// Cookie処理
		//---------------
		//ログインリストに追加
		$login_list = array();
		$login_list[$login_id] = $password;
		$cookie_login_list = $_COOKIE["login_list"];
		if (!empty($cookie_login_list)) {
			$login_list_json = $ctl->decrypt($cookie_login_list);
			$login_list_old = json_decode($login_list_json, true);
			if (!empty($login_list_old)) {
				foreach ($login_list_old as $key => $val) {
					if ($key != $id) {
						$login_list[$key] = $val;
					}
				}
			}
		}
		$login_list_json = json_encode($login_list);
		$cookie_login_list = $ctl->encrypt($login_list_json);
		setcookie("login_list", $cookie_login_list, strtotime('+30 days'));
		setcookie("login_id", $ctl->encrypt($login_id), strtotime('+30 days'));
		setcookie("password", $ctl->encrypt($password), strtotime('+30 days'));
		setcookie("login_status", "logined", strtotime('+30 days'));
		$ctl->res_redirect("app.php?class=base&windowcode=" . $ctl->get_windowcode());
	}

	function logo(Controller $ctl) {
		$ctl->res_saved_image("login_logo");
	}

	function logout(Controller $ctl) {
		setcookie("login_status", "");
		$ctl->res_redirect("app.php?class=login");
	}
}
