<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 14/10/2022
 * Time: 14:25
 */

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/../v1/model/ModelSetting.php';
require_once __DIR__ . '/../v1/model/ModelLivetv.php';

header('Content-type: application/json; charset=utf-8');

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
    case 'get_list':
        doGetList();
        break;
    case 'get_category_list':
        doGetCategoryList();
        break;

    default:
        http_response_code(HTTP_INTERNAL_ERROR);
        break;
}

exit();

/**
 * versi mobile tdk memerlukan sessionid utk list tv
 */
function doGetList(){

    $packageId = ModelSetting::getDefaultPackageId();

    $list = ModelLivetv::getByPackage($packageId);

    if (empty($list)){
        echo errCompose(ERR_PACKAGE_NOT_DEFINED);
        return;
    }

    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini
    $hostTv = ModelSetting::getHostTv();

    foreach ($list as &$item){
        $item['url_stream1'] = str_replace('{HOST-TV}', $hostTv, $item['url_stream1']);
        $item['url_station_logo'] = str_replace('{BASE-HOST}', $baseHost, $item['url_station_logo']);
    }

    echo json_encode( [ 'count'=>sizeof($list), 'list'=>$list] );
}

function doGetCategoryList(){
    $list = ModelLivetv::getCategory();

    if ($list instanceof PDOException){
        echo errCompose($list);
        return;
    }

    echo json_encode( [ 'count'=>sizeof($list), 'list'=>$list] );
}