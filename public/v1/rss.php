<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 7/24/2019
 * Time: 9:17 AM
 */

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../model/ModelStbCredential.php';

Log::writeLn('==========================================================================================================');
Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get_category':
		doGetCategory();
		break;
	case 'get_source':
		doGetSource();
		break;
}

die();

function doGetCategory(){
	require_once '../../model/ModelRss.php';

	$categories = ModelRss::getAllCategory();

	echo json_encode( ['count'=>count($categories), 'list'=>$categories] );
}


function doGetSource(){
	require_once '../../model/ModelRss.php';

	$categoryId = (empty($_GET['category_id']) ? 0 : $_GET['category_id']);

	$list = ModelRss::getSource($categoryId);

	echo json_encode( ['count'=>count($list), 'list'=>$list] );
}