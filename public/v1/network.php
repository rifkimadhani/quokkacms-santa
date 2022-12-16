<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 5/29/2019
 * Time: 12:04 PM
 *
 * Send paket to stb via ip_address
 * paket bisa di kirim ke 1 stb atau ke beberapa stb
 *
 */

require_once __DIR__ . '/../library/NetworkUtil.php';

error_reporting(0);
ini_set('display_errors', false);

const PORT = 8002;

function restartApp($stbId){
    $json = json_encode( ['type'=>'restart'] );
    sendToStb($stbId, $json);
}

/**
 * Send theme update to all / subscriber
 *
 * @param $subscriberId
 */
function sendThemeUpdateToSubscriber($subscriberId){
	$json = json_encode( ['type'=>'theme'] );
	sendToSubscriber($subscriberId, $json);
}

function sendThemeUpdateToAll(){
	$json = json_encode( ['type'=>'theme'] );
	sendToAll($json);
}


/**
 * Send checkout/in notification
 * @param $subscriberId
 */
function sendCheckinToSubscriber($subscriberId){
	$json = json_encode( ['type'=>'guest_checkin'] );
	sendToSubscriber($subscriberId, $json);
}

function sendCheckoutToSubscriber($subscriberId){
	$json = json_encode( ['type'=>'guest_checkout'] );
	sendToSubscriber($subscriberId, $json);
}

/**
 * Send emergency warning to all stb
 *
 * @param $on
 */
function sendEmergencyWarningToAll($on){

	if ($on){
		$json = json_encode( ['type'=>'emergency_warning_1'] );
	}else {
		$json = json_encode( ['type'=>'emergency_warning_0'] );
	}

	sendToAll($json);
}

/**
 * Kirim tourist info update ke all
 */
function sendTouristInfoUpdateToAll(){
	$json = json_encode( ['type'=>'locality'] );
	sendToAll($json);
}

function sendServiceUpdateToAll(){
	$json = json_encode( ['type'=>'facility'] );
	sendToAll($json);
}

/**
 * Update background music ke all
 */
function sendBackgroundMusicUpdateToAll(){
	$json = json_encode( ['type'=>'background_music'] );
	sendToAll($json);
}

/**
 * Send livetv update ke subscriber / all
 *
 * @param $subscriberId
 */
function sendLivetvUpdateToSubscriber($subscriberId){
	$json = json_encode( ['type'=>'livetv'] );
	sendToSubscriber($subscriberId, $json);
}

function sendLivetvUpdateToRoom($roomId){
	$json = json_encode( ['type'=>'livetv'] );
	sendToRoom($roomId, $json);
}

function sendLivetvUpdateToAll(){
	$json = json_encode( ['type'=>'livetv'] );
	sendToAll($json);
}

/**
 * Send Advertisement update ke all
 */
function sendAdsUpdateToAll(){
	$json = json_encode( ['type'=>'ads'] );
	sendToAll($json);
}

/**
 * Send message notification
 *
 * @param $roomId
 */
function sendMessageToRoom($roomId){
	$json = json_encode( ['type'=>'message'] );
	sendToRoom($roomId, $json);
}

function sendMessageToSubscriber($subscriberId){
	$json = json_encode( ['type'=>'message'] );
	sendToSubscriber($subscriberId, $json);
}

function sendMessageToAll(){
	$json = json_encode( ['type'=>'message'] );
	sendToAll($json);
}

/**
 *  Send game update to all
 */
function sendGameUpdateToAll(){
	$json = json_encode( ['type'=>'game'] );
	sendToAll($json);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Kirim paket ke 1 stb
 *
 * @param $stbId
 * @param $json
 */
function sendToStb($stbId, $json) {

	require_once __DIR__ . '/model/ModelStb.php';

	$stb = ModelStb::get($stbId);

    $ip = $stb['ip_address'];
    $status = $stb['status'];

    //exit apabila status==0, status di ambil dari last seen (< 10 second)
    if ($status==0) return;

	if(isset($ip)){
		$r = NetworkUtil::send($ip, PORT, $json);
	}
}

function sendToRoom($roomId, $json){
	require_once 'model/ModelStb.php';

	$ar = ModelStb::getByRoom($roomId);

	set_time_limit(0);

	foreach ($ar as $item){
		$ip = $item['ip_address'];
		if(isset($ip)){
			$r = NetworkUtil::send($ip, PORT, $json);
		}
	}

	set_time_limit(30);
}

function sendToSubscriber($subscriberId, $json){
	require_once 'model/ModelSubscriber.php';

	$ar = ModelSubscriber::getStb($subscriberId);

	set_time_limit(0);

	foreach ($ar as $item){
		$ip = $item['ip_address'];
		if(isset($ip)){
			$r = NetworkUtil::send($ip, PORT, $json);
		}
	}

	set_time_limit(30);

}

function sendToAll($json){
	require_once 'model/ModelStb.php';

	$ar = ModelStb::getAll();

	set_time_limit(0);

	foreach ($ar as $item){
		$ip = $item['ip_address'];
		if(isset($ip)){
			$r = NetworkUtil::send($ip, PORT, $json);
		}
	}

	set_time_limit(30);

}