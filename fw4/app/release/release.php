<?php

/*
 *  YOU CAN'T CHANGE THIS PROJECT.
 *  It will be overwritten when the framework updates.
 */

class release {

	function page(Controller $ctl) {
		$url_ex = explode(".", $_SERVER['HTTP_HOST']);
		$url_ex2 = explode("-", $url_ex[0], 2);
		$appcode = $url_ex2[1];
		$appdir_test = dirname(__FILE__) . "/../../../classes/app";
		$ctl->assign("appdir_test", $appdir_test);

		$ctl->show_multi_dialog("Production", "release_confirm.tpl", "Production", 600, true, true);
		return;
	}

	function exe(Controller $ctl) {
		$url_ex = explode(".", $_SERVER['HTTP_HOST']);
		$url_ex2 = explode("-", $url_ex[0], 2);
		$appcode = $url_ex2[1];

		$appdir_test = dirname(__FILE__) . "/../../../classes/app/";
		$appdir_release = dirname(__FILE__) . "/../../../../app-$appcode/classes/app";
		$output = "";
		exec("rsync -avz --delete $appdir_test $appdir_release", $output);

		// コピーのためのデータフォルダ
		$data_test = dirname(__FILE__) . "/../../../classes/data";
		$data_release = dirname(__FILE__) . "/../../../../app-$appcode/classes/data";

		// langデータのコピー
		exec("cp -rf $data_test/lang/lang.dat $data_release/lang/lang.dat", $output2);

		// Email formatデータのコピー
		exec("cp -rf $data_test/email_format/email_format.dat $data_release/email_format/email_format.dat", $output4);

		// Menuデータのコピー
		exec("cp -rf $data_test/menu/menu.dat $data_release/menu/menu.dat", $output4);
		exec("cp -rf $data_test/menu/link.dat $data_release/menu/link.dat", $output4);

		// .htaccess のコピー
//		$htaccess_test = dirname(__FILE__) . "/../../../.htaccess";
//		$htaccess_release = dirname(__FILE__) . "/../../../../app-$appcode/";
//		exec("cp -rf $htaccess_test $htaccess_release",$output3);
		// templates_cの削除
		$template_c_release = dirname(__FILE__) . "/../../../../app-$appcode/classes/data/templates_c";
		exec("rm -rf $template_c_release");

		$ctl->assign("output", $output);

		$ctl->show_multi_dialog("Production", "finish.tpl", "Production", 600, true, true);
	}
}
