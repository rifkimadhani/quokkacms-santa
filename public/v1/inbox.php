<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 8/9/2017
 * Time: 12:44 PM
 */

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelStbCredential.php';


require_once __DIR__ . '/model/ModelSession.php';
require_once __DIR__ . '/model/ModelUser.php';
require_once __DIR__ . '/model/ModelInbox.php';
require_once __DIR__ . '/model/ModelSetting.php';

//Log::writeLn("=====================================================================");
//Log::writeRequestUri();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

$action = isset($_GET['action']) ? $_GET['action'] : '';
//$sessionId = isset($_GET['sessionId']) ? $_GET['sessionId'] : '';

//$sesObj = new ModelSession();
//$userId = $sesObj->validate($sessionId);
//$salt = $sesObj->getSalt($sessionId);

//userId==null, sessionId tdk valid/expired
//if (isset($userId) == false) {
//    $error = new ErrorAPI();
//    $response = $error->compose(ErrorAPI::INVALID_SESSIONID);
//    header('Content-type: application/json');
//    echo json_encode($response);
//    return;
//}

switch ($action) {
//    case "send":
//        doSend($sessionId, $userId, $salt);
//        break;
//    case "get":
//        doGet($userId);
//        break;
//    case "clean":
//        doClean($sessionId, $userId);
//        break;
//    case "delete":
//        doDelete($userId);
//        break;

    case 'get_list':
        doGetList();
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

/**Send message to user
 * @param string $sessionId
 * @param string $userId
 *
 */
function doSend(string $sessionId, string $userId, string $salt)
{
    $toUserId = isset($_GET['toUserId']) ? $_GET['toUserId'] : null;
    $messageType = isset($_GET['messageType']) ? $_GET['messageType'] : 1;
    $message = isset($_GET['message']) ? $_GET['message'] : null;
    $sig = isset($_GET['sig']) ? $_GET['sig'] : null;
    $image = isset($_FILES['uploaded_file']['name']) ? $_FILES['uploaded_file']['name'] : NULL;
    $file_path_db = "";

    $error = new ErrorAPI();

//    upload image
//    if ($image == NULL) {
//        $error = new ErrorAPI();
//        $response = $error->compose(ErrorAPI::FILENOTFOUND);
//        header('Content-type: application/json');
//        echo json_encode($response);
//        return;
//    }

    if ($image != NULL) {
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/topas/upload/admin_folder/admin_video/user_folder/";
        $file_path_base = $_SERVER['DOCUMENT_ROOT'] . "/topas/upload/admin_folder/admin_video/user_folder/";
        $security = new Security();
        $sigServer = $security->genHash($sig, $salt);
        $ext = substr(strrchr($image, '.'), 1);

        do {
            $image = $security->random(8) . time();
            $image = $image . "." . $ext;
            $file_path = $file_path_base . $image;

        } while (file_exists($file_path));

        if (isset($_SERVER['HTTPS'])) {
            $host = "https://" . $_SERVER['HTTP_HOST'];
        } else {
            $host = "http://" . $_SERVER['HTTP_HOST'];
        }
        $file_path_db = $host . "/topas/upload/admin_folder/admin_video/user_folder/" . $image;
        $upload = move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path);
        if ($upload) {
            if ($ext == 'png' or $ext == 'PNG') {
                $im_src = imagecreatefrompng($file_path);
                $src_width = imageSX($im_src);
                $src_height = imageSY($im_src);
                $dst_width = 720;
                $dst_height = ($dst_width / $src_width) * $src_height;
                $im = imagecreatetruecolor($dst_width, $dst_height);
                imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
                imagepng($im, $dir . $image);
            } elseif ($ext == 'JPG' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'JPEG') {
                $im_src = imagecreatefromjpeg($file_path);
                $src_width = imageSX($im_src);
                $src_height = imageSY($im_src);
                $dst_width = 720;
                $dst_height = ($dst_width / $src_width) * $src_height;
                $im = imagecreatetruecolor($dst_width, $dst_height);
                imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
                imagejpeg($im, $dir . $image);
            }
        }
    }


    //1. check all parameter touserId & message tdk boleh kosong
    if (isset($toUserId) == false || isset($message) == false || isset($sig) == false) {
        $response = $error->compose(ErrorAPI::INVALID_PARAMETER);
        header('Content-type: application/json');
        echo json_encode($response);
        return;
    }

    if($message!="") {
        //2. check signature
        $sesObj = new ModelSession();
        if ($sesObj->validateSignature($sessionId, $message, $sig) == 0) {
            $root = $error->compose(ErrorAPI::INVALID_SIGNATURE);
            header('Content-type: application/json');
            echo json_encode($root);
            return;
        }
    }

    //3. check destination user
    $user = new ModelUser();
    $userItem = $user->get($toUserId);
    if (isset($userItem) == false) {
        $root = $error->compose(ErrorAPI::INBOX_INVALID_TOUSERID);
        header('Content-type: application/json');
        echo json_encode($root);
        return;
    }

    //TODO:: 4. check apakah toUserId adalah teman dari user ?

    //Add message ke table
    $inboxId = ModelInbox::create($toUserId, $userId, $messageType, $message, $file_path_db);
//    $inboxId = ModelInbox::create($userId,$toUserId, $messageType, $message);
    //kirim reply ke caller
    require_once "../library/ConnectionUtil.php";
    ConnectionUtil::quickReply(function () use (&$inboxId,&$file_path_db) {
        $root = array('inboxId' => $inboxId,'image' => $file_path_db);
        header('Content-type: application/json');
        echo json_encode($root);
    });

    //5. Kirim notifikasi toUserId
    require_once "../config/C.php";
    require_once "../model/ModelProfile.php";
    require_once "../library/FirebaseMessage.php";

    $profile = new ModelProfile();
    $profileItem = $profile->get($userId);

//    $message = 'You receive a message';
    $data = array(
        'notifyId' => C::NOTIFYTYPE_INBOX_CHAT,
        'inboxId' => $inboxId,
        'message' => $message,
        'fromUserId' => $userId,
        'fromName' => $profileItem->name,
        'fromUrlPP' => $profileItem->urlPP,
        'image' => $file_path_db);

    $tokens = $sesObj->getDeviceTokensFromUserId($toUserId);

    if (isset($tokens) && count($tokens) > 0) {
        $result = FirebaseMessage::sendDataToTargets(C::FIREBASE_APIKEY, $data, $tokens);
        Log::writeLn($result);
    } else {
        Log::writeErrorLn('doSend no deviceToken found');
    }
}

/** Get inbox untuk user ini
 * @param string $userId
 */
function doGet(string $userId)
{
    $inboxId = isset($_GET['inboxId']) ? $_GET['inboxId'] : 0;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;

    $arInbox = ModelInbox::get($userId, $inboxId, $limit);

    $ar = array();
    foreach ($arInbox as $item) {
        $object = array(
            "inboxId" => $item->inboxId,
            "fromName" => $item->fromName,
            "fromUserId" => $item->fromUserId,
            "fromGender" => $item->fromGender,
            "fromUrlPP" => $item->fromUrlPP,
            "messageType" => $item->type,
            "message" => $item->message,
            "status" => $item->status,
            "createdDate" => DateUtil::formatDate($item->createdDate),
            "image" => $item->image

        );

        $ar[] = $object; //add object to array
    }

    $root = array("count" => count($ar), "array" => $ar);
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($root);
}

function doClean(string $sessionId, string $userId)
{
    $lastInboxId = isset($_GET['lastInboxId']) ? $_GET['lastInboxId'] : 0;
    $sig = isset($_GET['sig']) ? $_GET['sig'] : null;

    $error = new ErrorAPI();

    //1. check signature
    $sesObj = new ModelSession();
    if ($sesObj->validateSignature($sessionId, $lastInboxId, $sig) == 0) {
        $root = $error->compose(ErrorAPI::INVALID_SIGNATURE);
        header('Content-type: application/json');
        echo json_encode($root);
        return;
    }

    ModelInbox::removeAfterLastInboxId($userId, $lastInboxId);

    doGet($userId);
}

function doDelete(string $userId)
{
    $inboxId = isset($_GET['inboxId']) ? $_GET['inboxId'] : null;
    $otherUserId = isset($_GET['otherUserId']) ? $_GET['otherUserId'] : null;

    $error = new ErrorAPI();
    require_once "../config/C.php";
    require_once "../library/FirebaseMessage.php";

    //1. check all parameter $inboxId & $otherUserId tdk boleh kosong
    if (is_null($inboxId) || is_null($otherUserId)) {
        $response = $error->compose(ErrorAPI::INVALID_PARAMETER);
        header('Content-type: application/json');
        echo json_encode($response);
        return;
    }

    $result = ModelInbox::deleteStatus($userId, $inboxId, $otherUserId);
//    var_dump($userId);
//    var_dump($otherUserId);
    if ($result == 1) {
        $sesObj = new ModelSession();
        $data = array(
            'notifyId' => C::NOTIFYTYPE_INBOX_CHAT_DELETE,
            'inboxId' => $inboxId,
            'fromUserId' => $userId,
            'otherUserId' => $otherUserId);

        $tokens = $sesObj->getDeviceTokensFromUserId($otherUserId);

        if (isset($tokens) && count($tokens) > 0) {
            $resultFireBase = FirebaseMessage::sendDataToTargets(C::FIREBASE_APIKEY, $data, $tokens);
            Log::writeLn($resultFireBase);
        } else {
            Log::writeErrorLn('doSend no deviceToken found');
        }
    }
    header('Content-type: application/json');
    $root = array("result" => $result);
    echo json_encode($root);

//    //2. check signature
//    $sesObj = new ModelSession();
//    if ($sesObj->validateSignature($sessionId, $message, $sig)==0){
//        $root = $error->compose(ErrorAPI::INVALID_SIGNATURE);
//        header('Content-type: application/json');
//        echo json_encode($root);
//        return;
//    }


}

function doGetList(){

    $sessionId = isset($_GET['session_id']) ? $_GET['session_id'] : '';
    $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;

    $userId = 0;

    //cari userId dari session
    if (isset($sessionId)){
        require_once __DIR__ . '/model/ModelSession.php';
        $ses = ModelSession::get($sessionId);
        if (isset($ses)) $userId = $ses['user_id'];
    }

    $list = ModelInbox::getAll($userId, $offset, $limit);

//    $baseHost = baseUrl('../');
    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

    foreach ($list as &$item) {
        $item['url_image'] = str_replace('[BASEHOST]', $baseHost, $item['url_image']);
    }

    echo json_encode(['count'=>count($list), 'list'=>$list]);
}
