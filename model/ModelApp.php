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

	const SQL_GET = 'SELECT * FROM tapp WHERE id=?';
	const SQL_GET_LATEST = 'SELECT * FROM tapp WHERE app_id=? AND version_code>? ORDER BY version_code DESC LIMIT 1';

	static public function get($id){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelApp::SQL_GET);
			$stmt->execute( [$id] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

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

	static public function install($id, $ip, $stbId){
		require_once __DIR__ . '/../library/Adb.php';
		require_once __DIR__ . '/../library/Security.php';
		require_once __DIR__ . '/ModelStb.php';
		require_once __DIR__ . '/ModelSetting.php';

		$basePath = __DIR__ . '/..';

		//1. Get apk Path
		$app = self::get($id);
		if (is_null($app)) {
			echo errCompose(ERR_FAIL_GET_LATEST_APP);
			exit();
		}
        $pathApk = str_replace('{BASE-PATH}', $basePath, $app['path']);
		$appId = $app['app_id'];

		//2. get apk filename
		$apkFile = basename($pathApk);

		//3. get MainActiviry
		$mainActivity = $app['main_activity'];

		//4. buat sessionId utk stbId yg dimaksud
		$stbSession = substr($stbId . Security::random(24),0,24);

		$r = ModelStb::createStbSession($stbId, $stbSession);
		if ($r==0){
			echo errCompose(ERR_CREATE_STB_SESSION_FAIL);
			exit();
		}

		$sessionId = $stbSession;
		$hostApi = base_url('');//ModelSetting::getHostApi();//'https://homeconnectapp.com/ott2';
		$timeZone = ModelSetting::getTimezone();//'Asia/Jakarta';

		$pathAndroid = '/data/mr';
		$configFile = 'config.json';
		$port = 5555;

		//Begin install process
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //ini di pakai utk quokka tv
        $r0 = Adb::root();

		//2. connect if no device attached
		$name = "{$ip}:{$port}"; //setiap adb akan mempergunakan device name, shg dalam 1 adb bisa multiple devices
		$r1 = Adb::connect($ip, $port);
//		var_dump($r);

		//3. buat folder
		$r2 = Adb::mkdir($name, $pathAndroid);
//		var_dump($r);

		$r3 = Adb::chmod($name, $pathAndroid);
//		var_dump($r);

		//4. push apk ke path
		$r4 = Adb::push($name, $pathApk, $pathAndroid);
//		var_dump($r);

		//5. push config ke file config.json
		$configJson = "{\"host_api\": \"{$hostApi}\", \"time_zone\": \"{$timeZone}\", \"session_id\": \"{$sessionId}\"}";

		//convert " --> \"
		$configJson = str_replace('"', '\"', $configJson);

		$r5 = Adb::shell( $name,"echo '{$configJson}' > {$pathAndroid}/{$configFile}" );
//		var_dump($r);

		//6. install
		//adb shell pm install -t /data/mr/NewAcappela-release-1.0.apk
		$r6 = Adb::install($name,$pathAndroid . '/' . $apkFile);
//		var_dump($r);

		//7. enable device owner
		//adb shell dpm set-device-owner com.madeiraresearch.hoteliptv2/.MyDeviceAdminReceiver
//		$r = Adb::enableDeviceOwner($appId, $deviceAdminReceiver);
//		var_dump($r);

		//8. run
		//adb shell am start -n com.madeiraresearch.hoteliptv2/.MainActivity
		$r7 = Adb::runApp($name, $appId, $mainActivity);
//		var_dump($r);

		//disconnect from stb
		$r8 = Adb::disconnect($name);
//		var_dump($r);

        return ['root'=>$r0, 'connect'=>$r1, 'mkdir'=>$r2, 'chmod'=>$r3, 'push_apk'=>$r4, 'push_config'=>$r5, 'install'=>$r6, 'runapp'=>$r7, 'disconnect'=>$r8];
	}

}