<?php

require_once '../library/Log.php';

require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/../library/http_errorcodes.php';
require_once __DIR__ . '/model/ModelVodSubtitle.php';

//no cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

//Log::writeLn("=====================================================================");
//Log::writeRequestUri();

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_list': //get list subtitle dari vodid
        doGetList();
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

function doGetList(){

    $vodId = isset($_GET['vod_id']) ? $_GET['vod_id'] : 0;

    $subtitle = ModelVodSubtitle::getByVodId($vodId);

    if ($subtitle instanceof PDOException){
        echo errCompose($subtitle);
        return;
    }

    http_response_code(HTTP_OK);

    echo json_encode(['count'=>count($subtitle), 'list'=>$subtitle]);
}