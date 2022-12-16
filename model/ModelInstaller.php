<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 5/29/2019
 * Time: 11:47 AM
 */
require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../library/Adb.php';

class ModelInstaller
{

	static public function install($appId, $ip, $stbId){

		$pathApk = 'c:/xampp/htdocs/ott2/assets/apk';
		$apkFile = "NewAcappela-release-1.0.apk";

		$pathAndroid = '/data/mr';
		$configFile = 'config.json';
		$appId = 'com.madeiraresearch.hoteliptv2';
		$deviceAdminReceiver = '.MyDeviceAdminReceiver';
		$mainActivity = '.MainActivity';
		$port = 5555;

		//1. list all devices
//		$r = Adb::devices();
//		var_dump($r);

		//2. connect if no device attached
		$name = "{$ip}:{$port}"; //setiap adb akan mempergunakan device name, shg dalam 1 adb bisa multiple devices
		$r = Adb::connect($ip, $port);
		var_dump($r);

		//3. buat folder
		$r = Adb::mkdir($name, $pathAndroid);
		var_dump($r);

		$r = Adb::chmod($name, $pathAndroid);
		var_dump($r);

		//4. push apk ke path
		$r = Adb::push($name, $pathApk . "/{$apkFile}", $pathAndroid);
		var_dump($r);

		//5. push config ke file config.json
		$hostApi = 'https://homeconnectapp.com/ott2';
		$timeZone = 'Asia/Jakarta';
		$sessionId = 'SESSION-ACAPPELLA';
		$configJson = "{\"host_api\": \"{$hostApi}\", \"time_zone\": \"{$timeZone}\", \"session_id\": \"{$sessionId}\"}";

		//convert " --> \"
		$configJson = str_replace('"', '\"', $configJson);

		$r = Adb::shell( $name,"echo '{$configJson}' > {$pathAndroid}/{$configFile}" );
		var_dump($r);

		//6. install
		//adb shell pm install -t /data/mr/NewAcappela-release-1.0.apk
		$r = Adb::install($name,$pathAndroid . '/' . $apkFile);
		var_dump($r);

		//7. enable device owner
		//adb shell dpm set-device-owner com.madeiraresearch.hoteliptv2/.MyDeviceAdminReceiver
//		$r = Adb::enableDeviceOwner($appId, $deviceAdminReceiver);
//		var_dump($r);

		//8. run
		//adb shell am start -n com.madeiraresearch.hoteliptv2/.MainActivity
		$r = Adb::runApp($name, $appId, $mainActivity);
		var_dump($r);




		//disconnect from stb
		$r = Adb::disconnect($name);
		var_dump($r);

	}


}