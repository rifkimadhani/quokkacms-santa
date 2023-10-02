<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/19/2019
 * Time: 10:13 AM
 *
 * versi mobile tdk ada sessionId
 */

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../model/ModelStbCredential.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');


//$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get_list':
		doGetList();
		break;
    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

function doGetList(){

	require_once '../../model/ModelFacility.php';
	require_once '../../model/ModelSetting.php';

	$facility = ModelFacility::getAll();
	$images = ModelFacility::getAllImage();

	//replace semua yg ada {HOST} --> $host
	$host = ModelSetting::getHostApi();
    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

    foreach ($images as &$image){
		$str = $image['url_image'];
		$image['url_image'] = str_replace('{HOST}', $host, $str);

        $image['url_image'] = str_replace('{BASE-HOST}', $baseHost, $image['url_image']);
	}

	echo (  json_encode(['count'=>sizeof($facility), 'list'=>$facility, 'images'=>$images]) );
}