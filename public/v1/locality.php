<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 4/9/2019
 * Time: 12:56 PM
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
	case 'get_list':
		doGetList();
		break;
}

die();

function doGetList(){
	require_once '../../model/ModelLocality.php';
	require_once '../../model/ModelSetting.php';

	$list = ModelLocality::getAll();
	$medias = ModelLocality::getAllImage();

	$host = ModelSetting::getHostApi();
	$hostVod = ModelSetting::getHostVod();
    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

    foreach ($medias as &$media){
		$media['url_image'] = str_replace('{HOST}', $host, $media['url_image']);
		$media['url_video'] = str_replace('{HOST}', $host, $media['url_video']);
		$media['url_video'] = str_replace('{HOST-VOD}', $hostVod, $media['url_video']);

        $media['url_image'] = str_replace('{BASE-HOST}', $baseHost, $media['url_image']);
        $media['url_video'] = str_replace('{BASE-HOST}', $baseHost, $media['url_video']);
	}

	echo (  json_encode(['count'=>sizeof($list), 'list'=>$list, 'medias'=>$medias]) );
}