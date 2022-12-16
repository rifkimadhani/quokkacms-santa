<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 6/10/2019
 * Time: 10:25 AM
 */

class Adb
{
	/**list all connected device
	 *
	 * @return array
	 */
	static public function devices(){
		$cmd = 'adb devices';
		return self::execute($cmd);
	}

	static public function disconnect($name){
		$cmd = "adb disconnect {$name}";
		return self::execute($cmd);
	}

	static public function connect($ip, $port=5555){
		$cmd = "adb connect {$ip}:{$port}";
		return self::execute($cmd);
	}

	//req root
	static public function mkdir($name, $path){
		$cmd = "adb -s {$name} shell \"su -c 'mkdir {$path}'\"";
		return self::execute($cmd);
	}

	//req root
	static public function chmod($name, $path, $permission='777'){
		$cmd = "adb -s {$name} shell \"su -c 'chmod {$permission} {$path}'\"";
		return self::execute($cmd);
	}

	static public function push($name, $localFile, $androidPath){
		$cmd = "adb -s {$name} push {$localFile} {$androidPath}";
		return self::execute($cmd);
	}

	//adb shell "echo {"host_api": "{HOST_API}", "time_zone": "{TIME_ZONE}", "session_id": "{SESSION_ID}"} > /data/mr/config.json"
	static public function shell($name, $shellCmd){
		$cmd = "adb -s {$name} shell \"{$shellCmd}\"";
//		$cmd = 'adb shell "echo \'\"lalalala"\"\'"';
		return self::execute($cmd);
	}

	//adb shell pm install -t /data/mr/NewAcappela-release-1.0.apk
	static public function install($name, $apkFile){
		$cmd = "adb -s {$name} shell pm install -t -r {$apkFile}";
		return self::execute($cmd);
	}

	//adb shell dpm set-device-owner com.madeiraresearch.hoteliptv2/.MyDeviceAdminReceiver
	static public function enableDeviceOwner($appId, $deviceAdminReceiver){
		$cmd = "adb shell dpm set-device-owner {$appId}/{$deviceAdminReceiver}";
		return self::execute($cmd);
	}

	//adb shell am start -n com.madeiraresearch.hoteliptv2/.MainActivity
	static public function runApp($name, $appId, $mainActivity){
		$cmd = "adb -s {$name} shell am start -n {$appId}/{$mainActivity}";
		return self::execute($cmd);
	}






	static private function execute($cmd){
//		echo 'execute ' . $cmd;
		$ret = exec($cmd, $output, $retValue);
		return ['cmd'=>$cmd, 'retValue'=>$retValue, 'retString'=>$ret, 'output'=>$output];
	}
}