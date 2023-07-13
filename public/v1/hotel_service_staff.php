<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 7/13/2023
 * Time: 1:02 PM
 */


/**
 * staff di pakai utk accept task yg di issue dari stb
 * 1. hotel service
 * 2. room service
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
    case 'list_history':
        doGetListHistory($admin);
        break;

    case 'list_active':
        doGetListActive($admin);
        break;

    case 'change_status':
        doChangeStatus($admin);
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;

}

exit();

////////////////////////////////////////////////////////////////////////////

function doGetListActive($admin){
    require_once '../../model/ModelHotelService.php';

    $data = ModelHotelService::getActive();

    echo json_encode([ 'length'=>sizeof($data), 'data'=>$data ]);
}

function doGetListHistory($admin){
    require_once '../../model/ModelHotelService.php';

    $data = ModelHotelService::getHistory();

    echo json_encode([ 'length'=>sizeof($data), 'data'=>$data ]);
}

/**
 * rubah status, new status ACK / FINISH
 * status tdk di verifikasi
 *
 * @param $admin
 */
function doChangeStatus($admin){
    require_once '../../model/ModelHotelService.php';

    $taskId = (empty($_GET['task_id']) ? '' : $_GET['task_id']);
    $status = (empty($_GET['status']) ? '' : $_GET['status']);

    $r = ModelHotelService::updateStatus($taskId, $status);

    echo json_encode(['result'=>$r]);
}
