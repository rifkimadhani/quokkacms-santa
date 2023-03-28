<?php
define("LOGFILE", __DIR__."/../writable/logs/".date("Ymd").".log");

error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", LOGFILE);
ini_set('display_errors', 'Off');

class Log{
	public static function writeLn(string $message){
		$date = date('Y/m/d h:i:s ', time());
		error_log($date . $message . "\r\n", 3, LOGFILE);
	}
	public static function writeErrorLn(string $message){
		$date = date('Y/m/d h:i:s ', time());
		error_log($date . "*** ". $message . " ***\r\n", 3, LOGFILE);
	}
	public static function error(string $errCode, string $message){
		$date = date('Y/m/d h:i:s ', time());
		error_log($date . "*** ". $message . " ({$errCode}) ***\r\n", 3, LOGFILE);
	}
	public static function writeRequestUri(){
	    $host= Log::host(); //(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	    $all = $host.$_SERVER['REQUEST_URI'];

		Log::writeLn($all);
	}

    public static function host() : string {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return $protocol.$_SERVER['HTTP_HOST'];
    }

    public static function var_dump($object){
        $debug = var_export($object, true);
        $date = date('Y/m/d h:i:s ', time());
        error_log($date . $debug . "\r\n", 3, LOGFILE);
    }

}