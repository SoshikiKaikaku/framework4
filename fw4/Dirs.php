<?php

class Dirs {

	public $appdir_user;
	public $appdir_fw;
	public $datadir;
	public $logdir;
	public $tmpdir;
	
	function __construct() {
		$this->appdir_user = __DIR__ . "/../classes/app";
		$this->appdir_fw = __DIR__ . "/app";
		$this->datadir = __DIR__ . "/../classes/data";
		$this->logdir = __DIR__ . "/../classes/log";
		$this->tmpdir = __DIR__ . "/../classes/log/tmp";
		
		if(!is_dir($this->datadir)){
			mkdir($this->datadir);
		}
		if(!is_dir($this->logdir)){
			mkdir($this->logdir);
		}
		if(!is_dir($this->tmpdir)){
			mkdir($this->tmpdir);
		}
	}
	
	function get_class_dir($class){
		
		// common
		if($class == "common"){
			$dir = "$this->datadir/_common/";
			if(!is_dir($dir)){
				mkdir($dir);
			}
			return $dir;			
		}else{
			$classdir = "$this->appdir_user/$class/$class.php";
			if(is_file($classdir)){
				return "$this->appdir_user/$class/";
			}else{
				$classdir = "$this->appdir_fw/$class/$class.php";
				if(is_file($classdir)){
					return "$this->appdir_fw/$class/";
				}else{
					throw new Exception("There is no class dir: " . $class);
				}
			}
		}
	}
}
