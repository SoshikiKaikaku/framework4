<?php

class ai_cron {

	private $ffm_ai_setting;

	public function __construct(Controller $ctl) {
		$ctl->set_check_login(false);
		$this->ffm_ai_setting = $ctl->db("ai_setting", "ai_settings");
	}

	public function execute_all(Controller $ctl) {

		// getting list type:0(original code) code_type:2(cron)
		$list = $this->ffm_ai_setting->select(["type", "code_type"], [0, 2]);
		$res = [];

		foreach ($list as $d) {
			$class = $d["class_name"];
			$function = $d["function_name"];
			$r = [
			    "class" => $class,
			    "function" => $function,
			];

			// Create a instance of classes and execute these.
			try {
				$dirs = new Dirs();
				$ctl->set_class($class);
				$appobj = getClassObject($ctl, $class, $dirs);
				$appobj->$function($ctl);
				$r["result"] = "true";
				$r["response"] = $ctl->get_res_array();
			} catch (Exception $e) {
				$r["result"] = "false";
				$r["response"] = $e->__toString();
			}
			$res[] = $r;
		}

		// smartyを戻す
		$ctl->set_class("ai_cron");

		// Response
		if ($ctl->GET("response_type") == "json") {
			echo json_encode($res, JSON_PRETTY_PRINT);
		} else {
			$result = "";
			foreach ($res as $r) {
				$rt = print_r($r, true);
				$rt = htmlspecialchars($rt);
				$rt = str_replace(" ", '&nbsp;', $rt);
				$rt = str_replace("Array", '', $rt);
				$result .= $rt;
			}
			$ctl->assign("result", $result);
			$ctl->show_multi_dialog("cron_result", "result.tpl", "Cron Result", 1200);
		}
	}
}
