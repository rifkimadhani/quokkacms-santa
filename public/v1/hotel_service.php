<?php
/**
 * Created by PhpStorm.
 * User: echri
 * Date: 18/08/2021
 * Time: 11:40
 */

define('SERVICE_CLEAN_ROOM', 'CLEAN-ROOM');
define('SERVICE_REQUEST_ITEM', 'REQUEST-ITEM');
define('SERVICE_CALL_TAXI', 'CALL-TAXI');

define('STATUS_NEW', 'NEW');
define('STATUS_ACK', 'ACK');
define('STATUS_CANCEL', 'CANCEL');
define('STATUS_FINISH', 'FINISH');

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelStbCredential.php';
require_once __DIR__ . '/model/ModelHotelService.php';

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
    case 'cancel':
        doCancel($stbId);
        break;

    case 'get':
        doGet($stbId);
        break;

    case 'get_list':
        doGetList($stbId);
        break;

    case SERVICE_CLEAN_ROOM:
        doServiceCleanRoom($action, $stbId);
        break;
    case SERVICE_REQUEST_ITEM:
        doServiceRequestItem($action, $stbId);
        break;
    case SERVICE_CALL_TAXI:
        doServiceCallTaxi($action, $stbId);
        break;
}

exit();

function doGetList($stbId){
    //ambil room
    $room = ModelStb::get($stbId);
    if (is_null($room)){
        echo errCompose(ERR_STB_HAS_NO_ROOM);
        exit();
    }
    $roomId = $room['room_id'];

    //check subscriberId
    $subscriberId = $room['subscriber_id'];
    if (is_null($subscriberId) or $subscriberId==0){
        //guest sdh checkout
        echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
        exit();
    }

    $r = ModelHotelService::getAll($roomId, $subscriberId);
    exitOnPdoException($r);

    echo json_encode(['count'=>sizeof($r), 'list'=>$r]);
}

function doGet($stbId){
    //ambil room
    $room = ModelStb::get($stbId);
    if (is_null($room)){
        echo errCompose(ERR_STB_HAS_NO_ROOM);
        exit();
    }
    $roomId = $room['room_id'];

    //check subscriberId
    $subscriberId = $room['subscriber_id'];
    if (is_null($subscriberId) or $subscriberId==0){
        //guest sdh checkout
        echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
        exit();
    }

    $taskId = (empty($_GET['task_id']) ? 0 : $_GET['task_id']);

    $r = ModelHotelService::get($roomId, $subscriberId, $taskId);
    exitOnPdoException($r);

    echo json_encode(['item'=>$r]);
}

function doServiceCleanRoom($action, $stbId){

    //ambil room
    $room = ModelStb::get($stbId);
    if (is_null($room)){
        echo errCompose(ERR_STB_HAS_NO_ROOM);
        exit();
    }
    $roomId = $room['room_id'];

    //check subscriberId
    $subscriberId = $room['subscriber_id'];
    if (is_null($subscriberId) or $subscriberId==0){
        //guest sdh checkout
        echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
        exit();
    }

    $r = ModelHotelService::create($roomId, $subscriberId, $action, STATUS_NEW, null);
    exitOnPdoException($r);

    echo json_encode(['success'=>$r]);
}

function doServiceRequestItem($action, $stbId){

    //ambil room
    $room = ModelStb::get($stbId);
    if (is_null($room)){
        echo errCompose(ERR_STB_HAS_NO_ROOM);
        exit();
    }
    $roomId = $room['room_id'];

    //check subscriberId
    $subscriberId = $room['subscriber_id'];
    if (is_null($subscriberId) or $subscriberId==0){
        //guest sdh checkout
        echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
        exit();
    }

    $data = (empty($_GET['data']) ? '' : $_GET['data']);
//    $note = (empty($_GET['note']) ? '' : $_GET['note']);
//    $data = json_encode(['note'=>$note]);

    $r = ModelHotelService::create($roomId, $subscriberId, $action, STATUS_NEW, $data);
    exitOnPdoException($r);

    echo json_encode(['success'=>$r]);
}

function doServiceCallTaxi($action, $stbId){

    //ambil room
    $room = ModelStb::get($stbId);
    if (is_null($room)){
        echo errCompose(ERR_STB_HAS_NO_ROOM);
        exit();
    }
    $roomId = $room['room_id'];

    //check subscriberId
    $subscriberId = $room['subscriber_id'];
    if (is_null($subscriberId) or $subscriberId==0){
        //guest sdh checkout
        echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
        exit();
    }

    $date = (empty($_GET['date']) ? '' : $_GET['date']);
//    $time = (empty($_GET['time']) ? '' : $_GET['time']);
    $destination = (empty($_GET['destination']) ? '' : $_GET['destination']);

    $data = json_encode(['date'=>$date, 'destination'=>$destination]);

    $r = ModelHotelService::create($roomId, $subscriberId, $action, STATUS_NEW, $data);
    exitOnPdoException($r);

    echo json_encode(['success'=>$r]);
}

function doCancel($stbId){
    //ambil room
    $room = ModelStb::get($stbId);
    if (is_null($room)){
        echo errCompose(ERR_STB_HAS_NO_ROOM);
        exit();
    }
    $roomId = $room['room_id'];

    //check subscriberId
    $subscriberId = $room['subscriber_id'];
    if (is_null($subscriberId) or $subscriberId==0){
        //guest sdh checkout
        echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
        exit();
    }

    $taskId = (empty($_GET['task_id']) ? 0 : $_GET['task_id']);

    $r = ModelHotelService::cancel($roomId, $subscriberId, $taskId);
    exitOnPdoException($r);

    echo json_encode(['success'=>$r]);
}
