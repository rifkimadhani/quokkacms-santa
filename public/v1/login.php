<?php
/**
 * Created by PhpStorm.
 * User: echri
 * Date: 26/10/2022
 * Time: 9:33
 */

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';

require_once __DIR__ . '/../../model/ModelUser.php';

define('LOGIN_EMAIL', 'EMAIL');
define('LOGIN_MSISDN', 'MSISDN');
define('LOGIN_FACEBOOK', 'FACEBOOK');
define('LOGIN_GOOGLE', 'GOOGLE');
define('LOGIN_INSTAGRAM', 'INSTAGRAM');

ini_set('display_errors', 'on');

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action){

    case 'login_msisdn':
        doLoginMsisdn();
        break;

    case 'login_email':
        doLoginEmail();
        break;

    case 'login_facebook':
        doLoginFacebook();
        break;

    case 'login_google':
        doLoginGoogle();
        break;

    case 'login_instagram':
        doLoginInstagram();
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

function doLoginMsisdn(){
    $msisdn = isset($_GET['msisdn']) ? $_GET['msisdn'] : '';
    $password = isset($_GET['password']) ? $_GET['password'] : ''; //real password
    $device = isset($_GET['device']) ? $_GET['device'] : ''; //ANDROID / IOS

    $user = ModelUser::getByMsisdn($msisdn);

    if ($user instanceof PDOException){
        echo errCompose($user);
        return;
    }

    //1. msisdn tdk ketemu
    if ($user==null){
        echo errCompose(ERR_USER_NOT_FOUND);
        return;
    }

    //2. check msisdn apakah sdh di verifikasi
    if ($user['msisdn_state']<2){
        echo errCompose(ERR_MSISDN_NOT_VERIFIED);
        return;
    }

    //3. user is block
    if ($user['is_block']==true){
        echo errCompose(ERR_USER_ISBLOCK);
        return;
    }

    //begin check password
    $hash = $user['hash'];
    $salt = $user['salt'];

    require_once __DIR__ . '/../../library/Security.php';
    $xor = Security::xor64($password, $salt);
    $sha = hash('sha512', $xor, true);
    $hash2 = base64_encode($sha);

//    echo json_encode(['hash1'=>$hash, 'hash2'=>$hash2]);

    //password tdk match
    if ($hash!=$hash2){
        echo errCompose(ERR_PASSWORD_INVALID_OR_ACC_NOT_FOUND);
        return;
    }

    //create session utk mobile user
    require_once __DIR__ . '/../../model/ModelSession.php';
    $userId = $user['user_id'];
    $id = ModelSession::create($userId, $device, LOGIN_MSISDN);
    $session = ModelSession::get($id);

    echo json_encode(['user_id'=>$userId, 'salt' => $session['salt'], 'session_id' => $session['session_id']]);
}

function doLoginEmail(){
    $email = isset($_GET['email']) ? $_GET['email'] : '';
    $password = isset($_GET['password']) ? $_GET['password'] : ''; //real password
    $device = isset($_GET['device']) ? $_GET['device'] : ''; //ANDROID / IOS

    $user = ModelUser::getByEmail($email);

    if ($user instanceof PDOException){
        echo errCompose($user);
        return;
    }

    //1. email tdk ketemu
    if ($user==null){
        echo errCompose(ERR_USER_NOT_FOUND);
        return;
    }

    //2. check msisdn apakah sdh di verifikasi
    if ($user['email_state']<2){
        echo errCompose(ERR_EMAIL_NOT_VERIFIED);
        return;
    }

    //3. user is block
    if ($user['is_block']==true){
        echo errCompose(ERR_USER_ISBLOCK);
        return;
    }

    //begin check password
    $hash = $user['hash'];
    $salt = $user['salt'];

    require_once __DIR__ . '/../../library/Security.php';
    $xor = Security::xor64($password, $salt);
    $sha = hash('sha512', $xor, true);
    $hash2 = base64_encode($sha);

//    echo json_encode(['hash1'=>$hash, 'hash2'=>$hash2]);

    //password tdk match
    if ($hash!=$hash2){
        echo errCompose(ERR_PASSWORD_INVALID_OR_ACC_NOT_FOUND);
        return;
    }

    //create session utk mobile user
    require_once __DIR__ . '/../../model/ModelSession.php';
    $userId = $user['user_id'];
    $id = ModelSession::create($userId, $device, LOGIN_EMAIL);
    $session = ModelSession::get($id);

    echo json_encode(['user_id'=>$userId, 'salt' => $session['salt'], 'session_id' => $session['session_id']]);
}

function doLoginFacebook()
{
    $fb_access_token = isset($_GET['token']) ? $_GET['token'] : NULL;
    $device = isset($_GET['device']) ? $_GET['device'] : '';

    $url = 'https://graph.facebook.com/v2.9/me?access_token=' . $fb_access_token . '&fields=id,name,about,hometown,location,gender,birthday,email,sports,picture.width(800).height(800){url}';

    $graph = @file_get_contents($url, true);
    if ($graph === false) {
        echo errCompose(ERR_FACEBOOK_TOKEN_INVALID);
        return;
    }

    $graph = json_decode($graph, true);
    $name = isset($graph['name']) ? $graph['name'] : NULL;
    $facebookId = isset($graph['id']) ? $graph['id'] : NULL;
    $picture = isset($graph['picture']) ? $graph['picture']['data']['url'] : NULL;

    //di bawah ini properties yg tdk bisa di extract
//    $email = isset($graph['email']) ? $graph['email'] : '';
    $birthday = isset($graph['birthday']) ? $graph['birthday'] : NULL; //format date dari fb m/d/y
    $gender = isset($graph['gender']) ? $graph['gender'] : NULL;
//    $about = isset($graph['about']) ? $graph['about'] : NULL;
//    $hobby = isset($graph['sports']) ? $graph['sports'] : NULL;
//    $location    = isset($graph['location']) ? $graph['location']['name'] : NULL;
//    $from        = isset($graph['hometown']) ? $graph['hometown']['name'] : NULL;

    if (isset($gender)) {
        if ($gender == "male") $gender = "M"; else $gender = "F";
    }

    if (isset($birthday)) {
        //conversikan format dari mdy --> ymd
        $dateTime = DateTime::createFromFormat('m/d/Y', $birthday);
        $birthday = $dateTime->format('Y/m/d');
    }

//    Log::writeLn("graph=" . var_export($graph, true));
    Log::writeLn("name=" . $name);
    Log::writeLn("gender=" . $gender);
//    Log::writeLn("email=" . $email);
    Log::writeLn("facebookId=" . $facebookId);
    Log::writeLn("urlPP=" . $picture);
    Log::writeLn("birthday=" . $birthday);

    //cari user dari facebookId
    $user = ModelUser::getByFacebook($facebookId);

    if ($user instanceof PDOException){
        echo errCompose($user);
        return;
    }

    if ($user==null){
        require_once __DIR__ . '/../../model/ModelProfile.php';

        //1. create user baru
        $userId = ModelUser::create();
        ModelUser::updateFacebook($userId, $facebookId);

        //2. create profile baru
        ModelProfile::create($userId);

        //3. isi kan data2 user
        ModelProfile::update($userId, ['name'=>$name, 'urlPP'=>$picture, 'birthdate'=>$birthday]);
    } else {
        $userId = $user['user_id'];

        //check apakah user di block?
        $isBlock = $user['is_block'];
        if ($isBlock==1){
            echo errCompose(ERR_USER_ISBLOCK);
            return;
        }
    }

    require_once __DIR__ . '/../../model/ModelSession.php';
    $id = ModelSession::create($userId, $device, LOGIN_FACEBOOK);
    $session = ModelSession::get($id);

    echo json_encode(['user_id'=>$userId, 'salt' => $session['salt'], 'session_id' => $session['session_id']]);
}

function doLoginGoogle()
{
    $google_access_token = isset($_GET['token']) ? $_GET['token'] : '';
    $device = isset($_GET['device']) ? $_GET['device'] : '';

    $url ='https://www.googleapis.com/oauth2/v3/tokeninfo?id_token='.$google_access_token;

    $graph = @file_get_contents($url, true);
    if ($graph === false) {
        echo errCompose(ERR_GOOGLE_TOKEN_INVALID);
        return;
    }

    $graph = json_decode($graph, true);

    $googleId = isset($graph['sub']) ? $graph['sub'] : '';
    $name = isset($graph['name']) ? $graph['name'] : '';
    $email = isset($graph['email']) ? $graph['email'] : '';
    $picture = isset($graph['picture']) ? $graph['picture'] : '';

    //cari user dgn googleId
    //
    $user = ModelUser::getByGoogle($googleId);

    if ($user instanceof PDOException){
        echo errCompose($user);
        return;
    }

    if ($user==null){
        //user blm ada, maka buat
        require_once __DIR__ . '/../../model/ModelProfile.php';

        //1. create user baru
        $userId = ModelUser::create();
        ModelUser::updateGoogle($userId, $googleId);

        //2. create profile baru
        ModelProfile::create($userId);

        //3. isi kan data2 user
        ModelProfile::update($userId, ['name'=>$name, 'urlPP'=>$picture]);
    } else {
        $userId = $user['user_id'];

        //check apakah user di block?
        $isBlock = $user['is_block'];
        if ($isBlock==1){
            echo errCompose(ERR_USER_ISBLOCK);
            return;
        }
    }

    require_once __DIR__ . '/../../model/ModelSession.php';
    $id = ModelSession::create($userId, $device, LOGIN_GOOGLE);
    $session = ModelSession::get($id);

    echo json_encode(['user_id'=>$userId, 'salt' => $session['salt'], 'session_id' => $session['session_id']]);
}

//TODO: perlu di test
function doLoginInstagram(){
    require_once __DIR__ . '/../../library/Instagram.php';

    $code = isset($_GET['code']) ? $_GET['code'] : '';
    $device = isset($_GET['device']) ? $_GET['device'] : '';

    $appId = '594963422406604';
    $appSecret = 'd390926926118bb490013479577bac6f';
    $urlRedir = 'https://cybertech-digital.com/metra/v1/instagram_callback.php/';

    $r = Instagram::getToken($appId, $appSecret, $urlRedir, $code);

    $data = json_decode($r);
    $token = $data->access_token;
    $instagramUserId = $data->user_id; //user id ini berbeda dgn user id saat ambil getInfo

    $r = Instagram::getInfo($token);
    $r = json_decode($r);
    $instagramId = $r->id;

    $user = ModelUser::getByInstagram($instagramUserId);

    if ($user instanceof PDOException){
        echo errCompose($user);
        return;
    }

//    $userModel = new ModelUser();

    //apabila user kosong maka create new user
    if ($user==null){
        //new instagram user
        $userId = ModelUser::create();
        ModelUser::update($userId, ['instagram_user_id'=>$instagramId, 'instagram_id'=>$instagramId]);
    } else {
        $userId = $user['userId'];
    }

//    ModelUser::updateInstagramToken($userId, $token);

    //3. buat session
    require_once __DIR__ . '/../../model/ModelSession.php';

    $session = ModelSession::create($userId, $device, LOGIN_INSTAGRAM);

    echo json_encode(['user_id'=>$userId, 'salt' => $session['salt'], 'session_id' => $session['session_id']]);
}