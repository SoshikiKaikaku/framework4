<?php

interface Controller {

	//新しいDBを作成
	function make_db($name,$class=null);
	
	//DBの接続を作成・取得
	function db($name,$class=null,$separated_by=null):FFM;
	
	//DBの接続をクローズ
	function db_close($name);
	
	//smartyを取得
	function smarty();
	
	//リダイレクト
	function res_redirect($url);
	
	//リロード
	function res_reload();
	
	//ダイアログ
	function res_dialog($title,$template,$options=array());
	
	//PDF
	function res_pdf($imgdir,$pdf_template,$download_filename,$title="印刷",$width=600);
	
	//レスポンス
	function append_res_data($key,$val);
	
	//jsonで応答を返す
	function res();
	
	//smarty assign
	function assign($key,$val);
	
	//テンプレートからHTMLを表示
	function display($template);
	
	//fetch
	function fetch($template);
	
	//リストをリセットする
	function list_reset();
	
	//POSTデータを返す
	function POST($key=null);
	
	//GETデータを返す
	function GET($key=null);
	
	//一部を更新
	function reload_area($id_or_class,$val);
	
	// 画像を出力
	function res_image($subdir,$filename,$class=null);

	
	//POSTされたファイル名を取得
	function get_posted_filename($post_name);
	
	//POSTされたファイルの拡張子を取得（小文字に変換）
	function get_posted_file_extention($post_name);
	
	//POSTされたファイルの保存
	function save_posted_file($post_name,$save_filename, $key=null);
	
	//POSTされたファイルがあるか調べる
	function is_posted_file($pot_name);
	
	//保存されたファイルがあるかチェック
	function is_saved_file($saved_filename);
	
	//保存されたファイルデータを応答する
	function res_saved_image($filename);
	
	//保存された画像のリサイズ
	function resize_saved_image($inputfile,$outputfile,$width,$quality=100);
	
	//POSTされた情報を見る
	function debug_post();
	
	//ログインチェックを設定
	function set_check_login($flg);
	
	//ログインチェックの状態を取得
	function get_check_login();
	
	//保存されたファイルのデータを返す
	function res_saved_file($filename);
	
	//保存されたファイルのパスを取得
	function get_saved_filepath($filename);
	
	//保存されたファイルを削除
	function remove_saved_file($filename);
	
	//CSVを返す
	function res_csv(&$row_arr,$encode="sjis-win",$ret="\r\n",$quote="");
	
	//データのdirを直接指定
	function set_data_dir($dir);
	
	//ファイルを保存
	function save_file($filename,$data);
	
	//マルチダイアログを表示
	function show_multi_dialog($dialog_name,$template,$title,$width=600,$fixed_bar_template=null,$options=array());

	//マルチダイアログをクローズ
	function close_multi_dialog($dialog_name,$class=null);
	
	
	function chart_set_type($type);
	
	function chart_append_scatter_value($x,$y);
	
	function chart_append_pie_value($label,$value,$background);
	
	function chart_append_scatter_dataset($label,$color,$options=array());
	
	//チャート
	function res_chart($canvas_id,$options=null);
	
	//POSTされたデータをインクリメント
	function increment_post_value($key,$increment_value);
	
	// debug info
	function get_debug_info();
	
	// var_dump
	function var_dump($message,$obj);
		
	function set_class($class);
	
	function get_session($key);
	
	function get_setting();
	
	function get_appcode();

	function set_session($key,$val);
	
	function get_windowcode();
	
	function set_windowcode($windowcode);
	
	function get_login_name();
	
	function get_login_id();
	
	function get_login_user_id();
	
	function get_login_type();
	
	function add_css_public($class);
	
	function show_main_area($dialog_name,$template,$title);
	
	function testserver();
	
	function set_square($square_application_id=null,$square_access_token=null,$square_location_id=null);
	
	function encrypt($str);
	
	function decrypt($encrypt);
	
	function send_mail_string($from,$to,$subject,$body,$attachment_files=null);
	
	function fetch_string($str);
	
	function append_area($key,$val);
	
	function get_lang();
	
	function show_notification($template,$width=600,$time=5);
	
	function show_pdf($pdf_template,$download_filename,$title="Print",$width=600);
	
	function login_node($room_name, $group_name, $name);
	
	function send_to_node($data,$room_name="",$group_name="",$user_id="");
	
	function send_pdf_to_node($pdf_template,$pdf_filename,$room_name="",$group_name="",$user_id="");
	
	function send_mail_prepared_format($to,$format_key,$attachment_files=null);
	
	function get_vimeo_thumbnail($vimeo_id);
	
	function markdown_to_html($text);
	
	function badge($id,$val);
	
	function ajax($class,$function,$post_arr=null);
	
	function set_called_function($name);
	
	function get_called_function();
	
	function close_all_dialog($exception);
	
	function map($tag_id="google_map",$lat=35.6947818,$lng=139.7763998,$zoom=0);
	
	function map_add_marker($location,$html);
	
	function get_classname();
	
	function delete_vimeo($vimeo_id);
	
	function add_tab($dialog_name,$tabname,$title,$selected,$post_arr);
        
        function qrcode_vcard($user);
        
        function qrcode_text($text);
        
        function res_json($array);
        
        function google_calendar_link($timestamp_start,$timestamp_end,$title,$description,$location="",$timezone="");
	
	function square_payment($square_customer_id,$card_id,$price,$currency="JPY");
	
	function api($api_url,$class, $function, $post_arr=[]);
	
	function show_notification_text($txt, $time = 2,$background="#4B70FF",$color="#FFF",$fontsize=24,$width=600);
	
	function copy_file_uploaded($src_filename,$to_filename);

	function unzip($zip_filename, $subdir);

	function get_filelist($subdir = null, $recursive = false, $flags = 0);

	function delete_folder($subdir);
	
	function random_number($length=8);
	
	function random_alphabet($length=8);

	function random_password($length=8);
	
	function text_to_speech($text, $lang='en-US', $voice='', $pitch=1, $speed=1);
	
	function translate($q,$language_source="ja",$language_target="en");
	
	function delete_saved_file($filename);
        
        function show_popup($template,$width=300,$height=200);
	
	function strtotime($str,$timezone);
	
	function date($format,$timestamp,$timezone="UTC");
	
	function chatGPT_history_reset();
	
	function chatGPT_history_add($role,$prompt);
	
	function chatGPT($prompt_or_smartytemplate, $use_history=false,$role="user",$temperature=0,$tokens=1000,$model="gpt-3.5-turbo-1106");
	
	function show_square_dialog($callback_class_name, $callback_function_name, $callback_parameter_array, $error_msg = "");
        
        function chat_show_text($message,$color);
        
        function chat_show_html($template_name);
	
	function chat_clear();
	
	function console_log($log);

	function add_constant_array($array_name,$key,$value,$color="#ccc");
	
	function is_constant_array($array_name);
	
	function get_all_constant_array_names($emptydata=false);
	
	function get_constant_array($array_name,$emptydata=false);
	
	function get_constant_array_color($array_name);
	
	function validate_duplicate($table_name, $ffm_class, $field_names, $target_values, $exclude_id = 0);
	
	function cron_log($log);
	
	function set_css_other_class($class_name);
	
	function get_mail_body_prepared_format($format_key);
	
	function stop_executing_function();
	
	function change_user_workflow_status($new_status);
}
