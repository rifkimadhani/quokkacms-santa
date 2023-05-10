<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 5/10/2023
 * Time: 9:43 AM
 */

define('ELEMENT_LOGO', 100000);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

//TODO: session Id di ignore dulu, nantinya akan mempergukana session pada tuser_session
//$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
    case 'get_info':
        doGetInfo();
        break;
    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

/**
 * ambil info dari berbagai sumber
 *
 */
function doGetInfo(){
    require_once '../../model/ModelSetting.php';
    require_once '../../model/ModelTheme.php';

    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

    $themeId = ModelSetting::getDefaultThemeId();
    $logo = ModelTheme::getElement($themeId, ELEMENT_LOGO);

    $logo['url_image'] = str_replace('{BASE-HOST}', $baseHost, $logo['url_image']);

    $name = ModelSetting::getSiteName();
    $addr = ModelSetting::getSiteAddress();

    echo json_encode( ['site_name'=>$name, 'site_address'=>$addr, 'logo'=>$logo] );
}
