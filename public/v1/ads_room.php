<?php
/**
 * Created by PhpStorm.
 * User: echri
 * Date: 18/02/2021
 * Time: 10:08
 */

const SYSTEM_TYPE = 'TARGETADS';

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../model/ModelStbCredential.php';


Log::writeLn('==========================================================================================================');
Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
    case 'get_list':
        doGetList($stbId);
        break;
}

exit();

function doGetList($stbId){
    require_once '../../model/ModelStb.php';

    $stb = ModelStb::get($stbId);

    $spotId = (empty($_GET['spot_id']) ? 0 : $_GET['spot_id']);
    $roomId = $stb['room_id'];

    require_once '../../model/ModelAds.php';

    $list = ModelAds::getByRoomId($spotId, $roomId);

    require_once '../../model/ModelSetting.php';

    $urlHost = ModelSetting::getHostApi();
    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

    foreach($list as &$item){
        $item['url_image'] = str_replace('{HOST}', $urlHost, $item['url_image']);
        $item['url_image'] = str_replace('{BASE-HOST}', $baseHost, $item['url_image']);
    }

    echo json_encode( [ 'count'=>count($list), 'list'=>$list] );
}

