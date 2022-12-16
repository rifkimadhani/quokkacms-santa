<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 7/26/2017
 * Time: 8:36 AM
 */
class Util
{
    public static function host() : string {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return $protocol.$_SERVER['HTTP_HOST'];
    }

    public static function getBaseUrl() : string {
        return self::host() . "/homeconnect/";
    }

	public static function fetchImage(string $url, $path, $filename):?string {
		$temp_filename = $path . '/' . $filename . '.temp';

		//get image from url
		$image = file_get_contents($url);
		if ($image==false) return null;

		//save image to file
		$result = file_put_contents($temp_filename, $image);
		if ($result==false) return null;

		//get extension dari file yg di save
		require_once 'HelperExif.php';
		$ext = HelperExif::getExtension($temp_filename);

		//add extension ke filename
		$filename = $filename . '.' . $ext;

		//rename temp filename --> filename
		if (rename($temp_filename, $path . '/' . $filename)){
			return $filename;
		}

		return null;
	}

	public static function findMacaddress(){

    	//create shell to execute arp
		//
		$ip = $_SERVER['REMOTE_ADDR'];
		$str = shell_exec('arp -a '.escapeshellarg($ip));

		//use regulax expression to parse str from arp
		preg_match('([0-9a-z]{2}:[0-9a-z]{2}:[0-9a-z]{2}:[0-9a-z]{2}:[0-9a-z]{2}:[0-9a-z]{2})', $str, $result );

		if (sizeof($result)==0) return null;
		return $result[0];
	}

    static $base_url = null; //global variable
    public static function baseUrl($relative = null){
        if (function_exists('base_url')) {
            return substr(base_url(), 0, -1); //delete char '/' di belakang
        }

        global $base_url; //import base_url dari global

        //compose base_url apabila blm di set
        if (is_null($base_url)){
            $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
            $base_url .= "://". @$_SERVER['HTTP_HOST'];
            $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
        }

        if (is_null($relative)) return substr($base_url, 0, -1);
        $base_url = Util::rel2abs($relative, $base_url);
        return substr($base_url, 0, -1);
    }

    public static function rel2abs($rel, $base)
    {
        /* return if already absolute URL */
        if (parse_url($rel, PHP_URL_SCHEME) != '' || substr($rel, 0, 2) == '//') return $rel;

        /* queries and anchors */
        if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

        /* parse base URL and convert to local variables:
         $scheme, $host, $path */
        extract(parse_url($base));

        /* remove non-directory element from path */
        $path = preg_replace('#/[^/]*$#', '', $path);

        /* destroy path if relative url points to root */
        if ($rel[0] == '/') $path = '';

        /* dirty absolute URL */
        $abs = "$host$path/$rel";

        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

        /* absolute URL is ready! */
        return $scheme.'://'.$abs;
    }
}