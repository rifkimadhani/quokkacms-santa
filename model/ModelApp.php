<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 5/30/2019
 * Time: 4:04 PM
 */

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';


class ModelApp{

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

		$basePath = realpath(__DIR__ . '/..');

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

		//1. connect if no device attached
		$r1 = Adb::connect($ip, $port);
        Log::writeLn(json_encode($r1));

		//sleep 2 second utk memberikan waktu pada adb utk start
		sleep(2);

        //2. ini di pakai utk quokka tv
        $r0 = Adb::root();
        Log::writeLn(json_encode($r0));

        $name = "{$ip}:{$port}"; //setiap adb akan mempergunakan device name, shg dalam 1 adb bisa multiple devices

        //3. buat folder
		$r2 = Adb::mkdir($name, $pathAndroid);

        //chmod
		$r3 = Adb::chmod($name, $pathAndroid);

		//4. push apk
		$r4 = Adb::push($name, $pathApk, $pathAndroid);

		//5. push config ke file config.json
		$configJson = "{\"host_api\": \"{$hostApi}\", \"time_zone\": \"{$timeZone}\", \"session_id\": \"{$sessionId}\"}";

		//convert " --> \"
		$configJson = str_replace('"', '\"', $configJson);

		$r5 = Adb::shell( $name,"echo '{$configJson}' > {$pathAndroid}/{$configFile}" );

		//6. install
		//adb shell pm install -t /data/mr/NewAcappela-release-1.0.apk
		$r6 = Adb::install($name,$pathAndroid . '/' . $apkFile);

		//7. enable device owner
		//adb shell dpm set-device-owner com.madeiraresearch.hoteliptv2/.MyDeviceAdminReceiver
//		$r = Adb::enableDeviceOwner($appId, $deviceAdminReceiver);
//		var_dump($r);

		//8. run
		//adb shell am start -n com.madeiraresearch.hoteliptv2/.MainActivity
		$r7 = Adb::runApp($name, $appId, $mainActivity);

		//disconnect from stb
		$r8 = Adb::disconnect($name);

        return ['root'=>$r0, 'connect'=>$r1, 'mkdir'=>$r2, 'chmod'=>$r3, 'push_apk'=>$r4, 'push_config'=>$r5, 'install'=>$r6, 'runapp'=>$r7, 'disconnect'=>$r8];
	}

	/**
	 * Install app on Philips TV (Non-Rooted)
	 * Uses run-as for config injection and direct APK installation
	 *
	 * @param $id - App ID from database
	 * @param $ip - TV IP address
	 * @param $stbId - STB/Device ID
	 * @param $simulate - If true, no actual ADB commands are executed
	 * @return array - Results of each step
	 */
	static public function installPhilips($id, $ip, $stbId, $simulate = false){
		require_once __DIR__ . '/../library/Adb.php';
		require_once __DIR__ . '/../library/Security.php';
		require_once __DIR__ . '/ModelStb.php';
		require_once __DIR__ . '/ModelSetting.php';

		// Enable simulation mode if requested
		Adb::setSimulateMode($simulate);

		$basePath = realpath(__DIR__ . '/..');

		//1. Get apk Path and app info
		$app = self::get($id);
		if (is_null($app)) {
			echo errCompose(ERR_FAIL_GET_LATEST_APP);
			exit();
		}
		$pathApk = str_replace('{BASE-PATH}', $basePath, $app['path']);
		$appId = $app['app_id'];
		$mainActivity = $app['main_activity'];

		//2. Create session for this STB
		$stbSession = substr($stbId . Security::random(24), 0, 24);
		$r = ModelStb::createStbSession($stbId, $stbSession);
		if ($r == 0) {
			echo errCompose(ERR_CREATE_STB_SESSION_FAIL);
			exit();
		}

		//3. Get settings for config.json
		$sessionId = $stbSession;
		$hostApi = base_url('');
		$timeZone = ModelSetting::getTimezone();
		$hostWeather = ModelSetting::getWeatherServer();

		//4. Configuration
		$port = 5555;
		$targetRelativePath = 'files/mr';  // Relative to /data/data/{package}/
		$configFilename = 'config.json';
		$tempConfigFilename = 'config_temp.json';
		$accessibilityService = 'com.madeiraresearch.library.cdb.AppWatcherAccessibility';

		// Bloatware packages to disable
		$bloatwarePackages = [
			'com.google.android.tvlauncher',
			'org.droidtv.welcome',
			'com.google.android.tungsten.setupwraith'
		];

		//5. Create temp config.json file on server
		$configData = [
			'host_api' => $hostApi,
			'time_zone' => $timeZone,
			'host_weather' => $hostWeather,
			'session_id' => $sessionId
		];
		$configJson = json_encode($configData);
		$tempConfigPath = sys_get_temp_dir() . '/' . $tempConfigFilename;
		file_put_contents($tempConfigPath, $configJson);

		//===========================================================================
		// Begin Philips TV Installation Process
		//===========================================================================

		$name = "{$ip}:{$port}";

		//Step 0: Ping to check if device is reachable (5 second timeout)
		$r0 = Adb::ping($ip, 5);
		Log::writeLn('Philips Install - Ping: ' . json_encode($r0));

		// If ping fails, return early with error
		if ($r0['retValue'] != 0) {
			return [
				'ping' => $r0,
				'success' => false,
				'error' => 'Device not reachable (ping failed)'
			];
		}

		//Step 1: Connect to device
		$r1 = Adb::connect($ip, $port);
		Log::writeLn('Philips Install - Connect: ' . json_encode($r1));
		sleep(2);

		//Step 2: Install APK directly (not via shell)
		$r2 = Adb::installDirect($name, $pathApk);
		Log::writeLn('Philips Install - Install APK: ' . json_encode($r2));

		//Step 3: Run app first (required for run-as to work)
		$r3 = Adb::runApp($name, $appId, $mainActivity);
		Log::writeLn('Philips Install - Run App: ' . json_encode($r3));
		sleep(2);

		//Step 4: Push config to sdcard (temp location)
		$r4 = Adb::pushToSdcard($name, $tempConfigPath, $tempConfigFilename);
		Log::writeLn('Philips Install - Push Config to SDCard: ' . json_encode($r4));

		//Step 5: Create directory using run-as
		$r5 = Adb::runAsCommand($name, $appId, "mkdir -p {$targetRelativePath}");
		Log::writeLn('Philips Install - Mkdir: ' . json_encode($r5));

		//Step 6: Copy config from sdcard to app's internal storage
		$r6 = Adb::runAsCommand($name, $appId, "cp /sdcard/{$tempConfigFilename} {$targetRelativePath}/{$configFilename}");
		Log::writeLn('Philips Install - Copy Config: ' . json_encode($r6));

		//Step 7: Cleanup temp file from sdcard
		$r7 = Adb::removeFile($name, "/sdcard/{$tempConfigFilename}");
		Log::writeLn('Philips Install - Cleanup: ' . json_encode($r7));

		//Step 8: Disable bloatware packages
		$rBloat = [];
		foreach ($bloatwarePackages as $pkg) {
			$rBloat[$pkg] = Adb::disablePackage($name, $pkg);
		}
		Log::writeLn('Philips Install - Disable Bloatware: ' . json_encode($rBloat));

		//Step 9: Enable accessibility service
		$r8 = Adb::enableAccessibility($name, $appId, $accessibilityService);
		Log::writeLn('Philips Install - Enable Accessibility: ' . json_encode($r8));

		//Step 10: Set as default home activity
		$r9 = Adb::setHomeActivity($name, $appId, $mainActivity);
		Log::writeLn('Philips Install - Set Home Activity: ' . json_encode($r9));

		//Step 11: Restart app
		$r10 = Adb::forceStop($name, $appId);
		$r11 = Adb::runApp($name, $appId, $mainActivity);
		Log::writeLn('Philips Install - Restart App: ' . json_encode($r11));

		//Step 12: Disconnect
		$r12 = Adb::disconnect($name);
		Log::writeLn('Philips Install - Disconnect: ' . json_encode($r12));

		// Cleanup server temp file
		@unlink($tempConfigPath);

		return [
			'connect' => $r1,
			'install' => $r2,
			'runapp_first' => $r3,
			'push_config' => $r4,
			'mkdir' => $r5,
			'copy_config' => $r6,
			'cleanup_temp' => $r7,
			'disable_bloatware' => ['cmd' => 'pm disable-user (multiple)', 'retValue' => 0, 'retString' => 'Disabled ' . count($bloatwarePackages) . ' packages', 'output' => []],
			'accessibility' => $r8,
			'set_home' => $r9,
			'runapp' => $r11,
			'disconnect' => $r12
		];
	}

}