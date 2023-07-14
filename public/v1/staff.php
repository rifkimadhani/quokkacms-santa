<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 7/14/2023
 * Time: 9:03 AM
 */

require_once __DIR__ . '/../../library/http_errorcodes.php';
require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../model/ModelAdmin.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

Log::writeLn('==========================================================================================================');
Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

//check admin_sessionid
$sessionId = (empty($_GET['admin_sessionid']) ? '' : $_GET['admin_sessionid']);
//get admin dari sessionId
$admin = ModelAdmin::getAdminFromSessionId($sessionId);
if (empty($admin)){
    echo errCompose(ERR_UNAUTHORIZED_ENTRY);
    exit();
}
if ($admin instanceof PDOException){
    echo errCompose($admin);
    exit();
}

////////////////////////////////////////////////////////////////////////////

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
    case 'update_info':
        doUpdateInfo($admin, $sessionId);
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;

}

exit();

////////////////////////////////////////////////////////////////////////////

/**
 * @param $admin
 * @param $sessionId
 */
function doUpdateInfo($admin, $sessionId){
    require_once '../../model/ModelSetting.php';
    require_once '../../model/ModelAdminSession.php';

    $deviceType = (empty($_GET['device_type']) ? '' : $_GET['device_type']);
    $fcmToken = (empty($_GET['fcm_token']) ? '' : $_GET['fcm_token']);
    $versionCode = (empty($_GET['version_code']) ? '' : $_GET['version_code']);
    $versionName = (empty($_GET['version_name']) ? '' : $_GET['version_name']);

    $siteId = ModelSetting::getSiteId();

    //update expire date
    $now = date('Y-m-d H:i:s'); // Current date and time
    $expDate = date('Y-m-d H:i:s', strtotime($now. '+3 days'));

    $deviceType = strtoupper($deviceType);

    ModelAdminSession::update($sessionId, $deviceType, $expDate, $versionCode, $versionName, $fcmToken);

    echo json_encode(['site_id'=>$siteId, 'expire_date'=>$expDate]);
}

