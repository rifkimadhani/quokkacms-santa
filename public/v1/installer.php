<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/27/2019
 * Time: 11:53 AM
 */


/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/19/2019
 * Time: 10:13 AM
 */

/**
 * Installer dipakai utk melakukan installasi pada stb,
 * tdk dibutuhkan auth stb,
 * auth di lakukan dgn mempergunakan username & password spt login di console
 */

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../model/ModelStbCredential.php';

Log::writeLn('==========================================================================================================');
Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'login':
		doLogin();
		break;
	case 'register_macaddress':
		doRegisterMacaddress();
		break;
	case 'create_session':
		doCreateSession();
		break;
	case 'get_stb_list':
		doGetStbList();
		break;

	case 'install':
		doInstall();
		break;
}

die();

function doLogin(){
	require_once '../../model/ModelAdmin.php';
	require_once '../../library/Security.php';

	$username = (empty($_GET['username']) ? '' : $_GET['username']);
	$hash = (empty($_GET['hash']) ? '' : $_GET['hash']);

	//check login
	$user = ModelAdmin::checkLogin($username, $hash);
	if (empty($user)){
		echo errCompose(ERR_UNAUTHORIZED_ENTRY);
		die();
	}
	if ($user instanceof PDOException){
		echo errCompose($user);
		die();
	}

	//login berhasil, user valid
	$adminId = $user['admin_id'];
	$json = $user['json'];

	$sessionId = $adminId . Security::random(24);
	$sessionId = substr($sessionId,0, 24); //cut string max 24 char

	$salt = Security::random(10);

	$r = ModelAdmin::addSession($adminId, $sessionId, $salt);
	if ($r instanceof PDOException){
		echo errCompose($r);
		die();
	}

	echo json_encode([ 'result'=>$r, 'admin_session_id'=>$sessionId, 'salt'=>$salt, 'username'=>$username, 'json'=>$json ]);
}

function doRegisterMacaddress(){
	require_once '../../model/ModelAdmin.php';
	require_once '../../model/ModelStb.php';
	require_once '../../library/Util.php';
	require_once '../../library/Security.php';

	$sessionId = (empty($_GET['sessionId']) ? '' : $_GET['sessionId']);
	$stbId = (empty($_GET['stbId']) ? '' : $_GET['stbId']);
	$sig = (empty($_GET['sig']) ? '' : $_GET['sig']);

	//get admin dari sessionId
	$admin = ModelAdmin::getAdminFromSessionId($sessionId);

	if (empty($admin)){
		echo errCompose(ERR_UNAUTHORIZED_ENTRY);
		die();
	}
	if ($admin instanceof PDOException){
		echo errCompose($admin);
		die();
	}

	//check apakah stbId valid?
	$stb = ModelStb::get($stbId);
	if (empty($stb)){
		echo errCompose(ERR_STBID_NOT_VALID);
		die();
	}

	//TODO: Check apakah role (installer) ada utk user ini ??
	//

	//check signature
	$salt = $admin['salt'];

	$checkSig = Security::genHash("{$stbId}", $salt);
	if ($checkSig<>$sig){
		echo errCompose(ERR_INVALID_SIGNATURE, " {$checkSig} <> {$sig}");

		die();
	}

	//ambil mac dari connection
	//
	$mac = Util::findMacaddress();
	if (empty($mac)){
		echo errCompose(ERR_MAC_ADDRESS_NOT_FOUND);
		die();
	}

	//Register macaddress
	$r = ModelStb::registerMacaddress($stbId, $mac);
	if ($r==0){
		echo errCompose(ERR_MAC_ADDRESS_DUPLICATE, " - {$mac}");
		die();
	}

	echo json_encode([ 'result'=>$r, 'mac'=>$mac ]);
}

function doCreateSession(){
	require_once '../../model/ModelAdmin.php';
	require_once '../../model/ModelStb.php';
	require_once '../../library/Util.php';
	require_once '../../library/Security.php';

	$sessionId = (empty($_GET['sessionId']) ? '' : $_GET['sessionId']);
	$stbId = (empty($_GET['stbId']) ? '' : $_GET['stbId']);
	$sig = (empty($_GET['sig']) ? '' : $_GET['sig']);


	//get admin dari sessionId
	$admin = ModelAdmin::getAdminFromSessionId($sessionId);

	if (empty($admin)){
		echo errCompose(ERR_UNAUTHORIZED_ENTRY);
		die();
	}
	if ($admin instanceof PDOException){
		echo errCompose($admin);
		die();
	}

	//check apakah stbId valid?
	$stb = ModelStb::get($stbId);
	if (empty($stb)){
		echo errCompose(ERR_STBID_NOT_VALID);
		die();
	}

	//TODO: Check apakah role (installer) ada utk user ini ??
	//

	//check signature
	$salt = $admin['salt'];
	$checkSig = Security::genHash("{$stbId}", $salt);
	if ($checkSig<>$sig){
		echo errCompose(ERR_INVALID_SIGNATURE, " {$checkSig} <> {$sig}");
		die();
	}

	$stbSession = substr($stbId . Security::random(24),0,24);

	$r = ModelStb::createStbSession($stbId, $stbSession);
	if ($r==0){
		echo errCompose(ERR_CREATE_STB_SESSION_FAIL);
		die();
	}

	echo json_encode([ 'result'=>$r, 'stb_session_id'=>$stbSession ]);
}

function doGetStbList(){
	require_once '../../library/Security.php';
	require_once '../../model/ModelAdmin.php';
	require_once '../../model/ModelStb.php';

	$sessionId = (empty($_GET['sessionId']) ? '' : $_GET['sessionId']);
//	$sig = (empty($_GET['sig']) ? '' : $_GET['sig']);
	$keyword = (empty($_GET['keyword']) ? '' : $_GET['keyword']);
	$offset = (empty($_GET['offset']) ? 0 : $_GET['offset']);
	$limit = (empty($_GET['limit']) ? 50 : $_GET['limit']);

	//get admin dari sessionId
	$admin = ModelAdmin::getAdminFromSessionId($sessionId);

	if (empty($admin)){
		echo errCompose(ERR_UNAUTHORIZED_ENTRY);
		die();
	}
	if ($admin instanceof PDOException){
		echo errCompose($admin);
		die();
	}

	//TODO: Check apakah role (installer) ada utk user ini ??
	//

	//check signature
//	$salt = $admin['salt'];
//	$checkSig = Security::genHash("{$keyword}{$offset}", $salt);
//
//	if ($checkSig<>$sig){
//		$extra = ' ' . $checkSig . ' <> ' . $sig;
//		echo errCompose(ERR_INVALID_SIGNATURE, $extra);
//		die();
//	}

	//get list
	$list = ModelStb::getPartialByKeyword($keyword, $offset, $limit);

	echo json_encode([ 'count'=>sizeof($list), 'list'=>$list ]);
}

function doInstall(){
	require_once '../../model/ModelApp.php';

	$appId = (empty($_GET['app_id']) ? '' : $_GET['app_id']);
	$ip = (empty($_GET['ip']) ? '192.168.0.120' : $_GET['ip']);
	$stbId = (empty($_GET['stb_id']) ? '' : $_GET['stb_id']);

	ModelApp::install($appId, $ip, $stbId);

}