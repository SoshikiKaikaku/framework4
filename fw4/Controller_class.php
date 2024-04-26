<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/phpqrcode/qrlib.php';

class Controller_class implements Controller {

	private $class;
	private $userdir;
	private $template_dir;
	private $dbarr;
	private $smarty;
	private $arr; //レスポンス用
	private $flg_check_login;
	private $chart_type;
	private $chart_data;
	private $chart_dataset;
	private $chart_label;
	private $chart_background;
	private $assign_list;
	private $debug_obj;
	private $flg_square = false;
	private $square_application_id;
	private $square_access_token;
	private $square_location_id;
	private $windowcode;
	public $add_css_public;
	private $node_login = false;
	private $called_function;
	public $display_flg = false;
	private $called_parameters;
	private $dirs;
	public $flg_stop_executing_function = false;

	function __construct($class = null, $smarty = null) {

		$this->dirs = new Dirs();
		$this->smarty = $smarty;
		$this->dbarr = array();
		$this->class = $class;
		$this->assign_list = array();
		$this->debug_obj = array();

		$this->set_class($class);
	}

	function api($api_url, $class, $function, $post_arr = []) {

		$post_arr["class"] = $class;
		$post_arr["function"] = $function;

		$postdata = http_build_query($post_arr);

		$header = array(
		    "Content-Type: application/x-www-form-urlencoded",
		);

		$opts = array('http' =>
		    array(
			'method' => 'POST',
			'header' => implode("\r\n", $header),
			'content' => $postdata
		    )
		);

		$context = stream_context_create($opts);

		//データベースすべてをロック解除
		foreach ($GLOBALS["lock_class_arr"] as $c) {
			$c->closeDatFile();
		}

		$result_json = file_get_contents($api_url, false, $context);

		//データベースを再度ロック
		foreach ($GLOBALS["lock_class_arr"] as $c) {
			$c->openDatFile(false, false);
		}

		$result = json_decode($result_json, true);
		if ($result == null) {
			throw new \Exception("API RECEIVED SERVER ERROR:" . $result_json);
		} else {
			if ($result["location"] == "app.php?class=login") {
				throw new \Exception("[API ERROR] The server isn't public page.");
			}
			return $result;
		}
	}

	function get_session($key) {
		if(isset($_SESSION[$this->windowcode][$key])){
			return $_SESSION[$this->windowcode][$key];
		}else{
			return null;
		}
	}

	function set_session($key, $val) {
		$_SESSION[$this->windowcode][$key] = $val;
	}

	function get_windowcode() {
		return $this->windowcode;
	}

	function set_windowcode($windowcode) {
		$this->windowcode = $windowcode;
	}

	function set_data_dir($dir) {
		$this->dirs->datadir = $dir;
	}

	function set_class($class) {
		$this->class = $class;
		if ($class == null) {
			return;
		}
		if (is_object($this->smarty)) {
			$this->smarty->template_dir = $this->dirs->get_class_dir($class) . "/Templates/";
			$this->smarty->compile_dir = $this->dirs->datadir . "/templates_c/" . "$class" . "/";
			if (!is_dir($this->smarty->compile_dir)) {
				mkdir($this->smarty->compile_dir, 0777, true);
			}
		}
	}

	function make_db($name, $class = null) {
		if ($class == null) {
			$class = $this->class;
		}
		$path_fmt = $this->dirs->get_class_dir($class) . "/fmt/$name.fmt";
		file_put_contents($path_fmt, "id,24,N\n");
	}

	//DBの接続を作成・取得
	function db($name, $class = null, $separated_by = null): FFM {

		if ($class == null) {
			$class = $this->class;
		}


		if ($separated_by != null) {
			$separated_by = "_" . $separated_by;
		} else {
			$separated_by = "";
		}

		$ddir = $this->dirs->datadir . "/" . $class . $separated_by;
		$fdir = $this->dirs->get_class_dir($class) . "/fmt";

		$key = $ddir . "/" . $name;
		if (!isset($this->dbarr[$key])) {
			$ffm = new fixed_file_manager($name, $ddir, $fdir);
			$this->dbarr[$key] = $ffm;
			return $ffm;
		} else {
			$ffm = $this->dbarr[$key];
			//check
			$ffm->check_hf();
			return $ffm;
		}
	}
	
	// DBの接続をクローズ(ffmのオブジェクトを使用）
	function close_db_by_ffm(FFM $ffm){
//		$name = $ffm->get_unique_key();
//		$this->db_close($name);
	}
	
	//DBの接続をクローズ
	function db_close($name) {
		//
		// Controllerからはクローズしない（fixed_file_managerとの整合性が合わなくなるみたい）
		//
//		if ($this->dbarr[$name] != null) {
//			$this->dbarr[$name]->close();
//			unset($this->dbarr[$name]);
//		} else {
//			throw new \Exception("db name error:" + $name);
//		}
	}

	//smartyを取得
	function smarty() {
		return $this->smarty;
	}

	//リダイレクト
	function res_redirect($url) {
		if ($this->POST("_call_from") == "appcon") {
			$this->arr["location"] = $url;
		} else {
			header("Location: $url");
		}
	}

	//リロード
	function res_reload() {
		$this->arr["reload"] = "do";
	}

//	//検索
//	function res_search(){
//		echo "do_search";
//	}
//	
//	//ダイアログを閉じる
//	function res_close(){
//		echo "close";
//	}
	//ダイアログ
	function res_dialog($title, $template, $options = array()) {
		$this->arr["title"] = $title;
		if (!empty($options["width"])) {
			$this->arr["width"] = $options["width"];
		} else {
			$this->arr["width"] = 600;
		}
		if (!empty($options["height"])) {
			$this->arr["height"] = $options["height"];
		} else {
			$this->arr["height"] = null;
		}
		$this->arr["dialog_options"] = $options;
		$this->smarty->assign("MYSESSION", $_SESSION[$this->windowcode]);
		$tmp = $this->smarty->fetch($template);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");
		$html = '<div class="class_style_' . $this->class . '">' . $tmp . '</div>';
		$this->arr["reloadarea"]["#dialog"] = $html;
		$this->res();
	}

	//PDF(OLD)
	function res_pdf($imgdir, $pdf_template, $download_filename, $title = "Print", $width = 600) {

		if ($imgdir == null) {
			$_SESSION["pdf_imgdir"] = $this->dirs->datadir . "/upload/";
		} else {
			$_SESSION["pdf_imgdir"] = $this->get_session("appdir") . "/" . $this->get_session("class") . "/" . $imgdir . "/";
		}
		$this->smarty->assign("MYSESSION", $_SESSION[$this->windowcode]);
		$this->smarty->escape_html = false;
		$_SESSION['pdf_text'] = $this->smarty->fetch($pdf_template);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");
		$_SESSION["pdf_filename"] = $download_filename;

		$this->show_multi_dialog("__pdf__" . date("Ymdhs"), dirname(__FILE__) . "/Templates/print_dialog.tpl", $title, $width);
	}

	//
	function show_pdf($pdf_template, $download_filename, $title = "Print", $width = 600) {

		$this->smarty->assign("MYSESSION", $_SESSION[$this->windowcode]);
		$this->smarty->escape_html = false;
		$_SESSION['pdf_text'] = $this->smarty->fetch($pdf_template);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");
		$_SESSION["pdf_filename"] = $download_filename;
		$_SESSION["pdf_imgdir"] = [$this->dirs->get_class_dir($this->class) . "/images/", $this->dirs->datadir . "/upload/"];

		$this->show_multi_dialog("__pdf__" . date("Ymdhs"), dirname(__FILE__) . "/Templates/print_dialog.tpl", $title, $width);
	}

	// Save PDF
	function save_pdf($imgdir, $pdf_template, $pdf_filename) {
		include_once(dirname(__FILE__) . "/pdfmaker/pdfmaker_class.php");

		if ($imgdir == null) {
			$imgdir = $this->dirs->datadir . "/upload/";
		} else {
			$imgdir = $this->get_session("appdir") . "/" . $this->get_session("class") . "/" . $imgdir . "/";
		}

		$txt = $this->smarty->fetch($pdf_template);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");

		$pdfmaker = new pdfmaker_class();
		$pdfmaker->makepdf($txt, $imgdir, $this->dirs->datadir . "/upload/" . $pdf_filename, "F");
	}

	//レスポンス
	function append_res_data($key, $val) {
		$checkarr = ["title", "width", "reloadarea", "height", "chart", "dialog_options"];
		foreach ($checkarr as $c) {
			if ($c == $key) {
				throw new \Exception($c . " はキーとして使用できません。");
			}
		}
		$this->arr[$key] = $val;
	}

	//reload_area
	function reload_area($key, $val) {
		$this->arr["reloadarea"][$key] = $val;
	}

	//append area
	function append_area($key, $val) {
		$this->arr["appendarea"][$key] = $val;
	}

	//jsonで応答を返す
	function res() {
		if ($this->arr == null) {
			echo "";
		} else {
			if (!$this->display_flg) {
				$this->arr["class"] = $this->class;
				if(isset($this->session_arr)){
					$this->arr["session"] = json_encode($this->session_arr);
				}
				echo json_encode($this->arr);
			}
		}
		$this->arr = null;
	}

	//smarty assign
	function assign($key, $val) {
		$this->smarty->assign($key, $val);
		$this->assign_list[$key] = $val;
	}

	function display($template) {
		$this->arr["class"] = $this->class;
		$this->set_session("_DISPLAY_ARR", $this->arr);

		if ($this->POST("_call_from") == "appcon") {
			$html = $this->smarty->fetch($template);
			$this->set_session("_DISPLAY", $html);
			$this->res_reload();
		} else {
			$this->smarty->assign("MYSESSION", $_SESSION[$this->windowcode]);
			$this->smarty->display($template);
			$this->display_flg = true;
		}
	}

	function fetch($template) {
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");
		$this->smarty->assign("MYSESSION", $_SESSION[$this->windowcode]);
		return $this->smarty->fetch($template);
	}

	function list_reset() {
		$this->set_session("morepage_startidx", 1);
		$this->set_session("search", null);
	}

	function POST($key = null) {
		if ($key == null) {
			return $_POST;
		} else {
			if(isset($_POST[$key])){
				return $_POST[$key];
			}else{
				return null;
			}
		}
	}

	function set_called_parameters() {
		$this->called_parameters = [];
		foreach ($_POST as $key => $val) {
			if ($key == "class" || $key == "function") {
				//
			} else {
				$this->called_parameters[$key] = $val;
			}
		}
	}

	function GET($key = null) {
		if ($key == null) {
			return $_GET;
		} else {
			return $_GET[$key];
		}
	}

	function res_image($subdir, $filename, $class = null) {

		//すべてのデータベースをクローズ
		foreach ($GLOBALS["lock_class_arr"] as $c) {
			$c->close();
		}

		//エラーを非表示
		error_reporting(~E_ALL);

		$filename = basename($filename);
		if ($class == null) {
			$class = $this->class;
		}
		$filepath = $this->dirs->get_class_dir($class) . "/$subdir/$filename";

		if (!is_file($filepath)) {
			echo "File not found: class=$class subdir=$subdir filename=$filename";
			return;
		}

		$fsize = filesize($filepath);
		if ($fsize > 1024) {
			$fsize = 1024;
		}

		$fp = fopen($filepath, "rb");

		$finfo = new finfo();
		$mimetype = $finfo->file($filepath, FILEINFO_MIME_TYPE);
		$contents = "";
		header('Content-Type: ' . $mimetype);
		while (!feof($fp)) {
			$contents = fread($fp, $fsize);
			echo $contents;
		}
		fclose($fp);
	}

	//
	function get_posted_filename($post_name) {
		return $_FILES[$post_name]['name'];
	}

	// POSTされたかの確認
	function is_posted_file($post_name) {
		if (empty($_FILES[$post_name]['name'])) {
			return false;
		} else {
			return true;
		}
	}

	//POSTされたファイルの拡張子を取得（小文字に変換）
	function get_posted_file_extention($post_name) {
		$pathinfo = pathinfo($this->get_posted_filename($post_name));
		return mb_strtolower($pathinfo["extension"]);
	}

	//POSTされたファイルを保存
	function save_posted_file($post_name, $save_filename, $key = null) {
		$upload_dir = $this->dirs->datadir . "/upload";
		//$this->log("saved_posted_file: post_name=" . $post_name . " saved_file_name:" . $upload_dir . "/$save_filename");
		//アップロード用のディレクトリ
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir);
		}

		//保存
		//$path = $_FILES[$post_name]['tmp_name'];
		if (!is_null($key)) {
			$path = $_FILES[$post_name]['tmp_name'][$key];
		} else {
			$path = $_FILES[$post_name]['tmp_name'];
		}

		if (is_file($upload_dir . "/$save_filename")) {
			unlink($upload_dir . "/$save_filename");
		}

		$res = move_uploaded_file($path, $upload_dir . "/$save_filename");
		if (!$res) {
			//$this->log("saved_posted_file: can't save file: " . $path . " ->" . $upload_dir . "/$save_filename");
		}
	}

	//ファイルを作成
	function save_file($filename, $data) {
		$upload_dir = $this->dirs->datadir . "/upload";

		//アップロード用のディレクトリ
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir);
		}

		//保存
		file_put_contents($upload_dir . "/$filename", $data);
	}

	//
	function is_saved_file($filename) {
		if (is_file($this->dirs->datadir . "/upload/$filename")) {
			return true;
		} else {
			return false;
		}
	}

	function blank_image() {
		header('Content-Type: image/png');
		header("Cache-Control:no-cache,no-store,must-revalidate,max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma:no-cache");
		$time_newest = strtotime("now");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", $time_newest) . "GMT");
		//透明PNG(1x1px)
		echo base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQI12NgYAAAAAMAASDVlMcAAAAASUVORK5CYII=");
	}

	function res_saved_image($filename) {
		//エラーを非表示
		error_reporting(~E_ALL);

		$filepath = $this->dirs->datadir . "/upload/$filename";

		if (!is_file($filepath)) {
			$this->blank_image();
			return;
		}

		$fp = fopen($filepath, "rb");
		if ($fp === false) {
			$this->blank_image();
			return;
		}

		$finfo = new finfo();
		$mimetype = $finfo->file($filepath, FILEINFO_MIME_TYPE);
		$contents = "";
		header('Content-Type: ' . $mimetype);
		header("Cache-Control:no-cache,no-store,must-revalidate,max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma:no-cache");
		$time_newest = strtotime("now");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", $time_newest) . "GMT");
		while (!feof($fp)) {
			$contents = fread($fp, 1024);
			echo $contents;
		}
		fclose($fp);
	}

	function res_saved_file($filename) {
		//エラーを非表示
		//error_reporting(~E_ALL);

		$filepath = $this->dirs->datadir . "/upload/$filename";

		$fp = fopen($filepath, "rb");
		if ($fp === false) {
			return;
		}

		$mimeType = 'application/octet-stream';
		header('Content-Type: ' . $mimeType);
		header('Content-Length: ' . filesize($filepath));

		while (!feof($fp)) {
			$contents = fread($fp, 1024);
			echo $contents;
		}
		fclose($fp);
	}

	function remove_saved_file($filename) {

		$filepath = $this->dirs->datadir . "/upload/$filename";
		if (is_file($filepath)) {
			unlink($filepath);
		}
	}

	function delete_saved_file($filename) {
		$this->remove_saved_file($filename);
	}

	//POSTされた情報を見る
	function debug_post() {
		echo "<pre>";
		var_dump($_POST);
		echo "</pre>";
	}

	function copy_file_uploaded($src_filename, $to_filename) {
		$file = $this->dirs->datadir . "/upload/$src_filename";
		if (!is_file($file)) {
			return;
		}

		$file_to = $this->dirs->datadir . "/upload/$to_filename";
		if (is_file($file_to)) {
			unlink($file_to);
		}
		copy($file, $file_to);
	}

	//----------------------------------------
	// イメージのリサイズ
	//----------------------------------------
	function resize_saved_image($inputfile, $outputfile, $width, $quality = 100) {

		$file = $this->dirs->datadir . "/upload/$inputfile";

		if (!is_file($file)) {
			return;
		}

		require_once "php-heic-to-jpg/src/HeicToJpg.php";

		$HeicToJpg = new Maestroerror\HeicToJpg();
		if ($HeicToJpg->isHeic($file)) {
			$HeicToJpg->convert("$file")->saveAs("$file");
		}

		$type = exif_imagetype($file);

		if ($type == IMAGETYPE_JPEG || $type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG
		) {

			// 元画像のファイルサイズを取得
			list($image_w, $image_h) = getimagesize($file);

			//元画像の比率を計算し、高さを設定
			$proportion = $image_w / $image_h;
			$height = $width / $proportion;

			//高さが幅より大きい場合は、高さを幅に合わせ、横幅を縮小
			if ($proportion < 1) {
				$height = $width;
				$width = $width * $proportion;
			}

			// サイズを指定して、背景用画像を生成
			$canvas = imagecreatetruecolor($width, $height);

			// ファイル名から、画像インスタンスを生成
			switch ($type) {
				case 1 :
					$image = imageCreateFromGif($file);
					break;
				case 2 :
					$image = imageCreateFromJpeg($file);
					break;
				case 3 :
					$image = imageCreateFromPng($file);
					break;
			}

			// 背景画像に、画像をコピーする
			imagecopyresampled($canvas, // 背景画像
				$image, // コピー元画像
				0, // 背景画像の x 座標
				0, // 背景画像の y 座標
				0, // コピー元の x 座標
				0, // コピー元の y 座標
				$width, // 背景画像の幅
				$height, // 背景画像の高さ
				$image_w, // コピー元画像ファイルの幅
				$image_h  // コピー元画像ファイルの高さ
			);

			// 画像を出力する
			imagejpeg($canvas, // 背景画像
				$file = $this->dirs->datadir . "/upload/$outputfile", // 出力するファイル名（省略すると画面に表示する）
				$quality  // 画像精度
			);

			// メモリを開放する
			imagedestroy($canvas);
		}
	}

	function set_check_login($flg) {
		$this->flg_check_login = $flg;
	}

	function get_check_login() {
		return $this->flg_check_login;
	}

	function get_saved_filepath($filename) {
		$filepath = $this->dirs->datadir . "/upload/$filename";
		return $filepath;
	}

	function res_csv(&$row_arr, $encode = "sjis-win", $ret = "\r\n", $quote = "") {
		foreach ($row_arr as $key => $d) {
			$row_arr[$key] = str_replace(array("\n", "\r", "\t", ",", "\""), "", $d);
			if ($quote == "") {
				$row_arr[$key] = mb_convert_encoding($row_arr[$key], $encode);
			} else {
				$t = mb_convert_encoding($row_arr[$key], $encode);
				$t = str_replace($quote, "\\" . $quote, $t);
				$row_arr[$key] = $quote . $t . $quote;
			}
		}
		echo implode(",", $row_arr) . $ret;
	}

	function show_multi_dialog($dialog_name, $template, $title, $width = 600, $fixed_bar_template = null, $options = array()) {

		// 以前のオプションとの互換
		if ($fixed_bar_template === false || $fixed_bar_template === true) {
			$fixed_bar_template = null;
		}
		if ($options === false || $options === true) {
			$options = array();
		}

		$dialog_name = str_replace([" ", ".", "#"], "", $dialog_name);

		$this->smarty->assign("dialog_name", $dialog_name);

		$this->smarty->assign("MYSESSION", $_SESSION[$this->windowcode]);
		$tmp = $this->smarty->fetch($template);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");

		if ($fixed_bar_template != null) {
			$fixed_bar = $this->smarty->fetch($fixed_bar_template);
			$this->console_log("Template:" . $this->class . "/" . $fixed_bar_template, "#CE5C00");
		}

		$html = '<div class="class_style_' . $this->class . '">' . $tmp . '</div>';
		$md["dialog_name"] = $dialog_name;
		$md["html"] = $html;
		$md["title"] = $title;
		$md["width"] = $width;
		$md["fixed_bar"] = $fixed_bar;
		$md["options"] = $options;
		$md["testserver"] = $this->get_session("testserver");
		$md["post"] = $_POST;
		$md["multi_dialog_zindex"] = $_POST["multi_dialog_zindex"];
		$this->arr["multi_dialog"][] = $md;
	}

	function show_notification_text($txt, $time = 2, $background = "#4B70FF", $color = "#FFF", $fontsize = 24, $width = 600) {
		$html = '<div class="class_style_' . $this->class . '">' . $tmp . '</div>';
		$style = "background:$background;color:$color;font-size:{$fontsize}px;";
		$md["html"] = '<div class="class_style_' . $this->class . '"><div class="fr_notification lang" style="' . $style . '">' . $txt . '</div></div>';
		$md["width"] = $width;
		$md["time"] = $time;
		$md["multi_dialog_zindex"] = $_POST["multi_dialog_zindex"];
		$this->arr["notifications"][] = $md;
	}

	function show_notification($template, $width = 600, $time = 5) {

		$this->smarty->assign("MYSESSION", $_SESSION[$this->windowcode]);
		$tmp = $this->smarty->fetch($template);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");

		$html = '<div class="class_style_' . $this->class . '">' . $tmp . '</div>';
		$md["html"] = $html;
		$md["width"] = $width;
		$md["time"] = $time;
		$md["multi_dialog_zindex"] = $_POST["multi_dialog_zindex"];
		$this->arr["notifications"][] = $md;
	}

	function show_sidemenu($template, $width = 300, $time = 200, $from = 'left') {
		$this->smarty->assign("MYSESSION", $_SESSION[$this->windowcode]);

		$menu_file = $this->dirs->appdir_user . "/common/menu.tpl";
		$this->assign('menu_file', $menu_file);

		$tmp = $this->smarty->fetch($template);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");

		$html = '<div class="class_style_' . $this->class . '">' . $tmp . '</div>';
		$md["html"] = $html;
		$md["width"] = $width;
		$md["time"] = $time;
		$md["from"] = $from;
		$md["multi_dialog_zindex"] = $_POST["multi_dialog_zindex"];
		$this->arr["sidemenu"][] = $md;
	}

	function show_popup($template, $width = 300, $height = 200) {
		$this->smarty->assign("MYSESSION", $_SESSION[$this->windowcode]);

		$menu_file = $this->dirs->appdir_user . "/common/menu.tpl";
		$this->assign('menu_file', $menu_file);

		$this->smarty->template_dir = $this->dirs->get_class_dir($this->class) . "/Templates/";
		$tmp = $this->smarty->fetch($template);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");

		$html = '<div class="class_style_' . $this->class . '">' . $tmp . '</div>';
		$md["html"] = $html;
		$md["width"] = $width;
		$md["height"] = $height;
		$md["multi_dialog_zindex"] = $_POST["multi_dialog_zindex"];
		$this->arr["popup"][] = $md;
	}

	function show_main_area($dialog_name, $template, $title) {
		$dialog_name = str_replace([" ", ".", "#"], "", $dialog_name);

		// F5で再読込したときに自動表示
		$alma = [
		    "class" => $this->class,
		    "function" => $this->called_function,
		    "parameters" => $this->called_parameters
		];
		$this->set_session("__AUTO_LOAD_MAIN_AREA", $alma);

		$this->smarty->assign("MYSESSION", $_SESSION[$this->windowcode]);
		$tmp = $this->smarty->fetch($template);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");
		$html = '<div class="class_style_' . $this->class . '">' . $tmp . '</div>';
		$md["dialog_name"] = $dialog_name;
		$md["html"] = $html;
		$md["title"] = $title;
		$md["testserver"] = $this->get_session("testserver");
		$md["post"] = $_POST;
		$this->arr["work_area"] = $md;
	}

	function ajax($class, $function, $post_arr = null) {

		if (is_array($post_arr)) {
			foreach ($post_arr as $key => $p) {
				if ($key == "class" || $key == "function" || $key == "cmd") {
					unset($post_arr[$key]);
				}
			}
		}

		$md = [];
		$md["class"] = $class;
		$md["function"] = $function;
		if ($post_arr == null) {
			$post_arr = array();
		}
		$md["post_arr"] = json_encode($post_arr);
		$md["cmd"] = "ajax";
		$this->arr["ajax"][] = $md;
	}

	function robot($classname_which_open_dialog, $dialog_name, $element_id, $command = "click", $data = null) {

		$md = [];
		$md["class"] = $classname_which_open_dialog;
		$md["dialog_name"] = $dialog_name;
		$md["element_id"] = $element_id;
		$md["data"] = $data;
		$md["robot_cmd"] = $command;
		$md["cmd"] = "robot";
		$this->arr["robot"][] = $md;
	}

	function close_multi_dialog($dialog_name, $class = null) {
		$md = [];
		$md["dialog_name"] = $dialog_name;
		if ($class == null) {
			$class = $this->class;
		}
		$md["class"] = $class;
		$md["cmd"] = "close";
		$this->arr["multi_dialog"][] = $md;
	}

	function chart_set_type($type) {
		$this->chart_type = $type;
	}

	function chart_append_scatter_value($x, $y) {
		if ($x == null) {
			$this->$chart_data[] = $y;
		} else {
			$this->chart_data[] = ["x" => $x, "y" => $y];
		}
	}

	function chart_append_pie_value($label, $value, $background) {
		$this->chart_data[] = $value;
		$this->chart_label[] = $label;
		$this->chart_background[] = $background;
	}

	function chart_append_scatter_dataset($label, $color, $options = array()) {

		$dataset_option = [
		    "label" => $label,
		    "borderColor" => $color,
		    "backgroundColor" => $color,
		    "borderWidth" => $color,
		    "borderWidth" => 2,
		    "pointRadius" => 5,
		    "tension" => 0,
		    "showLine" => true,
		    "fill" => false
		];

		foreach ($options as $key => $val) {
			$dataset_option[$key] = $val;
		}

		$dataset_option["data"] = $this->chart_data;
		$dataset_option["type"] = "scatter";
		$this->chart_dataset[] = $dataset_option;

		$this->chart_data = [];
	}

	//チャート
	function res_chart($canvas_id, $options = null) {

		// #がついていた場合は削除
		$canvas_id = str_replace("#", "", $canvas_id);

		if ($this->chart_type == "scatter") {

			if ($options == null) {
				$options = array();
			}

			$chart = array();
			$chart["chartid"] = $canvas_id;
			$chart["type"] = $this->chart_type;
			$chart["data"] = ["datasets" => $this->chart_dataset];
			$chart["options"] = $options;
			$this->arr["chart"] = $chart;
		} else if ($this->chart_type == "pie") {

			if ($options == null) {
				$options = [
				    "legend" => [
					"display" => true
				    ]
				];
			}

			$dataset_option["type"] = "pie";
			$dataset_option["data"] = $this->chart_data;
			$dataset_option["backgroundColor"] = $this->chart_background;
			$this->chart_dataset[] = $dataset_option;

			$chart = array();
			$chart["chartid"] = $canvas_id;
			$chart["type"] = $this->chart_type;
			$chart["data"] = ["labels" => $this->chart_label, "datasets" => $this->chart_dataset];
			$chart["options"] = $options;
			$this->arr["chart"] = $chart;
		}
	}

	// nullを回避してインクリメントする
	function increment_post_value($key, $increment_value) {
		if (empty($this->POST($key))) {
			return $increment_value;
		} else {
			//$this->var_dump("bbb");
			return $this->POST($key) + $increment_value;
		}
	}

	// Debug
	function get_debug_info() {
		
	}

	function var_dump($message, $obj = null) {
		$this->debug_obj[$message] = $obj;
	}

	function log($obj) {

		$this->debug_obj[] = $obj;

		$log_dir = $this->dirs->logdir;

		if (!is_dir($log_dir)) {
			mkdir($log_dir);
		}


		$file = $log_dir . "/log.txt";
		$fsize = @filesize($file);
		if ($fsize > 1000000) {
			$fp = fopen($file, "w");
		} else {
			$fp = fopen($file, "a");
		}

		flock($fp, LOCK_EX);
		fwrite($fp, date("Y/m/d H:i:s") . " ");
		if ($obj == null) {
			fwrite($fp, "NULL");
		} else {
			fwrite($fp, print_r($obj, true));
		}
		fwrite($fp, "\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	function get_appcode() {
		return $this->get_session("appcode");
	}

	function get_setting() {
		return $_SESSION[$this->windowcode]["setting"];
	}

	function get_login_name() {
		return $_SESSION[$this->windowcode]["name"];
	}

	function get_login_id() {
		return $_SESSION[$this->windowcode]["login_id"];
	}

	function get_login_user_id() {
		return $_SESSION[$this->windowcode]["user_id"];
	}

	function get_login_type() {
		return $_SESSION[$this->windowcode]["type"];
	}

	function get_user_db() {
		return $this->db("user", "user");
	}

	function show_vimeo_uploader($dialog_name, $callback_class_name, $callback_function_name, $callback_parameter_array) {

		$setting = $this->get_setting();

		$vimeo_client_id = $setting["vimeo_client_id"];
		$vimeo_client_secret = $setting["vimeo_client_secret"];
		$vimeo_access_token = $setting["vimeo_access_token"];

		$this->smarty->assign("callback_class_name", $callback_class_name);
		$this->smarty->assign("callback_function_name", $callback_function_name);
		$this->smarty->assign("callback_parameter_array", base64_encode(json_encode($callback_parameter_array)));
		$this->append_res_data("vimeo_client_id", $vimeo_client_id);
		$this->append_res_data("vimeo_client_secret", $vimeo_client_secret);
		$this->append_res_data("vimeo_access_token", $vimeo_access_token);
		$this->show_multi_dialog($dialog_name, dirname(__FILE__) . "/Templates/vimeo_upload_area.tpl", "Vimeo Uploader");
	}

	function get_vimeo_setting() {
		$setting = $this->get_setting();
		$arr["vimeo_client_id"] = $setting["vimeo_client_id"];
		$arr["vimeo_client_secret"] = $setting["vimeo_client_secret"];
		$arr["vimeo_access_token"] = $setting["vimeo_access_token"];
		return $arr;
	}

	function get_vimeo_id_uploaded() {
		return $_POST["uploaded_vimeo_id"];
	}

	function get_vimeo_title_uploaded() {
		return $_POST["vimeo_title"];
	}

	function get_vimeo_description_uploaded() {
		return $_POST["vimeo_description"];
	}

	function get_vimeo_callback_parameter_array() {
		if (!empty($_POST["callback_parameter_array"])) {
			return json_decode(base64_decode($_POST["callback_parameter_array"]), true);
		} else {
			return null;
		}
	}

	//Vimeoのサムネイルを取得する
	function get_vimeo_thumbnail($vimeo_id) {
		if (is_numeric($vimeo_id) && $vimeo_id > 0) {
			$url = "https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/" . $vimeo_id;

			$json = @file_get_contents($url);
			if (!empty($json)) {
				$arr = json_decode($json, true);
				return $arr["thumbnail_url"];
			}
		}
	}

	function delete_vimeo($vimeo_id) {
		$setting = $this->get_setting();

		$vimeo_client_id = $setting["vimeo_client_id"];
		$vimeo_client_secret = $setting["vimeo_client_secret"];
		$vimeo_access_token = $setting["vimeo_access_token"];

		$this->append_res_data("vimeo_client_id", $vimeo_client_id);
		$this->append_res_data("vimeo_client_secret", $vimeo_client_secret);
		$this->append_res_data("vimeo_access_token", $vimeo_access_token);
		$this->arr["delete_vimeo"]["vimeo_id"] = $vimeo_id;
	}

	function get_lang() {
		return $_COOKIE["lang"];
	}

	function set_square($square_application_id = null, $square_access_token = null, $square_location_id = null) {
		if ($square_application_id == null || $square_access_token == null || $square_location_id == null) {
			$setting = $this->get_setting();
			$this->square_application_id = $setting["square_application_id"];
			$this->square_access_token = $setting["square_access_token"];
			$this->square_location_id = $setting["square_location_id"];
		} else {
			$this->square_application_id = $square_application_id;
			$this->square_access_token = $square_access_token;
			$this->square_location_id = $square_location_id;
		}
		$this->flg_square = true;
	}

	function get_square() {
		return $this->flg_square;
	}

	function show_square_dialog($callback_class_name, $callback_function_name, $callback_parameter_array, $error_msg = "", $amount = "") {
		
		$this->smarty->assign("name",$callback_parameter_array["name"]);
		$this->smarty->assign("email",$callback_parameter_array["email"]);
		$this->smarty->assign("address",$callback_parameter_array["address"]);

		$this->smarty->assign("dialog_name", $dialog_name);
		$this->smarty->assign("square_application_id", $this->square_application_id);
		$this->smarty->assign("square_location_id", $this->square_location_id);
		$this->smarty->assign("callback_class", $callback_class_name);
		$this->smarty->assign("callback_function", $callback_function_name);
		$this->smarty->assign("callback_parameter_array", base64_encode(json_encode($callback_parameter_array)));
		if ($error_msg != "") {
			$error_msg = "An error is occured. Please try again.<br>" . $error_msg;
		}
		$settings = $this->get_setting();
		$this->smarty->assign("error", $error_msg);
		$this->smarty->assign("currency", $settings['currency']);
		$this->smarty->assign("amount", $amount);
		$this->smarty->assign("public",$this->POST("public"));

		$this->show_multi_dialog("SQUARE_DIALOG", dirname(__FILE__) . "/Templates/square.tpl", "SQUARE");
	}

	function close_square_dialog() {
		$this->close_multi_dialog("SQUARE_DIALOG");
	}

	function square_regist_customer($name, $mail, $address, $locality = "Japan", $country = "JP") {
		if (!class_exists("mysquare")) {
			throw new \Exception('Call $ctl->set_square() in constructor.');
		}

		$mysquare = new mysquare($this->square_access_token, $this->get_session("testserver"));
		return $mysquare->regist_customer($name, $mail, $address, $locality, $country);
	}

	function square_regist_card($square_customer_id) {
		$nonce = $_POST["nonce"];
		if (!class_exists("mysquare")) {
			throw new \Exception('Call $ctl->set_square() in constructor.');
		}
		$mysquare = new mysquare($this->square_access_token, $this->get_session("testserver"));
		return $mysquare->regist_card($square_customer_id, $nonce);
	}

	function square_payment($square_customer_id, $card_id, $price, $currency = null) {
		if (!class_exists("mysquare")) {
			throw new \Exception('Call $ctl->set_square() in constructor.');
		}
		$settings = $this->get_setting();
		if($currency == null){
		    $currency = $settings['currency'];
		}
		if(empty($currency)){
			$currency = "JPY";
		}
		$mysquare = new mysquare($this->square_access_token, $this->get_session("testserver"));
		return $mysquare->payment($square_customer_id, $card_id, $price, $currency);
	}

	function get_square_callback_parameter_array() {
		if (!empty($_POST["callback_parameter_array"])) {
			return json_decode(base64_decode($_POST["callback_parameter_array"]), true);
		} else {
			return null;
		}
	}

	function send_mail_prepared_format($to, $format_key, $attachment_files = null,$default_subject="",$default_template=null) {

		$setting = $this->get_setting();
		if (empty($setting["smtp_from"])) {
			throw new \Exception("You must set Mail Address (for from) on the setting.");
		}

		$ffm_email_format = $this->db("email_format", "email_format");
		$email_format_list = $ffm_email_format->select("key", $format_key);
		if (count($email_format_list) == 0) {
			if(empty($default_subject) || $default_template==null){
				throw new \Exception("There is no email_format which key is " . $format_key . ". You can set \$default_subject and \$default_template to send_mail_prepared_format.");
			}else{
				$dir = new Dirs();
				$arr = array();
				$arr["key"] = $format_key;
				$arr["template_name"] = $default_subject;
				$arr["subject"] = $default_subject;
				
				$template_path = $this->dirs->get_class_dir($this->class) . "/Templates/" . $default_template;
				$arr["body"] = file_get_contents($template_path);
				$ffm_email_format->insert($arr);
				$email_format = $arr;
			}
		}else{
			$email_format = $email_format_list[0];
		}

		$subject = $this->fetch_string($email_format["subject"]);
		$body = $this->fetch_string($email_format["body"]);
		$this->send_mail_string(null, $to, $subject, $body, $attachment_files);

	}
	
	function get_mail_body_prepared_format($format_key){
		$ffm_email_format = $this->db("email_format", "email_format");
		$email_format_list = $ffm_email_format->select("key", $format_key);
		$this->close_db_by_ffm($ffm_email_format);
		if ($email_format_list > 0) {
			$email_format = $email_format_list[0];
			$body = $this->fetch_string($email_format["body"]);
			return $body;
		} else {
			throw new \Exception("There is no email_format which key is " . $format_key);
		}
	}

	function send_mail($from, $to, $subject, $template, $attachment_files = null) {
		$body = $this->smarty->fetch($template);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");
		$this->send_mail_string($from, $to, $subject, $body, $attachment_files);
	}

	function send_mail_string($from, $to, $subject, $body, $attachment_files = null) {
		
		$this->console_log("### MAIL ###");
		$this->console_log("To:" . $to);
		$this->console_log("Subject:" . $subject);
		$this->console_log($body);

		require_once(dirname(__FILE__) . '/phpmailer/PHPMailer.php');
		require_once(dirname(__FILE__) . '/phpmailer/Exception.php');
		require_once(dirname(__FILE__) . '/phpmailer/SMTP.php');

		$setting = $this->get_setting();

		if ($from == null) {
			if (empty($setting["smtp_from"])) {
				throw new \Exception("You must set Mail Address (for from) on the setting.");
			}
			$from = $setting["smtp_from"];
		}

		$email = new PHPMailer(true);
		$email->CharSet = 'utf-8';
		$email->isSMTP();
		$email->Host = $setting["smtp_server"];
		$email->SMTPAuth = true;
		$email->Username = $setting["smtp_user"];
		$email->Password = $setting["smtp_password"];
		if ($setting["smtp_secure"] == 1) {
			$email->SMTPSecure = "tls";
		} else if ($setting["smtp_secure"] == 2) {
			$email->SMTPSecure = "ssl";
		} else {
			$email->SMTPSecure = false;
		}
		$email->Port = $setting["smtp_port"];

		try {
			$email->SetFrom($from);
			if (is_array($to)) {
				foreach ($to as $key => $value) {
					$email->addBCC($value);
				}
			} else {
				$email->addAddress($to);
			}

			$email->Subject = $subject;
			$email->Body = $body;

			if ($attachment_files != null) {
				if (is_array($attachment_files)) {
					foreach ($attachment_files as $f) {
						$email->addAttachment($this->dirs->datadir . "/upload/" . $f);
					}
				} else {
					$email->addAttachment($this->dirs->datadir . "/upload/" . $attachment_files);
				}
			}

			$email->Send();
		} catch (Exception $e) {
			if ($this->testserver()) {
				throw $e;
			}
		}
	}

	function add_css_public($class) {
		$this->add_css_public[] = $class;
	}

	function testserver() {
		return $this->get_session("testserver");
	}

	function encrypt($str) {
		$setting = $this->get_setting();
		$mc = new Mcrypt($setting["secret"], $setting["iv"]);
		return $mc->encrypt($str);
	}

	function decrypt($encrypt) {
		$setting = $this->get_setting();
		$mc = new Mcrypt($setting["secret"], $setting["iv"]);
		return $mc->decrypt($encrypt);
	}

	function fetch_string($str) {
		return $this->fetch("string:" . $str);
	}

	function login_node($room_name, $group_name, $name) {
		$md["room_name"] = $room_name;
		$md["group_name"] = $group_name;
		$md["user_id"] = $user_id;
		$this->arr["login_node"] = $md;

		$this->node_login = true;
	}

	function send_to_node($data, $room_name = "", $group_name = "", $user_id = "") {

		if (!$this->node_login) {
			throw new \Exception("You must call login_node before calling send_to_node");
		}

		$md["data"] = $data;
		$md["room_name"] = $room_name;
		$md["group_name"] = $group_name;
		$md["user_id"] = $user_id;
		$this->arr["send_to_node"][] = $md;
	}

	function close_all_dialog($exception) {
		$dialog_name = str_replace([" ", ".", "#"], "", $exception);
		$this->arr["close_all_dialog"]["exception"] = $dialog_name;
	}

	function send_file_to_node($filename, $room_name = "", $group_name = "", $user_id = "") {
		if ($this->is_saved_file($filename)) {
			$filepath = $this->dirs->datadir . "/upload/$filename";
			$d = file_get_contents($filepath);
			$base64 = base64_encode($d);
			$data["file"] = $base64;
			$data["filename"] = $filename;
			$this->send_to_node($data, $room_name, $group_name, $user_id);
		} else {
			throw new \Exception("file is not exist: " . $filename);
		}
	}

	function send_pdf_to_node($pdf_template, $pdf_filename, $room_name = "", $group_name = "", $user_id = "") {
		include_once(dirname(__FILE__) . "/pdfmaker/pdfmaker_class.php");

		$imgdir = [$this->dirs->get_class_dir($this->class) . "/images/", $this->dirs->datadir . "/upload/"];

		$txt = $this->smarty->fetch($pdf_template);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");

		$pdfmaker = new pdfmaker_class();
		$pdfmaker->makepdf($txt, $imgdir, $this->dirs->datadir . "/upload/" . $pdf_filename, "F");

		$this->send_file_to_node($pdf_filename, $room_name, $group_name, $user_id);

		$this->remove_saved_file($pdf_filename);
	}

	function markdown_to_html($text) {
		include_once(dirname(__FILE__) . "/markdown/Parsedown.php");
		$Parsedown = new Parsedown();
		$html = $Parsedown->text($text);

		return $html;
	}

	function badge($id, $val) {
		$md = [];
		$md["id"] = $id;
		$md["val"] = $val;
		$this->arr["badge"][] = $md;
	}

	function set_called_function($name) {
		$this->called_function = $name;
	}

	function get_called_function() {
		return $this->called_function;
	}

	function map($tag_id = "google_map", $lat = 35.6947818, $lng = 139.7763998, $zoom = 0) {
		$md = [];
		$md["tag_id"] = $tag_id;
		$md["lat"] = (float) $lat;
		$md["lng"] = (float) $lng;
		$md["zoom"] = $zoom;
		$this->arr["map"] = $md;
	}

	function map_add_marker($location, $html) {
		$md = [];
		$ex = explode(",", $location);
		$lat = str_replace("(", "", $ex[0]);
		$lng = str_replace([")", " "], "", $ex[1]);
		$md["location"] = ["lat" => (float) $lat, "lng" => (float) $lng];
		$md["html"] = $html;
		$this->arr["map_marker"][] = $md;
	}

	function get_classname() {
		return $this->class;
	}

	function add_tab($dialog_name, $tabname, $title, $selected, $post_arr) {

		$dialog_name = str_replace([" ", ".", "#"], "", $dialog_name);

		$md = ["dialog_name" => $dialog_name,
		    "tabname" => $tabname,
		    "title" => $title,
		    "selected" => $selected,
		    "post_arr" => $post_arr
		];
		$this->arr["add_tab"][] = $md;
	}

	function qrcode_vcard($user) {
		//file upload path
		$target = $this->dirs->datadir . "/upload/";
		$filename = 'qr-code-' . uniqid() . '.png';
		$filePath = $target . '' . $filename;

		// Contact details
		$name = $user['name'];
		$phone = $user['phone'];
		// QR code content with VACARD
		$qrCode = 'BEGIN:VCARD' . "\n";
		$qrCode .= 'FN:' . $name . "\n";
		$qrCode .= 'TEL;WORK;VOICE:' . $phone . "\n";
		$qrCode .= 'END:VCARD';
		// Attaching VCARD to QR code
		if (!file_exists($filePath)) {
			QRcode::png($qrCode, $filePath, QR_ECLEVEL_L, 3);
		}
		return $filename;
	}

	function qrcode_text($text) {
		//file upload path
		$target = $this->dirs->datadir . "/upload/";
		$filename = 'qr-code-text' . uniqid() . '.png';
		$filePath = $target . '' . $filename;

		// Attach the phone to text
		$qrContent = $text;

		// generating
		QRcode::png($qrContent, $filePath, QR_ECLEVEL_L, 3);

		return $filename;
	}

	function res_json($array) {
		//$data = json_encode($array, JSON_PRETTY_PRINT);
		$data = json_encode($array, JSON_PRETTY_PRINT);
		header('Content-Type: application/json');
		header('Expires: 0'); //No caching allowed
		header('Cache-Control: must-revalidate');
		header('Content-Length: ' . strlen($data));
		file_put_contents('php://output', $data);
	}

	function google_calendar_link($timestamp_start, $timestamp_end, $title, $description, $location = "", $timezone = "") {
		$timezone_list = DateTimeZone::listIdentifiers();
		if (in_array($timezone, $timezone_list)) {
			if (!empty($timestamp_start) && !empty($timestamp_end) && !empty($title)) {
				if (is_int($timestamp_start)) {
					$format_timestamp_start = date("Ymd", $timestamp_start) . "T" . date("His", $timestamp_start);
				} else {
					$format_timestamp_start = date("Ymd", strtotime($timestamp_start)) . "T" . date("His", strtotime($timestamp_start));
				}
				if (is_int($timestamp_end)) {
					$format_timestamp_end = date("Ymd", $timestamp_end) . "T" . date("His", $timestamp_end);
				} else {
					$format_timestamp_end = date("Ymd", strtotime($timestamp_end)) . "T" . date("His", strtotime($timestamp_end));
				}

				$url = "https://calendar.google.com/calendar/r/eventedit?text=" . urlencode($title) . "&details=" . urlencode($description) . "&location=" . urlencode($location) . "&dates=" . urlencode($format_timestamp_start) . "/" . urlencode($format_timestamp_end) . "&ctz=" . urlencode($timezone);
				return $url;
			} else {
				throw new \Exception("Please check parameter values. ");
			}
		} else {
			throw new \Exception("Timezone does not exist.");
		}
	}

	function unzip($filename, $subdir) {
		if (empty($filename) || empty($subdir))
			return false;

		$filename = $this->dirs->datadir . "/upload/" . $filename;
		$dir = $this->dirs->datadir . "/upload/" . $subdir;

		$zip = new ZipArchive;
		$res = $zip->open($filename);
		if ($res === TRUE) {
			if (!file_exists($dir))
				mkdir($dir, 0755);

			$zip->extractTo($dir);
			$zip->close();
			return true;
		} else {
			return false;
		}
	}

	function get_filelist($subdir = null, $recursive = false, $flags = 0) {
		$dir = $this->dirs->datadir . "/upload/" . $subdir;

		if ($recursive) {
			$files = $this->glob_recursive($dir . "/*", $flags);
		} else {
			$files = array_diff(scandir($dir), array('.', '..'));
		}

		return $files;
	}

	function glob_recursive($full_path, $flags = 0) {
		$files = glob(dirname($full_path) . "/*.{*}", GLOB_BRACE);
		foreach (glob(dirname($full_path) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
			$files = array_merge($files, $this->glob_recursive($dir . '/' . basename($full_path), $flags));
		}
		return $files;
	}

	function delete_folder($subdir) {
		if (empty($subdir))
			return false;

		$upload_dir = $this->dirs->datadir . "/upload/";
		$dir = $upload_dir . $subdir;
		$files = glob($dir . '/*');
		foreach ($files as $file) {
			is_dir($file) ? $this->delete_folder(str_replace($upload_dir, "", $file)) : unlink($file);
		}
		return rmdir($dir);
	}

	function random_number($length = 8) {
		return substr(str_shuffle("0123456789"), 0, $length);
	}

	function random_alphabet($length = 8) {
		return substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz"), 0, $length);
	}

	function random_password($length = 8) {
		return substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz0123456789!@#$%&:/"), 0, $length);
	}

	function translate($q, $language_source = "ja", $language_target = "en") {
		$setting = $this->get_setting();

		if (empty($setting["api_key_map"])) {
			throw new \Exception("Please set Google API KEY");
		}

		$url = "https://translation.googleapis.com/language/translate/v2";
		$data = [
		    'key' => $setting["api_key_map"],
		    'q' => $q,
		    'target' => $language_target,
		    'source' => $language_source
		];
		$postdata = http_build_query($data);
		$header = array(
		    "Content-Type: application/x-www-form-urlencoded",
		    "Referer: " . $_SERVER['HTTP_ORIGIN'],
		);
		$opts = array('http' =>
		    array(
			'method' => 'POST',
			'header' => implode("\r\n", $header),
			'content' => $postdata
		    )
		);
		$context = stream_context_create($opts);
		$result_json = file_get_contents($url, false, $context);
		$responseData = json_decode($result_json, TRUE);

		return $responseData['data']['translations'][0]['translatedText'];
	}

	function text_to_speech($text, $lang = 'en-US', $voice = '', $pitch = 1, $speed = 1) {

		$text = trim($text);

		if (empty($text))
			return false;


		$params = [
		    "audioConfig" => [
			"audioEncoding" => "LINEAR16",
			"pitch" => $pitch,
			"speakingRate" => $speed,
			"effectsProfileId" => [
			    "medium-bluetooth-speaker-class-device"
			]
		    ],
		    "input" => [
			"text" => $text
		    ],
		    "voice" => [
			"languageCode" => $lang, //ja-JP
			"name" => $voice //en-US-Wavenet-F
		    ]
		];

		$data_string = json_encode($params);

		$setting = $this->get_setting();

		$url = 'https://texttospeech.googleapis.com/v1/text:synthesize?fields=audioContent&key=' . $setting["api_key_map"];
		$handle = curl_init($url);

		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt(
			$handle,
			CURLOPT_HTTPHEADER,
			[
			    'Content-Type: application/json',
			    'Content-Length: ' . strlen($data_string),
			    'Referer: https://test-framework4.sv005.focus-business-platform.com'
			]
		);
		$response = curl_exec($handle);
		$responseDecoded = json_decode($response, true);
		curl_close($handle);
		if ($responseDecoded['audioContent']) {
			$speech_data = $responseDecoded['audioContent'];

			$file_name = strtolower(md5(uniqid($text)) . '.mp3');
			$path = $this->dirs->datadir . "/upload/";
			if (file_put_contents($path . $file_name, base64_decode($speech_data))) {
				return $file_name;
			}
		}

		return false;
	}

	function strtotime($str, $timezone) {

		// check
		$arr = DateTimeZone::listIdentifiers();
		if (!in_array($timezone, $arr)) {
			throw new \Exception("Timezone name is wrong : " . $timezone);
		}

		$moto = date_default_timezone_get();
		date_default_timezone_set($timezone);
		$ret = strtotime($str);
		date_default_timezone_set($moto);
		return $ret;
	}

	function date($format, $timestamp, $timezone = "UTC") {

		// check
		$arr = DateTimeZone::listIdentifiers();
		if (!in_array($timezone, $arr)) {
			throw new \Exception("Timezone name is wrong : " . $timezone);
		}

		$moto = date_default_timezone_get();
		date_default_timezone_set($timezone);
		$ret = date($format, $timestamp);
		date_default_timezone_set($moto);
		return $ret;
	}

	function chatGPT_history_reset() {
		$this->console_log("////RESET CHATGPT///");
		$_SESSION['chatgpt_messages'] = [];
	}
	
	function chatGPT_history_add($role,$prompt){
		$this->console_log($prompt);
		$_SESSION['chatgpt_messages'][] = ['role' => $role, 'content' => $prompt];
	}

	function chatGPT($prompt_or_smartytemplate, $use_history = false, $role = "user",$temperature=0,$tokens=1000,$model="gpt-3.5-turbo-1106") {

		if (endsWith($prompt_or_smartytemplate, ".tpl")) {
			$prompt = $this->smarty->fetch($prompt_or_smartytemplate);
			$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");
		} else {
			$prompt = $prompt_or_smartytemplate;
		}

		if (empty($prompt)) {
			$this->console_log("PROMPT IS EMPTY");
			return;
		}
		
		$this->console_log($prompt);

		$curl = curl_init();    // create cURL session

		$setting = $this->get_setting();

		// login to https://beta.openai.com/account/api-keys and create an API KEY
		$API_KEY = $setting["chatgpt_api_key"];
		if (empty($API_KEY)) {
			throw new \Exception("chatGPT API KEY is needed on the setting.");
		}

		//there is tow type chat, first is chat (we should send chat history) secont is just ask question, no history
		// 1
		$url = "https://api.openai.com/v1/chat/completions";
		// 2
		// $url = "https://api.openai.com/v1/completions";

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);

		$headers = array(// cUrl headers (-H)
		    "Content-Type: application/json",
		    "Authorization: Bearer $API_KEY"
		);

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		if ($use_history) {
			if (!isset($_SESSION['chatgpt_messages'])) {
				$_SESSION['chatgpt_messages'] = array();
			}
			$messages = $_SESSION['chatgpt_messages'];
			$messages[] = ['role' => $role, 'content' => $prompt];
		}else{
			$messages = ['role' => $role, 'content' => $prompt];
		}

		// 1
		$data = array(// cUrl data
		    "model" => $model, // choose your designated model
		    //"model" => "gpt-4-1106-preview",
		    "temperature" => $temperature, // temperature = creativity (higher value => greater creativity in your promts)
		    "max_tokens" => $tokens, // max amount of tokens to use per request
		    "messages" => $messages
		);

		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		$response1 = curl_exec($curl); // execute cURL
		$response2 = json_decode($response1, true);   // extract json from response
		$generated_text = $response2['choices'][0]['message']['content'];  // extract first response from json
		curl_close($curl);      // close cURL session

		if($generated_text == null){
			$this->console_log("chatGPT responsed null");
			$this->console_log($response2);
		}else{
			$this->console_log($generated_text);
		}

		return $generated_text;
	}

	function chat_show_text($message, $color) {
		if (empty($message)) {
			return;
		}
		$message = text_to_link($message);
		$arr = [
		    'html' => nl2br($message),
		    'reload_last_message' => false,
		    'color' => $color,
		    'type' => 'text'
		];
		$this->arr["chat"][] = $arr;
	}

	function chat_show_html($template_name) {
		$this->smarty->assign("MYSESSION", $_SESSION[$this->windowcode]);
		$template = $this->smarty->fetch($template_name);
		$this->console_log("Template:" . $this->class . "/" . $template, "#CE5C00");
		$html = '<div class="class_style_' . $this->class . '">' . $template . '</div>';
		$arr = [
		    'html' => $html,
		    'type' => 'html'
		];
		$this->arr["chat"][] = $arr;
	}

	function chat_clear() {
		$arr = [
		    "type" => "clear"
		];
		$this->arr["chat"][] = $arr;
	}

	function console_log($log, $color = "#064683") {
		if ($this->get_session("testserver")) {
			if (is_array($log)) {
				$arr = [
				    "log" => json_encode($log, JSON_PRETTY_PRINT),
				    "color" => $color
				];
			} else {
				$arr = [
				    "log" => $log,
				    "color" => $color
				];
			}
			$this->arr["console_log"][] = $arr;
		}
	}
	
	function cron_log($log){
		$this->arr["cron_log"][] = $log;
	}

	function set_userdir($user_dir) {
		$this->userdir = $user_dir;
	}

	function get_userdir() {
		return $this->userdir;
	}
	
	function is_constant_array($array_name){
		$ffm_constant_array = $this->db("constant_array", "constant_array");
		
		$list = $ffm_constant_array->select("array_name", $array_name);
		if(count($list) == 0){
			return false;
		}else{
			return true;
		}
	}
	
	function add_constant_array($array_name,$key,$value,$color="#ccc"){
		
		if(!endsWith($array_name, "_opt")){
			throw new \Exception("Array Name should ends with _opt");
		}
		
		$ffm_constant_array = $this->db("constant_array", "constant_array");
		$ffm_values = $this->db("values", "constant_array");
		
		$list = $ffm_constant_array->select("array_name", $array_name);
		if(count($list) == 0){
			$ca = ["array_name"=>$array_name];
			$ffm_constant_array->insert($ca);
		}else{
			$ca = $list[0];
		}
		$v = [
		    "constant_array_id"=>$ca["id"],
		    "key"=>$key,
		    "value"=>$value,
		    "color"=>$color
		];
		$ffm_values->insert($v);
	}

	function get_all_constant_array_names($emptydata=false) {
		if($emptydata){
			$constant_array_names[""]="";
		}else{
			$constant_array_names = [];
		}
		
		$ffm_constant_array = $this->db("constant_array", "constant_array");
		
		$constant_array = $ffm_constant_array->getall();
		
		foreach ($constant_array as $key => $value) {
			$constant_array_names[$key] = $value['array_name'];
		}
		return $constant_array_names;
	}

	function get_constant_array($array_name, $emptydata = false) {

		if ($emptydata) {
			$valuearr[0] = "";
		} else {
			$valuearr = [];
		}
		
		$ffm_constant_array = $this->db("constant_array", "constant_array");
		$ffm_values = $this->db("values", "constant_array");
		
		$constant_array = $ffm_constant_array->select(['array_name'], [$array_name])[0];
		$value_array = $ffm_values->select(['constant_array_id'], [$constant_array['id']]);
		
//		$this->close_db_by_ffm($ffm_constant_array);
//		$this->close_db_by_ffm($ffm_values);
		
		foreach ($value_array as $key => $value) {
			$valuearr[$value['key']] = $value['value'];
		}
		return $valuearr;
	}

	function get_constant_array_color($array_name) {
		
		$ffm_constant_array = $this->db("constant_array", "constant_array");
		$ffm_values = $this->db("values", "constant_array");
		
		$constant_array = $ffm_constant_array->select(['array_name'], [$array_name])[0];
		$value_array = $ffm_values->select(['constant_array_id'], [$constant_array['id']]);
		
//		$this->close_db_by_ffm($ffm_constant_array);
//		$this->close_db_by_ffm($ffm_values);
		
		$valuearr = array();
		foreach ($value_array as $key => $value) {
			if ($value['color']) {
				$valuearr[$value['key']] = $value['color'];
			}
		}
		return $valuearr;
	}

	function validate_duplicate($table_name, $ffm_class, $field_names, $target_values, $exclude_id = 0) {
		
		if(!is_array($field_names)){
			$arr_field_name = [$field_names];
		}else{
			$arr_field_name = $field_names;
		}

		if(!is_array($target_values)){
			$arr_target_values = [$target_values];
		}else{
			$arr_target_values = $target_values;
		}

		$ffm = $this->db($table_name, $ffm_class);
		$data = $ffm->select($arr_field_name, $arr_target_values);
//		$this->close_db_by_ffm($ffm);
		
		if (count($data) == 0) {
			$is_duplicate = true;
		} else {
			// exclude $exclude_id (Edit screen is needed)
			foreach ($data as $k => $d) {
				if ($d["id"] == $exclude_id) {
					unset($data[$k]);
				}
			}
			if (count($data) > 0) {
				$is_duplicate = false;
			}else{
				$is_duplicate = true;
			}
		}
		return $is_duplicate;
	}
	
	function change_user_workflow_status($new_status){
		
		$user_id = $this->get_session("user_id");
		if($user_id > 0){
			$ffm_user = $this->db("user","user");
			$user = $ffm_user->get($user_id);
			$old_workflow_status = $user["workflow_status"];
			$user["workflow_status"] = $new_status;
			$ffm_user->update($user);
			$this->console_log("User workflow status was changed from $old_workflow_status to $new_status");
		}
	}
	
	function get_res_array(){
		return $this->arr;
	}
	
	function set_css_other_class($class_name){
		$this->assign("css_class", $class_name);
	}
	
	function stop_executing_function(){
		$this->flg_stop_executing_function = true;
	}
}