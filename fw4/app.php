<?php 

use Smarty;
include "smarty-4.3.1/libs/Smarty.class.php";
$smarty = new Smarty();
include "headerscript.php";
include("fixed_file_manager/fixed_file_manager.php");
include("../classes/app/common/Controller.php");
include("Controller_class.php");
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
	include('stream_filter/Stream_Filter_Mbstring7.php');
}else{
	include('stream_filter/Stream_Filter_Mbstring8.php');
}
include("Dirs.php");

header("Cache-Control:no-cache,no-store,must-revalidate,max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma:no-cache");

//デフォルトでエスケープ（エスケープを解除するには、{$var nofilter}のようにする  テキストの改行は {$var|escape|nl2br nofilter}
$smarty->escape_html  = true;

//Add a dir for Original Smarty Plugin
$smarty->addPluginsDir(dirname(__FILE__) . "/smarty_plugins_org/");

//-----------------------------------------------------
// DIRS
//-----------------------------------------------------
$dir = new Dirs();

//-------------
// appcodeのセット
//-------------
$url_ex = explode(".",$_SERVER['HTTP_HOST']);
$url_ex2 = explode("-",$url_ex[0],2);
if($url_ex2[0] == "test" || $url_ex2[0] == "192"){
	$testserver = true;
}else{
	$testserver = false;
}
if(isset($url_ex2[1])){
	$appcode = $url_ex2[1];
	$smarty->assign("appcode",$appcode);
}else{
	$appcode = "";
}
$smarty->assign("hostname",$url_ex[0]);

//---------------------
// class と function の取得
//---------------------
if(isset($_GET["class"])){
	$class = $_GET["class"];
}else{
	$class="";
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	$class = $_GET["class"];
	if(isset($_GET["function"])){
		$function = $_GET["function"];
	}else{
		$function = "page";
	}
}else{
	if(empty($class)){
		$class = $_POST["class"];
	}
	$function = $_POST["function"];
}
if(empty($class) || empty($function)){
	echo "classもしくはfunctionが設定されていません。<br>class={$class}<br>function={$function}";
	return;
}
$smarty->assign("class",$class);
$smarty->assign("css_class",$class); // Default

// Lang
$lang = $_COOKIE["lang"];
if(empty($lang)){
	$lang = "en";
}
$smarty->assign("lang",$lang);
$smarty->assign("arr_lang",["en"=>"English","jp"=>"Japanese"]);

// Windowcodeの生成
$windowcode = null;
if(isClawler()){
	$windowcode = "";
}else{
	if(!empty($_GET["windowcode"])){
		$windowcode = $_GET["windowcode"];
	}else if(!empty($_POST["windowcode"])){
		$windowcode = $_POST["windowcode"];
	}else{
		$mc = new Mcrypt();
		$windowcode = $mc->encrypt(microtime() + random_int(1,10000000));
	}
}

// ベースのテンプレートディレクトリ指定
$base_template_dir = dirname(__FILE__) . "/Templates";
$smarty->assign("base_template_dir",$base_template_dir);

//画像強制更新用タイムスタンプ
$smarty->assign("timestamp", strtotime("now"));

// SETTING
$setting_fmt_dir = $dir->appdir_fw . "/setting/fmt";
$setting_data_dir = $dir->datadir  . "/setting/";
$ffm_setting = new fixed_file_manager("setting", $setting_data_dir,$setting_fmt_dir);
$setting = $ffm_setting->get(1);
if(empty($setting)){
	$d = array();
	$ffm_setting->insert($d);
	$setting = $ffm_setting->get(1);
}
if(empty($setting["secret"])){
	$setting["secret"] = random_symbol(18);
	$ffm_setting->update($setting);	
}
if(empty($setting["iv"])){
	$setting["iv"] = random_symbol(16);
	$ffm_setting->update($setting);
}
$ffm_setting->close();  //この後使わないのでクローズ

// 強制テストモード
if($setting["force_testmode"] == 1){
	$testserver = true;
}

//Viewport デフォルト
if(empty($setting["viewport_public"])){
	$smarty->assign("viewport_public","width=600,viewport-fit=cover");
}else{
	$smarty->assign("viewport_public",$setting["viewport_public"]);
}
if(empty($setting["viewport_base"])){
	$smarty->assign("viewport_base","width=device-width");
}else{
	$smarty->assign("viewport_base",$setting["viewport_base"]);
}

// Smartyにアサイン
$smarty->assign("windowcode",$windowcode);
$smarty->assign("testserver",$testserver);
$smarty->assign("arr_lang",["en"=>"English","jp"=>"Japanese"]);
$smarty->assign("lang",$_COOKIE["lang"]);
$smarty->assign("setting",$setting);

//コントローラーを作成
if(startsWith($class, "_")){
	$ctl = new Controller_class();
}else{
	// クラスファイルのディレクトリ決定
	$ctl = new Controller_class($class,$smarty);
}
$ctl->set_windowcode($windowcode);
$ctl->set_session("class",$class);
$ctl->set_session("appcode",$appcode);
$ctl->set_session("testserver",$testserver);
$ctl->set_session("setting",$setting);
$ctl->set_check_login(true); //デフォルトを設定
$ctl->set_called_function($function);
$ctl->set_called_parameters();
$ctl->set_userdir($dir->appdir_user);

// 強制Display(ajaxからの呼び出しで $ctl->display() を使った場合の動作
$display_html = $ctl->get_session("_DISPLAY");
if(!empty($display_html)){
	$ctl->set_session("_DISPLAY",null);
	echo $display_html;
	exit;
}
if($class=="_DISPLAY" && $function="_ARR"){
	$arr = $ctl->get_session("_DISPLAY_ARR");
	if($arr != null){
		echo json_encode($arr);
		$ctl->set_session("_DISPLAY_ARR", null);
	}
	exit;
}

// Vimeo``設定
if($class=="_SETTING" && $function="_VIMEO"){
	$arr = $ctl->get_vimeo_setting();
	echo json_encode($arr);
	exit;
}

// Vimeo thumbnail
if($class=="_VIMEO" && $function="_THUMBNAIL"){
	$vimeo_id = $ctl->POST("vimeo_id");
	$url = $ctl->get_vimeo_thumbnail($vimeo_id);
	echo json_encode(["url"=>$url]);
	exit;
}

// 選択肢の自動セット
$constant_names = $ctl->get_all_constant_array_names();
$smarty->assign("constant_array_name",$constant_names);
foreach ($constant_names as $key => $arr_name) {
    $constant_values = $ctl->get_constant_array($arr_name,false);
    $smarty->assign($arr_name , $constant_values );

    $constant_colors = $ctl->get_constant_array_color($arr_name);
    $smarty->assign($arr_name. "_colors" , $constant_colors );
}

//クラスを読み込み
$appobj = getClassObject($ctl,$class,$dir);

//init関数を実行（過去互換）
if(method_exists($appobj,"init")){
	$appobj->init($ctl);
}

// SQUAREの読み込み
if($ctl->get_square()){
	include("mysquare.php");
}

// public ファイルを削除（仕様変更・当分して削除する）
if(is_file($dir->get_class_dir($class) . "/public")){
	unlink($dir->get_class_dir($class) . "/public");
}

// コンストラクタ内で停止が指示された場合
if($ctl->flg_stop_executing_function){
	if(!$ctl->display_flg){
		$ctl->res();
	}
	$ctl->assign("add_css_public",$ctl->add_css_public);
	exit;
}

//$user_type_opt_colors[$user.type]
//ログインチェック
if($ctl->get_check_login()){
	// ログインが必要
	
	if(!$ctl->get_session("login")){
		if($_SERVER["REQUEST_METHOD"] == "GET"){
			header("Location: app.php?class=login");
		}else{
			if($_POST["class"] == "lang"){
				$arr = array();
				echo json_encode($arr);
			}else{
				$arr = ["location"=>"app.php?class=login"];
				echo json_encode($arr);
			}
		}
	}else{
		//ログインが必要なクラスを実行
		if(method_exists($appobj,$function)){
			$appobj->$function($ctl);
			$ctl->res();
		}else{
			echo "Class \"$class\" does not have a function \"$function\" in $class/$class.php.";
		}
	}
	
}else{
	//ログイン不要の場合
	if(method_exists($appobj, $function)){
		$appobj->$function($ctl);
	}
	if(!$ctl->display_flg){
		$ctl->res();
	}
	
	// 他クラスのCSSを読み込み（publicのみ）
	$ctl->assign("add_css_public",$ctl->add_css_public);
	
}


function getClassObject($ctl,$class,Dirs $dir){
	
	//クラスを動的読み出し
	$classfile = $dir->get_class_dir($class) . "/$class.php";

	try{
		include_once($classfile);
		try{
			$appobj = new $class($ctl);
		}catch(Exception $e){
			$appobj = new $class;
		}
	}catch(Error $e2){
		throw new Exception("There is a php file $class.php but can't make class instance:" . $class . " Error1:" . $e . " Error:" . $e2);
	}
	
	return $appobj;
}

