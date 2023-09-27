<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 9/20/2023
 * Time: 5:32 PM
 */

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';

Log::writeLn('==========================================================================================================');
Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
    case 'auth':
        doAuth();
        break;
    case 'disconnect':
        doDisconnect();
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

function doAuth(){
    require_once __DIR__ . '/../../model/ModelStb.php';

    $sessionId = (empty($_GET['session_id']) ? '' : $_GET['session_id']);

    $stb= ModelStb::getFromSessionId($sessionId);

    if ($stb instanceof PDOException){
        Log::writeErrorLn($stb);
//        echo errCompose($stb);
        echo '0';
        return;
    }

    //stb not found
    if ($stb==null){
        echo '0';//json_encode(['status'=>0]);
        return;
    }

    echo '1';//json_encode(['status'=>1]);

    //stbid akan di pakai utk rubah status online menjadi 1
    $stbId = $stb['stb_id'];

    ModelStb::updateStatus($stbId, 1);
}

function doDisconnect(){
    require_once __DIR__ . '/../../model/ModelStb.php';

    $sessionId = (empty($_GET['session_id']) ? '' : $_GET['session_id']);

    $stb = ModelStb::getFromSessionId($sessionId);

    if ($stb instanceof PDOException){
        return;
    }

    //stb not found
    if ($stb==null){
        return;
    }

    //stbid akan di pakai utk rubah status online menjadi 1
    $stbId = $stb['stb_id'];

    ModelStb::updateStatus($stbId, 0);
}



//require_once __DIR__ . '/../../library/Dispatcher.php';
//
//$hostname = '127.0.0.1';
//$port = 8070;
//
//$d = new \Dispatcher();
//
//$d->connect($hostname, $port);
//
//$d->send('device-stb1,device-stb2', 'message');
//$d->send('room-1', 'warning');
//
//echo "Connect $hostname:$port";
//
