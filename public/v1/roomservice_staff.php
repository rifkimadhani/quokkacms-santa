<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 7/13/2023
 * Time: 2:31 PM
 */

/**
 * di pakai utk accept task yg di issue dari stb
 * status = NEW >> PROCESS >> WAIT_FOR_PICKUP >> ENROUTE >> DELIVERED >> FINISH / CANCEL
 *
 * status ini di rubah oleh 2 role yg berbeda room_service & kitchen
 *
 * status dari NEW --> WAIT_FOR_PICKUP di rubah oleh kitchen
 * status dari WAIT_FOR_PICKUP --> FINISH di rubah oleh room_service
 *
 */

require_once __DIR__ . '/../../library/http_errorcodes.php';
require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../model/ModelAdmin.php';
require_once __DIR__ . '/../../model/ModelRole.php';

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

//check role
$admin = ModelAdmin::get($admin['admin_id']);
$roles = json_decode($admin['json'])->roles;

$isRoleRoomService = (in_array(role_room_service, $roles)) ? true : false;

//role room service tdk ada
if ($isRoleRoomService==false){
    echo errCompose(ERR_ROLE_ROOMSERVICE_NOTFOUND);
    return;
}

////////////////////////////////////////////////////////////////////////

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
    case 'list_history':
        doGetListHistory($admin);
        break;

    case 'list_active':
        doGetListActive($admin);
        break;

    case 'get_detail':
        doGetDetail($admin);
        break;

    case 'change_status':
        doChangeStatus($admin);
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;

}

exit();

////////////////////////////////////////////////////////////////////////

/**
 * return json semua roomservice yg active saja
 *
 * apabila status NEW & PROCESS maka food_ready = false
 *
 * @param $admin
 */
function doGetListActive($admin){
    require_once '../../model/ModelAdmin.php';
    require_once '../../model/ModelRoomservice.php';

    $data = ModelRoomservice::getActive();
    foreach ($data as &$item){
        $status = $item['status'];

        if ($status==status_new || $status==status_process){
            $isFoodReady = 0;
        }else{
            //food ready apabila status bukan new / process
            $isFoodReady = 1;
        }

        $item['food_ready'] = $isFoodReady;
    }

    echo json_encode([ 'length'=>sizeof($data), 'data'=>$data ]);
}

function doGetListHistory($admin){
    require_once '../../model/ModelRoomservice.php';

    $data = ModelRoomservice::getHistory();

    echo json_encode([ 'length'=>sizeof($data), 'data'=>$data ]);
}

function doGetDetail($admin){
    require_once '../../model/ModelRoomservice.php';

    $orderCode = (empty($_GET['order_code']) ? '' : $_GET['order_code']);

    $order = ModelRoomservice::getOne($orderCode);
    if ($order==null){
        echo errCompose(ERR_INVALID_ORDERCODE);
        return;
    }
    if ($order instanceof PDOException){
        echo errCompose($order);
        return;
    }

    $data = ModelRoomservice::getDetail($orderCode);
    $notes = $order['notes'];

    echo json_encode([ 'length'=>sizeof($data), 'data'=>$data, 'notes'=> $notes]);
}

/**
 * rubah status, new status ACK / FINISH
 * status tdk di verifikasi
 *
 * @param $admin
 */
function doChangeStatus($admin){
    require_once '../../model/ModelRoomservice.php';

    $orderCode = (empty($_GET['order_code']) ? '' : $_GET['order_code']);

    $task = ModelRoomservice::getOneActive($orderCode);

    if (is_null($task)){
        echo errCompose(ERR_ROOMSERVICE_TASK_ALREADY_COMPLETE);
        exit();
    }

    //check status, hanya bisa di rubah apabila statuc bukan NEW / PROCESS
    $status = $task['status'];
    if ($status==status_new || $status==status_process){
        echo errCompose(ERR_ROOMSERVICE_FOOD_NOT_READY);
        return;
    }

    $r = ModelRoomservice::updateStatus($orderCode, getNewStatus($status), $admin['admin_id']);

    echo json_encode(['result'=>$r]);
}

function getNewStatus($status){
    switch ($status){
        case status_new:
            return status_process;

        case status_process:
            return status_wait_pick;

        case status_wait_pick:
            return status_enroute;

        case status_enroute:
            return status_delivered;

        case status_delivered:
            return status_finish;

        default:
            return $status;
    }
}
