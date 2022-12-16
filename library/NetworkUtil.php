<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 5/29/2019
 * Time: 11:51 AM
 */

//hanya dengan include file ini maka akan otomatis masuk ke file
require_once __DIR__ . '/Log.php';

class NetworkUtil
{

	/**
	 * Kirim message ke 1 ip
	 *
	 * @param $destIp
	 * @param $destPort
	 * @param $message
	 * @return string
	 */
	static public function send($destIp, $destPort, $message){

		$fp = fsockopen($destIp, $destPort, $errno, $errstr, 2);
		if (!$fp) {
//			echo "$errstr ($errno)<br />\n";
			return false;
		} else {
			fwrite($fp, $message);

//			while (!feof($fp)) {
//				$reply += fgets($fp, 128);
//			}

			fclose($fp);
		}
		return true;
	}

	/**
	 * Kirim message to many ip
	 *
	 * @param $arDestIp
	 * @param $destPort
	 * @param $message
	 */
	static function sendMany($arDestIp, $destPort, $message){

		foreach ($arDestIp as $destIp){
			self::send($destIp, $destPort, $message);
		}

	}
}