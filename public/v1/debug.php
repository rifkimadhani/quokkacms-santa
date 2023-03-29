<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 6/13/2019
 * Time: 3:30 PM
 */

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../model/ModelStbCredential.php';

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'add_log':
		doAddLog($stbId);
		break;
}

die();


function doAddLog($stbId) {
	require_once '../../model/ModelStb.php';
	require_once '../../model/ModelDebug.php';

	$ip = $_SERVER['REMOTE_ADDR'];

	$appId = (empty($_GET['app_id']) ? '' : $_GET['app_id']);
	$versionCode = (empty($_GET['version_code']) ? '' : $_GET['version_code']);
	$versioName = (empty($_GET['version_name']) ? '' : $_GET['version_name']);
	$type = (empty($_GET['type']) ? '' : $_GET['type']);
	$tag = (empty($_GET['tag']) ? '' : $_GET['tag']);
	$message = (empty($_GET['message']) ? '' : $_GET['message']);

	$r = ModelDebug::create($appId, $stbId, $versionCode, $ip, $type, $tag, $message);

	echo json_encode([ 'result'=>$r ]);
}