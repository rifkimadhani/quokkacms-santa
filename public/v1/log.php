<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 09/03/2022
 * Time: 9:13
 */
require_once __DIR__ . '/../../model/ModelStbCredential.php';

//check stb sessionId
$stbId = ModelStbCredential::check();

disconnect(json_encode(['success'=>1]));

//get variable from query
$type = (empty($_GET['type']) ? '' : $_GET['type']);
$message = (empty($_GET['message']) ? '' : $_GET['message']);

$ip = $_SERVER['REMOTE_ADDR'];
$level = getLevel($type);

require_once 'model/ModelLog.php';

ModelLog::create($stbId, $ip, $type, $level, $message);

exit();

function getLevel(string $type):int {
    switch (strtoupper($type)){
        case 'FATL':
            return 10;
        case 'ERRR':
            return 20;
        case 'WARN':
            return 30;
        case 'INFO':
            return 40;
        case 'DEBG':
            return 50;
        case 'VERB':
            return 60;
        default:
            return -1;
    }
}

/**
 * disconnect script dari user,
 * response ke user akan sgt cepat krn tdk harus menunggu script selesai
 *
 * @param $response output json
 */
function disconnect($json)
{
    // let's free the user, but continue running the
    // script in the background
    ignore_user_abort(true);
    header("Connection: close");
    ob_start();

    header("Content-Length: " . mb_strlen($json));
    header('Content-type: application/json; charset=utf-8');
    echo $json;

    ob_end_flush();
    ob_flush();
    flush();
}