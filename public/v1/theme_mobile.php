<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/5/2019
 * Time: 10:13 AM
 */

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../model/ModelStbCredential.php';
require_once __DIR__ . '/../../model/ModelStb.php';

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

//$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get_list':
		doGetList();
		break;
    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();


/**
 * list theme, pastikan MAC address sudah di daftar ke room
 *
 */
function doGetList(){
	require_once '../../model/ModelStb.php';
	require_once '../../model/ModelTheme.php';
	require_once '../../model/ModelSubscriber.php';
	require_once '../../model/ModelSetting.php';

    $themeId = ModelSetting::getDefaultThemeId();

	$theme = ModelTheme::get($themeId);
	if(empty($theme)){
		echo errCompose(ERR_THEME_NOT_DEFINED);
		exit();
	}

	//replace semua {HOST-IMAGE} & {BASE-HOST}
	//
	$hostImage = ModelSetting::getHostApi();
    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

    foreach ($theme as &$item){
		$item['url_image'] = str_replace('{HOST-IMAGE}', $hostImage, $item['url_image']);
        $item['url_image'] = str_replace('{BASE-HOST}', $baseHost, $item['url_image']);
	}

	echo json_encode( [ 'count'=>sizeof($theme), 'list'=>$theme] );
}
