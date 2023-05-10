<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/4/2019
 * Time: 1:41 PM
 */

//const HOTEL_NAME = 'Hotel Pop';
//const HOTEL_ADDRESS = 'JAKARTA';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

$action = (empty($_GET['action']) ? '' : $_GET['action']);

//special treament utk ping, utk memberikan response tercepat saat di call
//
if ($action=='ping'){
    //langsung disconnect stb dari php,
    //tdk perlu menunggu apakah proses update ke db selesai atau tdk
    disconnect(json_encode(['success'=>1]));

    //proses di bawah di lakukan setelah disconnect, shg tdk reponse akan lbh cepat
    require_once '../../model/ModelStb.php';
    require_once __DIR__ . '/../../model/ModelStbCredential.php';
    $stbId = ModelStbCredential::check();
    $ip = $_SERVER['REMOTE_ADDR'];
    ModelStb::updateIpaddress($stbId, $ip);

    exit();
}


require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../model/ModelStbCredential.php';

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

//header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

//$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'update_info':
		doUpdateInfo($stbId);
		break;
	case 'get_info':
		doGetInfo($stbId);
		break;
    case 'get_time':
        doGetTime();
        break;
    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

function doUpdateInfo($stbId){
	require_once '../../model/ModelStb.php';
	require_once '../../model/ModelApp.php';
	require_once '../../model/ModelSubscriber.php';
	require_once '../../model/ModelSetting.php';
	require_once '../../model/ModelEmegencyState.php';

	$ip = $_SERVER['REMOTE_ADDR'];
	$appId = (empty($_GET['appId']) ? '' : $_GET['appId']);
	$versionName = (empty($_GET['versionName']) ? '' : $_GET['versionName']);
	$versionCode = (empty($_GET['versionCode']) ? 0 : $_GET['versionCode']);
	$androidVersion = (empty($_GET['androidVersion']) ? '' : $_GET['androidVersion']);
	$androidApi = (empty($_GET['androidApi']) ? 0 : $_GET['androidApi']);

	$result = ModelStb::updateInfo($stbId, $ip, $appId, $versionName, $versionCode, $androidVersion, $androidApi);

	$app = ModelApp::getLatest($appId, $versionCode);

    $welcomeMessage = ModelSetting::getWelcomeMessage();

    $stb = ModelStb::get($stbId);
	$subscriber = null;
	if (isset($stb)){
		$subscriberId = $stb['subscriber_id'];
		$subscriber = ModelSubscriber::get($subscriberId);
	}

	$tz = ModelSetting::getTimezone();
	$now = new DateTime();
	$now = $now->format('Y-m-d H:i:sO');

	//emergency state dari setting
	$emergencyState = ModelSetting::getEmergencyState();

	$state=null;
	if (isset($emergencyState['value_string'])) $state = $emergencyState['value_string'];

    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

    //apabila state exist maka ambil url utk download image
	$emergency = null;
	if (empty($state)==false){
//		$host = ModelSetting::getHostApi();
		$emergency = ModelEmegencyState::getByCode($state);
		$emergency = str_replace('{BASE-HOST}', $baseHost, $emergency);
	}

	if (empty($app)==false){
//        $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini
        $app['urlDownload'] = str_replace('{BASE_HOST}', $baseHost, $app['urlDownload']);
    }

	$hotelName = ModelSetting::getSiteName();
	$hotelAddress = ModelSetting::getSiteAddress();
	$weatherServer = ModelSetting::getWeatherServer();
	$currency = ModelSetting::getDefaultCurrency();
	$currencySign = ModelSetting::getDefaultCurrencySign();
	$weatherLocalCityId = ModelSetting::getWeatherLocalCityId();

    $baseHost = ModelSetting::getBaseHost('../'); //turun 2 level dari folder v1
    $weatherServer = str_replace('{BASE-HOST}', $baseHost, $weatherServer);

    echo json_encode( [ 'result'=>$result, 'app'=>$app, 'subscriber'=>$subscriber, 'time_zone'=>$tz, 'server_time'=>$now, 'emergency_state'=>$emergency, 'hotel_name'=>$hotelName, 'hotel_address'=>$hotelAddress, 'weather_server'=>$weatherServer, 'welcome_message'=>$welcomeMessage, 'currency'=>$currency, 'currency_sign'=>$currencySign, 'weather_local_cityid'=>$weatherLocalCityId] );
}

function doGetInfo($stbId){
	require_once '../../model/ModelStb.php';
	require_once '../../model/ModelSubscriber.php';
	require_once '../../model/ModelApp.php';

	$stb = ModelStb::get($stbId);

	if (empty($stb)) {
		echo errCompose(ERR_STBID_NOT_VALID);
		die();
	}

	if ($stb instanceof PDOException){
		echo errCompose($stb);
		die();
	}

	$subscriberId = $stb['subscriber_id'];

	$subscriber = null;
	if (isset($subscriberId)){
		$subscriber = ModelSubscriber::get($subscriberId);
	}

	if(isset($stb['app_id'])) $appId = $stb['app_id']; else $appId = '';
	if (isset($stb['version_code'])) $versionCode = $stb['version_code']; else $versionCode='';
	$app = ModelApp::getLatest($appId, $versionCode);

	if ($app!=null){
        require_once '../../model/ModelSetting.php';
        $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini
        $app['urlDownload'] = str_replace('{BASE_HOST}', $baseHost, $app['urlDownload']);
    }


    echo json_encode( ['stb'=>$stb, 'subscriber'=>$subscriber, 'app'=>$app] );
}

function doGetTime(){

    $now = new DateTime();
    $now = $now->format('Y-m-d H:i:sO');

    echo json_encode( ['server_time'=>$now ] );
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