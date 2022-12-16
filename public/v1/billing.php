<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 4/22/2019
 * Time: 2:47 PM
 */

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelStbCredential.php';

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get_list':
		doGetList($stbId);
		break;
	case 'get_detail_roomservice':
		doGetDetailRoomservice($stbId);
		break;
}

die();


function doGetList($stbId){
	require_once 'model/ModelStb.php';
	require_once 'model/ModelRoomservice.php';
	require_once 'model/ModelSubscriber.php';

	//ambil room
	$room = ModelStb::get($stbId);
	if (is_null($room)){
		echo errCompose(ERR_STB_HAS_NO_ROOM);
		die();
	}
	if ($room instanceof PDOException){
		echo errCompose($room);
		die();
	}

	$subscriberId = $room['subscriber_id'];
	if (is_null($subscriberId)){
		echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
		die();
	}

	$roomId = $room['room_id'];

	$totalRs = 0;
	$listRs = ModelRoomservice::getBill($subscriberId, $roomId);
	foreach ($listRs as $item){
		$totalRs += $item['purchase_amount'] + $item['tax']  + $item['service_charge']  + $item['delivery_fee'];
	}

	$totalTv = 0;
	$listTv = ModelSubscriber::getTvBill($subscriberId, $roomId);
	foreach ($listTv as $item){
		$totalTv += $item['purchase_amount'] + $item['tax'];
	}

	$totalKaraoke = 0;
	$listKaraoke = ModelSubscriber::getKaraokeBill($subscriberId, $roomId);
	foreach ($listKaraoke as $item){
		$totalKaraoke += $item['purchase_amount'] + $item['tax'];
	}

	$totalVod = 0;
	$listVod = ModelSubscriber::getVodBill($subscriberId, $roomId);
	foreach ($listVod as $item){
		$totalVod += $item['purchase_amount'] + $item['tax'];
	}

	$totalPms = 0;
	$listPms = ModelSubscriber::getPmsBill($subscriberId, $roomId);
	foreach ($listPms as $item){
        $totalPms += $item['amount'];
	}

	echo json_encode( ['total_roomservice'=>$totalRs, 'total_tv'=>$totalTv, 'total_karaoke'=>$totalKaraoke,'total_vod'=>$totalVod, 'total_pms'=>$totalPms, 'bill_roomservice'=>$listRs, 'bill_tv'=>$listTv, 'bill_karaoke'=>$listKaraoke, 'bill_vod'=>$listVod, 'bill_pms'=>$listPms] );
}

function doGetDetailRoomservice($stbId){
	require_once 'model/ModelStb.php';
	require_once 'model/ModelRoomservice.php';
	require_once 'model/ModelSubscriber.php';

	$orderCode = (empty($_GET['orderCode']) ? '' : $_GET['orderCode']); //dipakai utk roomservice

	//ambil room
	$room = ModelStb::get($stbId);
	if (is_null($room)){
		echo errCompose(ERR_STB_HAS_NO_ROOM);
		die();
	}
	if ($room instanceof PDOException){
		echo errCompose($room);
		die();
	}

	$subscriberId = $room['subscriber_id'];
	if (is_null($subscriberId)){
		echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
		die();
	}

	$roomId = $room['room_id'];

	$roomservice = ModelRoomservice::get($subscriberId, $roomId, $orderCode);

	if (is_null($roomservice)) {
		echo errCompose(ERR_INVALID_ORDERCODE);
		die();
	}

	$detail = ModelRoomservice::getPurchasedDetail($orderCode);

	echo json_encode( ['count'=>count($detail), 'list'=>$detail] );
}