<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 4/9/2019
 * Time: 12:07 PM
 *
 * Konsep kitchen, ada beberapa kitchen (restaurant) dalam 1 buah hotel
 *
 *
 */



require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
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
	case 'get_menu_group':
		doGetMenuGroup();
		break;

	case 'get_menu':
		doGetMenu();
		break;
	case 'get_kitchen':
		doGetKitchen();
		break;
}

die();

function doGetList(){
	require_once 'model/ModelKitchen.php';

	$list = ModelKitchen::getAll();

	echo json_encode([ 'count'=>sizeof($list), 'list'=>$list]);
}

function doGetMenuGroup(){
	require_once 'model/ModelKitchen.php';
	require_once 'model/ModelSetting.php';

//	$kitchenId = (empty($_GET['kitchenId']) ? 0 : $_GET['kitchenId']);
	$kitchenId = ModelSetting::getDefaultKitchenId();

	$list = ModelKitchen::getAllMenuGroup($kitchenId);

    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini
    foreach ($list as &$item){
        $item['url_thumb'] = str_replace('{BASE-HOST}', $baseHost, $item['url_thumb']);
    }

    echo json_encode([ 'count'=>sizeof($list), 'list'=>$list]);
}

function doGetMenu(){
	require_once 'model/ModelKitchen.php';
    require_once 'model/ModelSetting.php';

	$groupId = (empty($_GET['groupId']) ? 0 : $_GET['groupId']);

	$list = ModelKitchen::getAllMenu($groupId);


    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini
    foreach ($list as &$item){
        $item['url_image'] = str_replace('{BASE-HOST}', $baseHost, $item['url_image']);
    }


    echo json_encode([ 'count'=>sizeof($list), 'list'=>$list]);
}

function doGetKitchen(){
    require_once 'model/ModelKitchen.php';
    require_once 'model/ModelSetting.php';

//	$kitchenId = (empty($_GET['kitchenId']) ? 0 : $_GET['kitchenId']);
    $kitchenId = ModelSetting::getDefaultKitchenId();

    $kitchen = ModelKitchen::get($kitchenId);

    echo json_encode([ 'kitchen'=>$kitchen ]);
}