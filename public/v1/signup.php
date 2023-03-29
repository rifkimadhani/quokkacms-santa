<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 21/09/2022
 * Time: 16:07
 */
require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../library/Security.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../library/http_errorcodes.php';
require_once __DIR__ . '/../../model/ModelUser.php';

define('EXP_CODE', '+3600 seconds'); //second, code expired
define('EXP_RESEND_CODE', '+30 seconds'); //second, resend code

ini_set('display_errors', 'on');

//no cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action){
    case 'signup_msisdn': //signup dgn nomor hp
        doSignupMsisdn();
        break;

    case 'req_msisdn_code': //request utk kirim code verifikasi msisdn
        doReqMsisdnCode();
        break;

    case 'check_msisdn_code': //check code
        doCheckMsisdnCode();
        break;

    case 'signup_email': //signup dgn nomor hp
        doSignupEmail();
        break;

    case 'req_email_code': //request utk kirim code verifikasi msisdn
        doReqEmailCode();
        break;

    case 'check_email_code': //check code
        doCheckEmailCode();
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

function doSignupMsisdn(){
    $msisdn = isset($_GET['msisdn']) ? $_GET['msisdn'] : '';
    $password = isset($_GET['password']) ? $_GET['password'] : '';

    if (empty($msisdn)){
        echo errCompose(ERR_MSISDN_MUST_NOT_EMPTY);
        return;
    }

    $user = ModelUser::getByMsisdn($msisdn);

    //1. msisdn tdk ada, buat new user
    if ($user==null){
        $userId = ModelUser::create();

        $salt = Security::randomString(64);
        $xor = Security::xor64($password, $salt);
        $sha = hash('sha512', $xor, true);
        $hash = base64_encode($sha);

        ModelUser::updateHash($userId, $hash, $salt);
        ModelUser::updateMsisdn($userId, $msisdn);
        echo json_encode(['status'=>1]);
        return;
    }

    $msisdn_state = $user['msisdn_state'];

    //state = 1 >> msisdn blm di confirm
    if ($msisdn_state==1){
        echo  errCompose(ERR_MSISDN_NOT_VERIFIED);
        return;
    }

    //state = 2 >> msisdn sdh di confirm
    echo errCompose(ERR_MSISDN_ALREADY_EXIST);
}

function doReqMsisdnCode(){
    require_once __DIR__ . '/../../library/Security.php';

    $msisdn = isset($_GET['msisdn']) ? $_GET['msisdn'] : '';

    $user = ModelUser::getByMsisdn($msisdn);

    if ($user==null){
        //apabila msisdn tdk ada maka return msisdn already exist
        //hal ini di sengaja utk menghindari api ini di abuse utk mencheck apakah
        //msisdn exist atau tdk
        echo errCompose(ERR_USER_NOT_FOUND);
        return;
    }

    $msisdn_state = $user['msisdn_state'];
    if ($msisdn_state >= 2){
        //msisdn sdh di verifikasi, maka return msisdn already exist
        echo errCompose(ERR_MSISDN_VERIFIED);
        return;
    }

    $now = time();

    //check resend apakah sdh bisa kirim sms lagi?
    $resend = strtotime($user['msisdn_code_resend']);

    if ($resend>$now){
        echo errCompose(ERR_MSISDN_REQ_CODE_TOO_FAST);
        return;
    }

    $userId = $user['user_id'];
    $code = Security::randomNumeric(4);
    $exp = strtotime(EXP_CODE, $now); //1 hour
    $resend = strtotime(EXP_RESEND_CODE, $now); //1 minute

    $exp = convert($exp);
    $resend = convert($resend);

    $e = ModelUser::updateMsisdnCode($userId, $code, $exp, $resend);

    echo json_encode(['status'=>1, 'exp'=>$exp, 'resend'=>$resend]);

    //send sms to user
}

function doCheckMsisdnCode(){
    require_once __DIR__ . '/../../model/ModelSession.php';

    $msisdn = isset($_GET['msisdn']) ? $_GET['msisdn'] : '';
    $code = isset($_GET['code']) ? $_GET['code'] : '';

    $user = ModelUser::getByMsisdn($msisdn);

    if ($user==null){
        //apabila null, maka return code verified,
        // hal ini di sengaja agar api tdk di abuse utk check nomor hp exist atau tdk
        echo errCompose(ERR_USER_NOT_FOUND);
        return;
    }

    //1. check state
    $msisdn_state = $user['msisdn_state'];
    if ($msisdn_state >= 2){
        //msisdn sdh di verifikasi, maka return code verified
        echo errCompose(ERR_MSISDN_VERIFIED);
        return;
    }

    //2. check apakah code sdh expired ?
    $exp = strtotime($user['msisdn_code_exp']);
    $now = time();
    if ($exp<$now){
        echo errCompose(ERR_MSISDN_CODE_EXPIRED);
        return;
    }

    //3. check code
    if ($code!=$user['msisdn_code']){
        echo errCompose(ERR_MSISDN_CODE_INVALID);
        return;
    }

    //code correct
    $userId = $user['user_id'];

    //msisdn confirmed
    $r = ModelUser::updateMsisdnState($userId, 2);

    if ($r instanceof PDOException){
        echo errCompose($r);
        return;
    }

    if ($r==0){
        echo errCompose(ERR_UNKNOWN);
        return;
    }

    echo json_encode(['status'=>$r]);
}



function doSignupEmail(){
    $email = isset($_GET['email']) ? $_GET['email'] : '';
    $password = isset($_GET['password']) ? $_GET['password'] : ''; //plain text password

    //1. email & password tdk boleh kosong
    if (empty($email)){
        echo errCompose(ERR_EMAIL_MUST_NOT_EMPTY);
        return;
    }

    $user = ModelUser::getByEmail($email);

    //2. msisdn tdk ada, buat new user
    if ($user==null){
        $userId = ModelUser::create();

        $salt = Security::randomString(64);
        $xor = Security::xor64($password, $salt);
        $sha = hash('sha512', $xor, true);
        $hash = base64_encode($sha);

        ModelUser::updateHash($userId, $hash, $salt);
        ModelUser::updateEmail($userId, $email);
        echo json_encode(['status'=>1]);
        return;
    }

    $state = $user['email_state'];

    //3. state = 1 >> msisdn blm di confirm
    if ($state==1){
        echo errCompose(ERR_EMAIL_NOT_VERIFIED);
        return;
    }

    //4. state = 2 >> email sdh di confirm
    echo errCompose(ERR_EMAIL_VERIFIED);
}

function doReqEmailCode(){
    require_once __DIR__ . '/../../library/Security.php';
    require_once __DIR__ . '/../../library/Mailgun.php';
    require_once __DIR__ . '/../../model/ModelSetting.php';

    $email = isset($_GET['email']) ? $_GET['email'] : '';

    if(empty($email)){
        echo errCompose(ERR_EMAIL_MUST_NOT_EMPTY);
        return;
    }

    $user = ModelUser::getByEmail($email);

    if ($user==null){
        //apabila email tdk ada maka return email already verified
        //hal ini di sengaja utk menghindari api ini di abuse utk mencheck apakah
        //email exist atau tdk
        echo errCompose(ERR_USER_NOT_FOUND);
        return;
    }

    $state = $user['email_state'];
    if ($state >= 2){
        //msisdn sdh di verifikasi, maka return msisdn already exist
        echo errCompose(ERR_EMAIL_VERIFIED);
        return;
    }

    $now = time();

    //check resend apakah sdh bisa kirim sms lagi?
    $resend = strtotime($user['email_code_resend']);

    if ($resend>$now){
        echo errCompose(ERR_EMAIL_REQ_CODE_TOO_FAST);
        return;
    }

    $userId = $user['user_id'];
    $code = Security::randomNumeric(4);
    $exp = strtotime(EXP_CODE, $now);
    $resend = strtotime(EXP_RESEND_CODE, $now);

    $exp = convert($exp);
    $resend = convert($resend);

    $e = ModelUser::updateEmailCode($userId, $code, $exp, $resend);

    $domain = ModelSetting::getMailgunDomain();
    $key = ModelSetting::getMailgunKey();
    $from = ModelSetting::getEmailFrom();
    $body = composeEmail($email, $code);

    //kirim email lewat mailgun
    Mailgun::send($domain, $key, $from, $email, 'Email Verification Code', $body);

    http_response_code(HTTP_OK);

    echo json_encode(['status'=>1, 'exp'=>$exp, 'resend'=>$resend]);
}

function doCheckEmailCode(){
    require_once __DIR__ . '/../../model/ModelSession.php';

    $email = isset($_GET['email']) ? $_GET['email'] : '';
    $code = isset($_GET['code']) ? $_GET['code'] : '';

    $user = ModelUser::getByEmail($email);

    if ($user==null){
        //apabila null, maka return code verified,
        // hal ini di sengaja agar api tdk di abuse utk check nomor hp exist atau tdk
        echo errCompose(ERR_USER_NOT_FOUND); //<<-- replace EMAIL_VERIFIED
        return;
    }

    //1. check state
    $state = $user['email_state'];
    if ($state >= 2){
        //msisdn sdh di verifikasi, maka return code verified
        echo errCompose(ERR_EMAIL_VERIFIED);
        return;
    }

    //2. check apakah code sdh expired ?
    $exp = strtotime($user['email_code_exp']);
    $now = time();
    if ($exp<$now){
        echo errCompose(ERR_EMAIL_CODE_EXPIRED);
        return;
    }

    //3. check code
    if ($code!=$user['email_code']){
        echo errCompose(ERR_EMAIL_CODE_INVALID);
        return;
    }

    //code correct
    $userId = $user['user_id'];

    //rubah state
    $r = ModelUser::updateEmailState($userId, 2);

    if ($r instanceof PDOException){
        echo errCompose($r);
        return;
    }

    if ($r==0){
        echo errCompose(ERR_UNKNOWN);
        return;
    }

    http_response_code(HTTP_OK);
    echo json_encode(['status'=>$r]);
}
/**
 * @param $value int millisecond
 * @return false|string
 */
function convert(int $value){
    return date('Y-m-d H:i:s', $value);
}

function composeEmail($email, $code): string
{
    return <<<HTML
<!DOCTYPE html><html><body>
<h1>Email Verification Code</h1>

To continue with your email verification, please enter the following code:
<br><br>
<div>
Your email"<br>
<b>{$email}</b>
</div>
<br>

<div>
Verification code:<br>
<b>{$code}</b>
</div>

</body></html>
HTML;
}
