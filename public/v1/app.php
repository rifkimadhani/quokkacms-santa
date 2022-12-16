<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 26/04/2022
 * Time: 10:04
 */

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../library/http_errorcodes.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelStbCredential.php';
require_once __DIR__ . '/model/ModelApp.php';
require_once __DIR__ . '/model/ModelSetting.php';

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
    case 'get_apk': //list order yg blm checkout
        doGetApk();
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

/**
 * get latest apk
 */
function doGetApk(){

    $appId = (empty($_GET['app_id']) ? '' : $_GET['app_id']);

    //walaupun versioncode = 1, tetapi yg di ambil adalah version code yg tertinggi
    $app = ModelApp::getLatest($appId, 0);

    if (is_null($app)){
        http_response_code(HTTP_BAD_REQUEST);
        echo errCompose(ERR_APPLICATION_ID_NOTFOUND);
        exit();
    }

    if ($app instanceof PDOException){
        http_response_code(HTTP_BAD_REQUEST);
        echo errCompose($app);
        exit();
    }

    //remove field yg tdk perlu di tampilkan
    unset($app['path']);
    unset($app['create_date']);
    unset($app['update_date']);
    unset($app['main_activity']);

    //fix url download
    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini
    $app['urlDownload'] = str_replace('{BASE_HOST}', $baseHost, $app['urlDownload']);

    echo json_encode($app);
}