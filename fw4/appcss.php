<?php


ini_set('display_errors',1);
error_reporting(E_ALL & ~E_NOTICE);

header('Content-Type: text/css');
header("Cache-Control:no-cache,no-store,must-revalidate,max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma:no-cache");

$class = $_GET["class"];
$css_class = $_GET["css_class"];
if(empty($css_class)){
	$css_class = $class;
}

include("Dirs.php");
$dir_class = new Dirs();

if($class == "base"){
	$dir_base = $dir_class->get_class_dir("base");
	$file = $dir_base . "/style.css";
	$css = file_get_contents($file);
	echo $css;
	
	// FW
	$appdir = $dir_class->appdir_fw;
	$dirs = scandir($appdir);
	foreach($dirs as $dir){
		if($dir != "." && $dir != ".."){
			if(is_dir("$appdir/$dir")){
				if(is_file("$appdir/$dir/style.css")){
					$css_project = getcss("$appdir/$dir/style.css",$dir);
					echo "\n";
					echo $css_project;
				}
			}
		}
	}
	
	// User
	$appdir = $dir_class->appdir_user;
	$dirs = scandir($appdir);
	foreach($dirs as $dir){
		if($dir != "." && $dir != ".."){
			if(is_dir("$appdir/$dir")){
				if(is_file("$appdir/$dir/style.css")){
					$css_project = getcss("$appdir/$dir/style.css",$dir);
					echo "\n";
					echo $css_project;
				}
			}
		}
	}

}else{
	$file = $dir_class->get_class_dir($css_class) . "/style.css";
	echo getcss($file,$class);
}


function getcss($file,$class){
	if(is_file($file)){
		$css = file_get_contents($file);
		$lines = explode("\n", $css);
		$add_class = ".class_style_" . $class . " ";
		$newcss = "";
		foreach($lines as $line ){
			if((strpos($line,"{") !== false) 
					&& (strpos($line,"@") === false) 
					&& (strpos($line,"BODY") === false
					&& (strpos($line,"*") === false))){
				$newcss .= $add_class . str_replace(",", "," . $add_class,$line) . "\n";
			}else{
				$newcss .= $line . "\n";
			}
		}
		return $newcss;
	}
}

