<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 6/10/2019
 * Time: 10:25 AM
 */

class Adb
{
	// Cache for ADB path (detected once, reused)
	private static $adbPath = null;

	// Simulation mode - when true, no actual ADB commands are executed
	private static $simulateMode = false;

	/**
	 * Enable/disable simulation mode
	 * When enabled, ADB commands return mock success responses
	 */
	static public function setSimulateMode($enabled){
		self::$simulateMode = $enabled;
	}

	/**
	 * Check if simulation mode is enabled
	 */
	static public function isSimulateMode(){
		return self::$simulateMode;
	}

	/**
	 * Dynamically detect ADB path
	 * Tries 'which adb' first, then common installation paths
	 */
	static private function getAdbPath(){
		// Return cached path if already detected
		if (self::$adbPath !== null) {
			return self::$adbPath;
		}

		// Try 'which adb' first
		$whichAdb = trim(shell_exec('which adb 2>/dev/null') ?? '');
		if (!empty($whichAdb) && file_exists($whichAdb)) {
			self::$adbPath = $whichAdb;
			return self::$adbPath;
		}

		// Common ADB installation paths
		$commonPaths = [
			'/home/quokka/platform-tools/adb',  // Current server
			'/usr/bin/adb',
			'/usr/local/bin/adb',
			'/opt/android-sdk/platform-tools/adb',
			'/home/' . get_current_user() . '/Android/Sdk/platform-tools/adb',
			'/home/' . get_current_user() . '/platform-tools/adb',
		];

		foreach ($commonPaths as $path) {
			if (file_exists($path)) {
				self::$adbPath = $path;
				return self::$adbPath;
			}
		}

		// Fallback to 'adb' (hope it's in PATH)
		self::$adbPath = 'adb';
		return self::$adbPath;
	}

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

	/**
	 * Ping IP to check if device is reachable
	 * Use this before adb connect to fail fast on unreachable IPs
	 * @param $ip - IP address to ping
	 * @param $timeout - Timeout in seconds (default 5)
	 * @return array - retValue 0 = reachable, non-zero = unreachable
	 */
	static public function ping($ip, $timeout = 5){
		// -c 1 = single packet, -W = timeout in seconds
		$cmd = "ping -c 1 -W {$timeout} {$ip}";

		// Ping bypasses simulation mode - we always want real network check
		$origSimulate = self::$simulateMode;
		self::$simulateMode = false;
		$result = self::execute($cmd);
		self::$simulateMode = $origSimulate;

		// In simulation mode, return success
		if ($origSimulate) {
			return [
				'cmd' => $cmd . ' [SIMULATED]',
				'retValue' => 0,
				'retString' => 'Ping success (simulated)',
				'output' => ['Simulation mode - ping bypassed']
			];
		}

		return $result;
	}

	static public function connect($ip, $port=5555){
		$cmd = "adb connect {$ip}:{$port}";
		return self::execute($cmd);
	}

	static public function root(){
		$cmd = "adb root";
		return self::execute($cmd);
	}

	//req root
	static public function mkdir($name, $path){
		$cmd = "adb -s {$name} shell \"mkdir {$path}\"";
		return self::execute($cmd);
	}

	//req root
	static public function chmod($name, $path, $permission='777'){
		$cmd = "adb -s {$name} shell \"chmod {$permission} {$path}\"";
		return self::execute($cmd);
	}

	static public function push($name, $localFile, $androidPath){
		$cmd = "adb -s {$name} push {$localFile} {$androidPath}";
		return self::execute($cmd);
	}

	//adb shell "echo {"host_api": "{HOST_API}", "time_zone": "{TIME_ZONE}", "session_id": "{SESSION_ID}"} > /data/mr/config.json"
	static public function shell($name, $shellCmd){
		$cmd = "adb -s {$name} shell \"{$shellCmd}\"";
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
		// Handle different activity name formats:
		// - ".MainActivity" - already shorthand, use as-is
		// - "MainActivity" - needs dot prefix for shorthand
		// - "com.pkg.name.MainActivity" - if starts with appId, convert to shorthand
		
		if (!empty($mainActivity)) {
			if (strpos($mainActivity, $appId . '.') === 0) {
				// Full path like "com.pkg.MainActivity" - convert to ".MainActivity"
				$mainActivity = '.' . substr($mainActivity, strlen($appId) + 1);
			} elseif ($mainActivity[0] !== '.' && strpos($mainActivity, '.') === false) {
				// Simple name like "MainActivity" - prepend dot
				$mainActivity = '.' . $mainActivity;
			}
		}
		
		$cmd = "adb -s {$name} shell am start -n {$appId}/{$mainActivity}";
		return self::execute($cmd);
	}

	//===================================================================================
	// Philips TV Methods (Non-Rooted Installation)
	//===================================================================================

	/**
	 * Install APK directly from host machine (not via shell)
	 * Used for non-rooted devices where we can't push to /data/
	 * adb install -r /path/to/app.apk
	 */
	static public function installDirect($name, $apkPath){
		$cmd = "adb -s {$name} install -r {$apkPath}";
		return self::execute($cmd);
	}

	/**
	 * Push file to sdcard (accessible without root)
	 * adb push /local/file /sdcard/filename
	 */
	static public function pushToSdcard($name, $localFile, $filename){
		$cmd = "adb -s {$name} push {$localFile} /sdcard/{$filename}";
		return self::execute($cmd);
	}

	/**
	 * Execute command using run-as (requires debuggable APK)
	 * This allows access to app's private data directory without root
	 * adb shell "run-as com.package.name command"
	 */
	static public function runAsCommand($name, $package, $runCmd){
		$cmd = "adb -s {$name} shell \"run-as {$package} {$runCmd}\"";
		return self::execute($cmd);
	}

	/**
	 * Remove a file from device
	 * adb shell rm /path/to/file
	 */
	static public function removeFile($name, $path){
		$cmd = "adb -s {$name} shell rm {$path}";
		return self::execute($cmd);
	}

	/**
	 * Disable a system package for current user
	 * adb shell pm disable-user --user 0 com.package.name
	 */
	static public function disablePackage($name, $package){
		$cmd = "adb -s {$name} shell pm disable-user --user 0 {$package}";
		return self::execute($cmd);
	}

	/**
	 * Enable accessibility service
	 * adb shell settings put secure enabled_accessibility_services com.pkg/com.pkg.Service
	 * adb shell settings put secure accessibility_enabled 1
	 */
	static public function enableAccessibility($name, $package, $service){
		$fullService = "{$package}/{$service}";
		$cmd1 = "adb -s {$name} shell settings put secure enabled_accessibility_services {$fullService}";
		$r1 = self::execute($cmd1);

		$cmd2 = "adb -s {$name} shell settings put secure accessibility_enabled 1";
		$r2 = self::execute($cmd2);

		return ['cmd' => $cmd1 . ' && ' . $cmd2, 'retValue' => $r2['retValue'], 'retString' => $r1['retString'] . ' ' . $r2['retString'], 'output' => array_merge($r1['output'], $r2['output'])];
	}

	/**
	 * Set default home/launcher activity
	 * adb shell cmd package set-home-activity com.package/.MainActivity
	 */
	static public function setHomeActivity($name, $package, $activity){
		$cmd = "adb -s {$name} shell cmd package set-home-activity {$package}/{$activity}";
		return self::execute($cmd);
	}

	/**
	 * Force stop an application
	 * adb shell am force-stop com.package.name
	 */
	static public function forceStop($name, $package){
		$cmd = "adb -s {$name} shell am force-stop {$package}";
		return self::execute($cmd);
	}



	static private function execute($cmd){
		// echo 'execute ' . $cmd;

		// Simulation mode - return mock success without executing
		if (self::$simulateMode) {
			usleep(500000); // 0.5 second delay to simulate real execution
			return [
				'cmd' => $cmd . ' [SIMULATED]',
				'retValue' => 0,
				'retString' => 'Success (simulated)',
				'output' => ['Simulation mode - no actual command executed']
			];
		}

		// Replace 'adb' with dynamically detected full path
		$adbPath = self::getAdbPath();
		$cmd = preg_replace('/^adb\b/', $adbPath, $cmd);
        $cmd = $cmd . ' 2>&1';
		$ret = exec($cmd, $output, $retValue);
		return ['cmd'=>$cmd, 'retValue'=>$retValue, 'retString'=>$ret, 'output'=>$output];
	}
}