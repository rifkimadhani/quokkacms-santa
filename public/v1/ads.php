<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 5/16/2019
 * Time: 11:26 AM
 */

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/model/ModelStbCredential.php';


Log::writeLn('==========================================================================================================');
Log::writeRequestUri();

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
	require_once 'model/ModelAds.php';


	$systemType = (empty($_GET['systemType']) ? '' : $_GET['systemType']);

	$list = ModelAds::getBySystemType($systemType);

	require_once 'model/ModelSetting.php';

	$urlHost = ModelSetting::getHostApi();
    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

	foreach($list as &$item){
		$item['url_image'] = str_replace('{HOST}', $urlHost, $item['url_image']);
        $item['url_image'] = str_replace('{BASE-HOST}', $baseHost, $item['url_image']);
	}

	echo json_encode( [ 'count'=>count($list), 'list'=>$list] );
}