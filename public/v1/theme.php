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

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get_list':
		doGetList($stbId);
		break;
	case 'get_last_update':
		doGetLastUpdate($stbId);
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
function doGetList($stbId){
	require_once '../../model/ModelStb.php';
	require_once '../../model/ModelTheme.php';
	require_once '../../model/ModelSubscriber.php';
	require_once '../../model/ModelSetting.php';

	$room = ModelStb::get($stbId);
	if(empty($room)){
		echo errCompose(ERR_MAC_ADDRESS_NOT_FOUND);
		die();
	}

	//cari subscriberId
	$subscriberId = $room['subscriber_id'];
	if(empty($subscriberId)) $subscriberId = 0;

	$subscriber = ModelSubscriber::get($subscriberId);
	if ($subscriber instanceof PDOException){
		echo errCompose($subscriber);
		die();
	}

	//ambil themeId dari subscriber
    if ($subscriber!=null) $themeId = $subscriber['theme_id'];

	//apabila themeId pada subscriber kosong
	//maka ambil themeId dari room
	if (empty($themeId)){
		$themeId = $room['theme_id'];

		//apabila room tdk punya themeId, maka ambil dari default
		if (empty($themeId)){
			$themeId = ModelSetting::getDefaultThemeId();
		}
	}

	$theme = ModelTheme::get($themeId);
	if(empty($theme)){
		echo errCompose(ERR_THEME_NOT_DEFINED);
		die();
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

/**
 * akan return json, tgl terakhir theme di update
 * apabila stb
 *
 * @param $stbId
 */
function doGetLastUpdate($stbId){
    require_once '../../model/ModelStb.php';
    require_once '../../model/ModelTheme.php';
    require_once '../../model/ModelSubscriber.php';
    require_once '../../model/ModelSetting.php';

    $room = ModelStb::get($stbId);
    if(empty($room)){
        echo errCompose(ERR_STBID_NOT_VALID);
        return;
    }

    //cari subscriberId
    $subscriberId = $room['subscriber_id'];
    if(empty($subscriberId)) $subscriberId = 0;

    $subscriber = ModelSubscriber::get($subscriberId);
    if ($subscriber instanceof PDOException){
        echo errCompose($subscriber);
        return;
    }

    //ambil themeId dari subscriber
    if ($subscriber!=null) $themeId = $subscriber['theme_id'];

    //apabila themeId pada subscriber kosong
    //maka ambil themeId dari room
    if (empty($themeId)){
        $themeId = $room['theme_id'];

        //apabila room tdk punya themeId, maka ambil dari default
        if (empty($themeId)){
            $themeId = ModelSetting::getDefaultThemeId();
        }
    }

    $theme = ModelTheme::getLastUpdate($themeId);
    if(empty($theme)){
        echo errCompose(ERR_THEME_NOT_DEFINED);
        return;
    }

    if ($theme==null || $theme instanceof PDOException || sizeof($theme)==0){
        //apabila tdk ada record, maka set lastupdate ke tgl tua
        $lastUpdate = '2020-01-01 00:00:00';
    } else {
        $lastUpdate = $theme[0]['update_date'];
    }

    echo json_encode( [ 'last_update'=>$lastUpdate] );
}