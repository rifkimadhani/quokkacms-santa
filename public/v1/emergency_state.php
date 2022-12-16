<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 7/12/2019
 * Time: 10:57 AM
 */

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelStbCredential.php';

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get_state':
		doGetState();
		break;
}

die();


function doGetState(){
	require_once 'model/ModelSetting.php';
	require_once 'model/ModelEmegencyState.php';

	//emergency state
	$emergencyState = ModelSetting::getEmergencyState();

	$state=null;
	if (isset($emergencyState['value_string'])) $state = $emergencyState['value_string'];

	//apabila state exist maka ambil url utk download image
	$emergency = null;
	if (empty($state)==false){
		$host = ModelSetting::getHostApi();
		$emergency = ModelEmegencyState::getByCode($state);
		$emergency['url_image'] = str_replace('{HOST-IMAGE}', $host, $emergency['url_image']);
	}

	echo json_encode([ 'emergency_state'=>$emergency]);
}