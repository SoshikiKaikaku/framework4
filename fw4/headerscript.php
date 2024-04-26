<?php

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Asia/Tokyo');
mb_language("Japanese");
mb_internal_encoding("UTF-8");

ignore_user_abort(true); // ブラウザによる途中中断禁止

session_start();

function get_appcode() {
	$url_ex = explode(".", $_SERVER['HTTP_HOST']);
	$appcode = substr($url_ex[0], 4);
}

function text_to_link($text, $target = "_blank") {
	$pattern = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/';
	$replace = '<a href="$1" target="' . $target . '">$1</a>';
	$text = preg_replace($pattern, $replace, $text);
	return $text;
}

// WARNINGでもスタックトレースを表示させる（@が効かなくなるため、完全に開発用）
$show_stack_trace_for_warning=false;
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // E_WARNINGレベルのエラーのみを扱う
    if ($errno == E_WARNING) {
        // スタックトレースを取得
        $trace = debug_backtrace();
        
        // スタックトレースの内容を表示
	echo "<pre>";
        echo "Warning: $errstr in $errfile on line $errline\n";
        echo "Stack trace:\n";
        foreach ($trace as $key => $frame) {
            if ($key > 0) { // 最初のフレームはこのエラーハンドラ自体なので除外
                echo "#$key {$frame['file']}({$frame['line']}): ";
                if (isset($frame['class'])) {
                    echo "{$frame['class']}{$frame['type']}";
                }
                echo "{$frame['function']}(";
                echo implode(', ', array_map(function ($item) {
                    return is_object($item) ? get_class($item) : $item;
                }, $frame['args']));
                echo ")\n";
            }
        }
	echo "</pre>";
    }
    /* エラーを標準エラーハンドラに渡す必要がない場合は、以下をコメントアウト */
    // return false;
}
// 独自のエラーハンドラをセット
if($show_stack_trace_for_warning){
	set_error_handler("customErrorHandler");
}

class Mcrypt {

	private $SECRET = 'eosiej482a$#!2(716';
	private $METHOD = 'AES-256-CBC';
	private $iv = "disu481749281djw";
	private $options=0;

	function __construct($secret = "", $iv = "") {
		if ($secret != "") {
			$this->SECRET = $secret;
		}
		if ($iv != "") {
			$this->iv = $iv;
		}
	}

	public function encrypt($input) {
		$encrypted = openssl_encrypt($input, $this->METHOD, $this->SECRET, $this->options, $this->iv);
		$base64 = base64_encode($encrypted);
		return urlencode($base64);
	}

	public function decrypt($encrypted) {
		$urldecode = urldecode($encrypted);
		$base64 = base64_decode($urldecode);
		$decrypted = openssl_decrypt($base64, $this->METHOD, $this->SECRET, $this->options, $this->iv);
		return $decrypted;
	}

}

function mb_wordwrap($str, $width = 35, $break = PHP_EOL) {
	$c = mb_strlen($str);
	$arr = [];
	for ($i = 0; $i <= $c; $i += $width) {
		$arr[] = mb_substr($str, $i, $width);
	}
	return implode($break, $arr);
}

//----------------------------------------
// 文字の前方の一致
//----------------------------------------
function startsWith($haystack, $needle) {
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

//----------------------------------------
// 文字の後方の一致
//----------------------------------------
function endsWith($haystack, $needle) {
	$length = strlen($needle);
	if ($length == 0) {
		return true;
	}

	return (substr($haystack, -$length) === $needle);
}

//----------------------------------------
// csvsafe
//----------------------------------------
function csvsafe($str) {
	return str_replace(array("\n", "\r", "\t", ",", "\""), "", $str);
}

function isHankaku($string) {
	return strlen($string) === mb_strlen($string, 'UTF-8');
}

function isEmail($email) {
    $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    return preg_match($pattern, $email);
}

function safe_download($dir, $file) {

	// basenameでファイル名自体を再取得することで安全になる
	$file = basename($file);

	header("Content-Type: application/octet-stream");
	header('Content-Length: ' . filesize($dir . "/" . $file));
	header('Content-Disposition: attachment; filename=' . urlencode($file));

	$h = fopen($dir . "/" . $file, "r");
	$buf = "";

	while (!feof($h)) {
		$buf = fread($h, 8192);
		echo $buf;
	}
	fclose($h);
}

function str_random($length = 8) {
	return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, $length);
}

function random_symbol($length = 8) {
	return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz!@#$%^&*()-_|{}[];:<>?/'), 0, $length);
}

// 月曜日を取得
function get_beginning_week_date($timestamp) {
	$w = date("w", $timestamp);
	if ($w == 0) {
		$rd = 6;
	} else {
		$rd = $w - 1;
	}
	$d = strtotime(date("Y/m/d", strtotime("-{$rd} day", $timestamp))); // 丁度にするために必要
	return $d;
}

// クローラーか判定する
function isClawler(){
	$ua = $_SERVER['HTTP_USER_AGENT'];
	
	$crawler_arr = array(
		"Googlebot"		// google
		,"Baiduspider"		// Baidu
		,"bingbot"		// Bing
		,"Yeti"			// NHN
		,"NaverBot"		// NaverBot
		,"Yahoo"		// Yahoo
		,"Tumblr"		// Tumblr
		,"livedoor"		// livedoor
	);
	foreach ($crawler_arr as $value) {
		if (stripos($ua, $value) !== false) {
		    return true;
		}
	}
	return false;
}