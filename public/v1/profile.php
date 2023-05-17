<?php

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';

require_once __DIR__ . '/../../model/ModelSession.php';
require_once __DIR__ . '/../../model/ModelProfile.php';
require_once __DIR__ . '/../../library/DateUtil.php';
require_once __DIR__ . '/../../library/http_errorcodes.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

$action = (empty($_GET['action']) ? '' : $_GET['action']);
$sessionId = (empty($_GET['sessionId']) ? '' : $_GET['sessionId']);

$sesObj = new ModelSession();
$userId = $sesObj->validate($sessionId);

//userId==null, sessionId tdk valid/expired
if (isset($userId)==false){
    echo errCompose(ERR_SESSIONID_NOT_FOUND);
    return;
}

switch ($action){
    case "uploadpp":
        doUploadPP($userId);
        break;
    case "update":
        doUpdate($userId);
        break;
    case "getOther":
        doGetOther($userId);
        break;
    case 'get':
        doGetMyProfile($userId);
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

/***
 * Get profile
 * @param $userId
 */
function doGetMyProfile(string $userId){
    require_once __DIR__ . '/../../model/ModelSession_v2.php';

    $profile = ModelProfile::get($userId);

    if ($profile==null){
        echo errCompose(ERR_SESSIONID_NOT_FOUND);
        return;
    }

    echo json_encode($profile);
}

/** update user profile
 * @param string $userId
 */
function doUpdate(string $userId){

    $name = isset($_GET['name']) ? $_GET['name'] : null;
    $birthdate = isset($_GET['birthday']) ? $_GET['birthday'] : null;
    $gender = isset($_GET['gender']) ? strtoupper($_GET['gender']) : null;
    $aboutMe= isset($_GET['aboutMe']) ? $_GET['aboutMe'] : null;
    $hobby= isset($_GET['hobby']) ? $_GET['hobby'] : null;
    $location = isset($_GET['location']) ? $_GET['location'] : null;
    $from = isset($_GET['from']) ? $_GET['from'] : null;
    $education = isset($_GET['education']) ? $_GET['education'] : null;

    //check date, make date = null if invalid
    $ar = date_parse($birthdate);
    if ($ar['error_count']>0) $birthdate = null;

    switch ($gender){
        case 'MALE':
        case 'M':
            $gender = 'M';
            break;
        case 'FEMALE':
        case 'F':
            $gender = 'F';
            break;
        default:
            $gender = '';
    }

    $profile =  new ModelProfile();
    $r = $profile->update($userId, ['name'=>$name, 'birthdate'=>$birthdate, 'gender'=>$gender, 'aboutMe'=>$aboutMe, 'hobby'=>$hobby, 'location'=>$location, 'from'=>$from, 'education'=>$education]);

    if ($r instanceof PDOException){
        echo errCompose($r);
        exit();
    }

    $root = array(
        "success"=>$r);

    header('Content-type: application/json');
    echo json_encode($root);
}

/** Ambil profile user lain, dan juga add countViewer
 * @param $userId
 */
function doGetOther($userId){

    $otherUserId = isset($_GET['otherUserId']) ? $_GET['otherUserId'] : null;

    $profile = new ModelProfile();

    //countViewer hanya utk user melihat user lain
    //apabila user melihat dirinya sendiri maka counter tdk di lakukan
    if ($otherUserId!=$userId){
        $result = $profile->updateCountViewer($otherUserId);
    } else {
        $result = 1;
    }

    $error = new ErrorAPI();

    if ($result==0){
        $response = $error->compose(ErrorAPI::INVALID_USER_ID);
        header('Content-type: application/json');
        echo json_encode($response);
        return;
    }

    $profileItem = $profile->get($otherUserId);

    $root = array(
        "userId"=>$otherUserId,
        "name"=>$profileItem->name,
        "birthdate"=>$profileItem->birthdate,
        "gender"=>$profileItem->gender,
        "aboutMe"=>$profileItem->aboutMe,
        "hobby"=>$profileItem->hobby,
        "location"=>$profileItem->location,
        "from"=>$profileItem->from,
        "urlPP"=>$profileItem->urlPP,
        "countViewer"=>$profileItem->countViewer,
    );

    header('Content-type: application/json');
    echo json_encode($root);
}

function doUploadPP(string $userId){
    require_once '../../model/ModelSetting.php';

    $profilePP = isset($_FILES['filename']) ? $_FILES['filename'] : null;

    //1. check if image is exist
    if (is_null($profilePP)){
        $error = new ErrorAPI();
        $response = $error->compose(ErrorAPI::PROFILE_IMAGE_NOTFOUND);
        header('Content-type: application/json');
        echo json_encode($response);
        return;
    }

//    var_dump($profilePP);

    //2. check type of file uploaded
    $tempfile = $profilePP['tmp_name'];
    $type = exif_imagetype ($tempfile);

	if ($type!=IMAGETYPE_JPEG && $type!=IMAGETYPE_PNG && $type!=IMAGETYPE_BMP){
        $error = new ErrorAPI();
        $response = $error->compose(ErrorAPI::PROFILE_IMAGE_TYPE_NOT_SUPPORTED);
        header('Content-type: application/json');
        echo json_encode($response);
        Log::var_dump($response);
        return;
    }

    //create userpp folder
    if (!file_exists('../userpp')) {
        mkdir('../userpp', 0777, true);
    }

    $ext = pathinfo($profilePP['name'], PATHINFO_EXTENSION);

    //move uploaded file to correct folder
    $newfilename = "pp-{$userId}.{$ext}";
    $dest = "../userpp/{$newfilename}";

    //matikan sementara error reporting
    error_reporting(null);

    if (move_uploaded_file($tempfile, $dest)) {

    	//aktifkan kembali error reporting
    	error_reporting(E_ERROR | E_WARNING | E_PARSE);

    } else {
	    $error = new ErrorAPI();
	    $response = $error->compose(ErrorAPI::PROFILE_UPLOADFAIL_INTERNALERROR);
	    header('Content-type: application/json');
	    echo json_encode($response);
	    Log::var_dump($response);
	    return;
    }

    //get url from database tsetting
    $urlHost = ModelSetting::getUrlUserPp();
    $urlPP = "$urlHost/$newfilename";

//    $urlPP = Log::host()."/topas/userpp/$newfilename";

    $profileObj = new ModelProfile();
    $profileObj->updateUrlPP($userId, $urlPP);

    header('Content-type: application/json');
    $root = array("urlPP"=> $urlPP);
    echo json_encode($root);
}

function resize_image($file, $w, $h, $crop=FALSE) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    var_dump($dst);

    return $dst;
}


