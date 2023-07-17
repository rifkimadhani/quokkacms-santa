<?php

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../library/http_errorcodes.php';

$errMessage = array();

define('ERR_MAC_ADDRESS_IS_EMPTY', 1000);
$errMessage[ERR_MAC_ADDRESS_IS_EMPTY] = 'MAC address is empty';

define('ERR_MAC_ADDRESS_NOT_FOUND', 1010);
$errMessage[ERR_MAC_ADDRESS_NOT_FOUND] = 'MAC address not found';

define('ERR_SESSIONID_NOT_FOUND', 1012);
$errMessage[ERR_SESSIONID_NOT_FOUND] = 'SessionId not found';

define('ERR_STBID_NOT_VALID', 1014);
$errMessage[ERR_STBID_NOT_VALID] = 'StbId not valid';

define('ERR_STB_HAS_NO_ROOM', 1016);
$errMessage[ERR_STB_HAS_NO_ROOM] = 'This STB is not associated with any room';

define('ERR_THEME_NOT_DEFINED', 1020);
$errMessage[ERR_THEME_NOT_DEFINED] = 'Theme not defined';

define('ERR_PACKAGE_NOT_DEFINED', 1030);
$errMessage[ERR_PACKAGE_NOT_DEFINED] = 'LiveTV package not defined';

define('ERR_SUBSCRIBER_NOT_DEFINED', 1040); //room tdk ada subscriber nya
$errMessage[ERR_SUBSCRIBER_NOT_DEFINED] = 'Subscriber not defined';

define('ERR_INVALID_SECURITY_PIN', 1050); //room tdk ada subscriber nya
$errMessage[ERR_INVALID_SECURITY_PIN] = 'Invalid security pin';

define('ERR_NO_ORDER', 1060); //room tdk ada subscriber nya
$errMessage[ERR_NO_ORDER] = 'You don\'t have any order';

define('ERR_INVALID_ORDERCODE', 1070); //order code tdk valid utk subscriber dan room
$errMessage[ERR_INVALID_ORDERCODE] = 'No such ordercode for subscriber and room';

//////////////////////////////////////////////////
//ACCOUNT utk user mobile
define('ERR_USER_NOT_FOUND', 1080);
$errMessage[ERR_USER_NOT_FOUND] = 'User not found';

define('ERR_USER_ISBLOCK', 1082); //user mobile di block
$errMessage[ERR_USER_ISBLOCK] = 'User is block';

define('ERR_PASSWORD_INVALID_OR_ACC_NOT_FOUND', 1084);
$errMessage[ERR_PASSWORD_INVALID_OR_ACC_NOT_FOUND] = 'Password tdk valid atau acc tdk ada';

//////////////////////////////////////////////////

define('ERR_KARAOKE_NOT_RENT', 1100);
$errMessage[ERR_KARAOKE_NOT_RENT] = 'Karaoke is not rent for this room';

define('ERR_INVALID_KARAOKE_MARKETING_ID', 1110);
$errMessage[ERR_INVALID_KARAOKE_MARKETING_ID] = 'Invalid karaoke marketingId';

//SHOP
define('ERR_SHOP_PRODUCT_NOT_DEFINED', 1120);
$errMessage[ERR_SHOP_PRODUCT_NOT_DEFINED] = 'Product not defined';


define('ERR_MOVIE_ID_NOT_VALID', 2000);
$errMessage[ERR_MOVIE_ID_NOT_VALID] = 'MovieId not valid';
define('ERR_MOVIE_ID_NOT_FOR_FREE', 2001);
$errMessage[ERR_MOVIE_ID_NOT_FOR_FREE] = 'MovieId not for free';

define('ERR_MAC_ADDRESS_DUPLICATE', 2012);
$errMessage[ERR_MAC_ADDRESS_DUPLICATE] = 'MAC address duplicate';

define('ERR_CREATE_STB_SESSION_FAIL', 2020);
$errMessage[ERR_CREATE_STB_SESSION_FAIL] = 'Create STB session fail';

/////////////////////////////////////////////////////////
//MSISDN
define('ERR_MSISDN_NOT_VERIFIED', 2030);
$errMessage[ERR_MSISDN_NOT_VERIFIED] = 'MSISDN not verified';

define('ERR_MSISDN_VERIFIED', 2031);
$errMessage[ERR_MSISDN_VERIFIED] = 'MSISDN is verified';

define('ERR_MSISDN_MUST_NOT_EMPTY', 2032);
$errMessage[ERR_MSISDN_MUST_NOT_EMPTY] = 'MSISDN must not empty';

define('ERR_MSISDN_ALREADY_EXIST', 2033);
$errMessage[ERR_MSISDN_ALREADY_EXIST] = 'MSISDN already exist';

define('ERR_MSISDN_REQ_CODE_TOO_FAST', 2034);
$errMessage[ERR_MSISDN_REQ_CODE_TOO_FAST] = 'MSISDN request code too fast';

define('ERR_MSISDN_CODE_EXPIRED', 2035);
$errMessage[ERR_MSISDN_CODE_EXPIRED] = 'MSISDN code expired';

define('ERR_MSISDN_CODE_INVALID', 2036);
$errMessage[ERR_MSISDN_CODE_INVALID] = 'MSISDN code invalid';

//////////////////////////////////////////////////////////////////////////////////////
// EMAIL
define('ERR_EMAIL_NOT_VERIFIED', 2040);
$errMessage[ERR_EMAIL_NOT_VERIFIED] = 'EMAIL not verified';

define('ERR_EMAIL_VERIFIED', 2041);
$errMessage[ERR_EMAIL_VERIFIED] = 'Email is verified';

define('ERR_EMAIL_MUST_NOT_EMPTY', 2042);
$errMessage[ERR_EMAIL_MUST_NOT_EMPTY] = 'Email must not empty';

define('ERR_EMAIL_REQ_CODE_TOO_FAST', 2044);
$errMessage[ERR_EMAIL_REQ_CODE_TOO_FAST] = 'Email request code too fast';

define('ERR_EMAIL_CODE_EXPIRED', 2045);
$errMessage[ERR_EMAIL_CODE_EXPIRED] = 'Email code expired';

define('ERR_EMAIL_CODE_INVALID', 2046);
$errMessage[ERR_EMAIL_CODE_INVALID] = 'Email code invalid';

//////////////////////////////////////////////////////////////////////////////////////
// FACEBOOK
define('ERR_FACEBOOK_TOKEN_INVALID', 2050);
$errMessage[ERR_FACEBOOK_TOKEN_INVALID] = 'Facebook token not valid';

//////////////////////////////////////////////////////////////////////////////////////
// GOOGLE
define('ERR_GOOGLE_TOKEN_INVALID', 2060);
$errMessage[ERR_GOOGLE_TOKEN_INVALID] = 'Google token not valid';

//////////////////////////////////////////////////////////////////////////////////////
// VOD
define('ERR_VODID_INVALID', 3010);
$errMessage[ERR_VODID_INVALID] = 'vod id not valid';

//////////////////////////////////////////////////////////////////////////////////////
// VOD RATING
define('ERR_VOD_RATING_INVALID_RATING_VALUE', 3020);
$errMessage[ERR_VOD_RATING_INVALID_RATING_VALUE] = 'Rating value not valid';

define('ERR_VOD_RATING_ALREADY_SUBMIT', 3021);
$errMessage[ERR_VOD_RATING_ALREADY_SUBMIT] = 'Rating already submit';

//////////////////////////////////////////////////////////////////////////////////////
// TASK Hotel Service & Room service

define('ERR_HOTELSERVICE_TASK_ALREADY_COMPLETE', 3030);
$errMessage[ERR_HOTELSERVICE_TASK_ALREADY_COMPLETE] = 'Task already complete'; // FINISH / CANCEL / CANCEL_BY_SYSTE<
define('ERR_ROOMSERVICE_TASK_ALREADY_COMPLETE', 3031);
$errMessage[ERR_ROOMSERVICE_TASK_ALREADY_COMPLETE] = 'Task already complete'; // FINISH / CANCEL / CANCEL_BY_SYSTE<

//////////////////////////////////////////////////////////////////////////////////////

define('ERR_UNAUTHORIZED_ENTRY', 9900);
$errMessage[ERR_UNAUTHORIZED_ENTRY] = 'Unauthorized entry';

define('ERR_FAIL_GET_LATEST_APP', 9910);
$errMessage[ERR_FAIL_GET_LATEST_APP] = 'Unable to get latest app from table tapp';

define('ERR_APPLICATION_ID_NOTFOUND', 9912);
$errMessage[ERR_APPLICATION_ID_NOTFOUND] = 'Application Id not found';


define('ERR_INVALID_SIGNATURE', 9990);
$errMessage[ERR_INVALID_SIGNATURE] = 'Invalid signature';


define('ERR_STAT_DEF_NOT_FOUND', 90000);
$errMessage[ERR_STAT_DEF_NOT_FOUND] = 'Statistic definition not found';


define('ERR_PDO', 9000000); //message akan di ambil dari object pdo
define('ERR_UNKNOWN', 9999999); //message akan di ambil dari object pdo
$errMessage[ERR_UNKNOWN] = 'Unknown error';

function errCompose($errCode, $moreMsg = ''){

	global $errMessage;

	if ($errCode instanceof PDOException){
        http_response_code(HTTP_INTERNAL_ERROR);
		$root = [ 'errCode'=>ERR_PDO, 'errMsg'=>$errCode->getMessage() ];
		Log::error(ERR_PDO, $errCode->getMessage());
	} else {
        http_response_code(HTTP_BAD_REQUEST);
        $msg = $errMessage[$errCode] . $moreMsg;
		$root = [ 'errCode'=>$errCode, 'errMsg'=>$msg ];
		Log::error($errCode, $msg);
	}

	return json_encode($root);
}

function exitOnPdoException($pdo){

    if ($pdo instanceof PDOException){
        header('Content-type: application/json; charset=utf-8');
        $root = [ 'errCode'=>ERR_PDO, 'errMsg'=>$pdo->getMessage() ];
        echo json_encode($root);
        exit();
    }
}
