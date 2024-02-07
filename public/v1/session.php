<?php
require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../library/DateUtil.php';
require_once __DIR__ . '/../../library/Security.php';

require_once __DIR__ . '/../../model/ModelSession.php';
require_once __DIR__ . '/../../model/ModelUser.php';
require_once __DIR__ . '/../../model/ModelProfile.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

//ini_set('display_errors', 'on');

$action = isset($_GET['action']) ? $_GET['action'] : null;

switch ($action) {
    case 'reg_token':
        regToken();
        break;
    case 'update_device_info':
        doUpdateDeviceInfo();
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

function regToken()
{
//    $errObj = new ErrorAPI();
//    $security = new Security();
//    $sess = new ModelSession();
    $sessionId = isset($_GET['session_id']) ? $_GET['session_id'] : '';
    $token = isset($_GET['token']) ? $_GET['token'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';
//    $signatureApp = isset($_GET['sig']) ? $_GET['sig'] : '';

    $cekSess = ModelSession::validate($sessionId);
    if ($cekSess == NULL) {
        http_response_code(HTTP_BAD_REQUEST);
        echo errCompose(ERR_SESSIONID_NOT_FOUND);
//        $response = $errObj->compose(ErrorAPI::INVALID_SESSIONID);
//        header('Content-type: application/json');
//        echo json_encode($response);
        return;
    }

    //update token
    $r = ModelSession::updateToken($sessionId, $type, $token);

    if ($r instanceof PDOException){
        echo errCompose($r);
        return;
    }

    echo json_encode(['success'=>$r]);

//    $cekExpSess = $sess->cekExpSession($sessionId);
//    if ($cekExpSess == NULL) {
//        $response = $errObj->compose(ErrorAPI::EXPIRED_SESSIONID);
//        header('Content-type: application/json');
//        echo json_encode($response);
//        return;
//    } else {
//        $getSalt = $sess->getSalt($sessionId);
//        $signatureServer = $security->genHash($token, $getSalt);
//        if ($signatureApp == $signatureServer) {
//            $updateSess = $sess->updateDeviceType($token, $type, $sessionId);
//            if ($updateSess == true) {
//                $updateSess = $sess->updateExpDate($sessionId);
//                if ($updateSess == true) {
//                    //berhasil update semua
//                    $response = array('output' => 1);
//                    header('Content-type: application/json');
//                    echo json_encode($response);
//                    return;
//                } else {
//                    //hanya update device dan type
//                    $response = array('output' => 2);
//                    header('Content-type: application/json');
//                    echo json_encode($response);
//                    return;
//                }
//            } else {
//                //gagal semua update
//                $response = $errObj->compose(ErrorAPI::INVALID_UPDATE);
//                header('Content-type: application/json');
//                echo json_encode($response);
//                return;
//            }
//        } else {
//            $response = $errObj->compose(ErrorAPI::INVALID_SIGNATURE);
//            header('Content-type: application/json');
//            echo json_encode($response);
//            return;
//        }
//
//    }
}

/**
 * update device information, versi express
 *
 */
function doUpdateDeviceInfo()
{
    require_once __DIR__ . '/../../library/IP2Location.php';
    require_once __DIR__ . '/../../model/ModelSession_v2.php';

    // todo update device info
    $ip = $_SERVER['REMOTE_ADDR'];

//    $db = new \IP2Location\Database('/opt/lampp/htdocs/IP2LOCATION-LITE-DB11.BIN', \IP2Location\Database::FILE_IO);
//    $records = $db->lookup($ip, \IP2Location\Database::ALL);
//    var_dump($records);

    $sessionId = (empty($_GET['session_id']) ? '' : $_GET['session_id']);
    $deviceType = (empty($_GET['device_type']) ? '' : $_GET['device_type']);
    $applicationId = (empty($_GET['application_id']) ? 0 : $_GET['application_id']);
    $versionName = (empty($_GET['version_name']) ? '' : $_GET['version_name']);
    $versionCode = (int)(empty($_GET['version_code']) ? 0 : $_GET['version_code']);
    $versionApi = (empty($_GET['version_api']) ? '' : $_GET['version_api']);
    $debug = (empty($_GET['debug']) ? 'false' : $_GET['debug']);
    $device_name = (empty($_GET['device_name']) ? null : $_GET['device_name']);
    $countryCode = null;//$records['countryCode']; //di ambil dari ip2location
    $cityName = null;//$records['cityName']; //di ambil dari ip2location
    $regionName = null;//$records['regionName']; //di ambil dari ip2location

//    if ($debug == 'true') {
//        $debug = 1;
//    } else $debug = 0;

    $r = ModelSession_v2::updateDeviceInfo($sessionId, $applicationId, $deviceType, $versionName, $versionCode, $versionApi, $device_name, $ip, $countryCode, $cityName, $regionName);

    exitOnPdoException($r);

    //todo return device datetime berdasarkan timezone yang diperoleh dari ip2location
    $timezone = null;//$records['timeZone'];
//    $timeZoneArray = explode(":", $timezone);
//    $hours = intval(str_replace("+", "", $timeZoneArray[0]));
//    $minutes = intval($timeZoneArray[1]);
    $date = new DateTime("now", new DateTimeZone('GMT'));
//    $date->add(new DateInterval("PT" . $hours . "H"));
    $dateTime = $date->format('Y-m-d H:i:s');

    if (strtolower($deviceType) == "stb") {
        require_once __DIR__ . '/../../model/ModelApp.php';
        $appId = (empty($_GET['appId']) ? '' : $_GET['appId']);
        $app = ModelApp::getLatest($appId, $versionCode);
//        $newTimeZone = str_replace(":","",$timezone);
        echo json_encode(['success' => $r, "network_time" => $dateTime, "time_zone" => $timezone, "app"=>$app]);

    } else {
        echo json_encode(['success' => $r, "network_time" => $dateTime, "time_zone" => $timezone]);
    }


}

?>