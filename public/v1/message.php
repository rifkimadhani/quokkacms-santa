<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/19/2019
 * Time: 12:20 PM
 */


const MESSAGE_STATUS_READ = 'READ';

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
		doGetList($stbId);
		break;
	case 'set_status_read':
		doSetStatusRead($stbId);
		break;
}

die();

function doGetList($stbId){

	require_once '../../model/ModelStb.php';
	require_once '../../model/ModelMessage.php';
	require_once '../../model/ModelSetting.php';

	$stb = ModelStb::get($stbId);

	$subscriberId = $stb['subscriber_id'];
	$roomId= $stb['room_id'];

	$list = ModelMessage::getBySubscriberAndRoom($subscriberId, $roomId);
	$images = ModelMessage::getAllImage($subscriberId, $roomId);

    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

    $host = ModelSetting::getHostVod();
	foreach($images as &$item){
		$item['url_video'] = str_replace('{HOST-VOD}', $host, $item['url_video']);
		$item['url_image'] = str_replace('{BASE-HOST}', $baseHost, $item['url_image']);
	}

	echo (  json_encode(['count'=>sizeof($list), 'list'=>$list, 'medias'=>$images ]) );
}

function doSetStatusRead($stbId){
	require_once '../../model/ModelStb.php';
	require_once '../../model/ModelMessage.php';

	$messageId = (empty($_GET['messageId']) ? 0 : $_GET['messageId']);

	//cari subscriberId dari stb
	$stb = ModelStb::get($stbId);
	$subscriberId = $stb['subscriber_id'];

	$result = ModelMessage::updateStatus($subscriberId, $messageId, MESSAGE_STATUS_READ);

	echo json_encode( [ 'result'=>$result] );
}


