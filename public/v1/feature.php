<?php
/**
 * Created by PhpStorm.
 * User: echri
 * Date: 06/09/2021
 * Time: 14:15
 */

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../model/ModelStbCredential.php';

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
    case 'get_list':
        doGetList($stbId);
        break;
}

exit();

function doGetList(){
    require_once __DIR__ . '/../../model/ModelSetting.php';

    $menuList = ModelSetting::getFeatureAppMenuList();
    $appList= ModelSetting::getFeatureMoreAppList();

    echo json_encode(['menu_list'=>$menuList, 'app_list'=>$appList]);
}