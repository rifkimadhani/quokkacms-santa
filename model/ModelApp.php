<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 5/30/2019
 * Time: 4:04 PM
 */

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';


class ModelApp {

	const SQL_GET_LATEST = 'SELECT * FROM tapp WHERE app_id=? AND version_code>? ORDER BY version_code DESC LIMIT 1';

	static public function getLatest($appId, $versionCode){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelApp::SQL_GET_LATEST);
			$stmt->execute( [$appId, $versionCode] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function install($appId, $ip, $stbId){
		require_once __DIR__ . '/../../library/Adb.php';
		require_once __DIR__ . '/../../library/Security.php';
		require_once __DIR__ . '/ModelSetting.php';


		//1. Get apk Path
		//		$pathApk = 'c:/xampp/htdocs/ott2/assets/apk';
		$stb = self::getLatest($appId, 0);
		if (is_null($stb)) {
			echo errCompose(ERR_FAIL_GET_LATEST_APP);
			die();
		}
		$pathApk = $stb['path'];

		//2. get apk filename
		//		$apkFile = "NewAcappela-release-1.0.apk";
		$apkFile = basename($pathApk);

		//3. get MainActiviry
		//		$mainActivity = '.MainActivity';
		$mainActivity = $stb['main_activity'];


		//4. buat sessionId utk stbId yg dimaksud
		//		$sessionId = 'SESSION-ACAPPELLA';
		$stbSession = substr($stbId . Security::random(24),0,24);

		$r = ModelStb::createStbSession($stbId, $stbSession);
		if ($r==0){
			echo errCompose(ERR_CREATE_STB_SESSION_FAIL);
			die();
		}

		$sessionId = $stbSession;
		$hostApi = ModelSetting::getHostApi();//'https://homeconnectapp.com/ott2';
		$timeZone = ModelSetting::getTimezone();//'Asia/Jakarta';


		$pathAndroid = '/data/mr';
		$configFile = 'config.json';
		$port = 5555;

		//Begin install process
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
		$r = Adb::push($name, $pathApk, $pathAndroid);
		var_dump($r);

		//5. push config ke file config.json
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