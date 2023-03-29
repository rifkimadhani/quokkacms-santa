<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 5/16/2019
 * Time: 12:46 PM
 */
require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../model/ModelStbCredential.php';


Log::writeLn('==========================================================================================================');
Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get':
		doGet();
		break;
}

die();


function doGet(){

	$url = '{HOST}/assets/content/bgmusic2.mp3';
//	$url = 'https://homeconnectapp.com/ott2/assets/bgmusic/piano1.mp3';
//	$url = 'https://homeconnectapp.com/ott2/assets/bgmusic/piano2.mp3';

	require_once '../../model/ModelSetting.php';

	$urlHost = ModelSetting::getHostApi();
	$url = str_replace('{HOST}', $urlHost, $url);

	echo json_encode( [ 'url_media'=>$url] );
}