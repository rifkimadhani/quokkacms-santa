<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 4/16/2019
 * Time: 9:41 AM
 */

//moved to ModelRoomservice.php
//
//const PAYMENT_TYPE_BILL_TO_ROOM = 'BILL-TO-ROOM';

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelStbCredential.php';

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get_order_list': //list order yg blm checkout
		doGetList($stbId);
		break;
	case 'set_order':
		doSetOrder($stbId);
		break;
	case 'remove_order':
		doRemoveOrder($stbId);
		break;
	case 'clear_order':
		doClearOrder($stbId);
		break;
	case 'checkout':
		doCheckout($stbId);
		break;
	case 'get_purchased_list': //pesanan yg sudh checkout
		doGetPurchasedList($stbId);
		break;
}

die();

function doGetList($stbId){
	require_once 'model/ModelStb.php';
	require_once 'model/ModelSetting.php';
	require_once 'model/ModelRoomservice.php';

	$menuId = (empty($_GET['menuId']) ? '' : $_GET['menuId']);
	$qty = (empty($_GET['qty']) ? '' : $_GET['qty']);

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
	$kitchenId = ModelSetting::getDefaultKitchenId();

	$list = ModelRoomservice::getList($subscriberId, $roomId, $kitchenId);

	echo json_encode( [ 'count'=>sizeof($list), 'list'=>$list] );
}

/**
 * utk add order ataupun rubah qty order
 *
 * @param $stbId
 */
function doSetOrder($stbId){
	require_once 'model/ModelStb.php';
	require_once 'model/ModelSetting.php';
	require_once 'model/ModelRoomservice.php';

	$menuId = (empty($_GET['menuId']) ? 0 : $_GET['menuId']);
	$qty = (empty($_GET['qty']) ? 0 : $_GET['qty']);

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

	$r = ModelRoomservice::addOrUpdateOrder($subscriberId, $roomId, $menuId, $qty);

	if ($r instanceof PDOException){
		echo errCompose($r);
		die();
	}

	echo json_encode(['result'=>$r]);
}

function doRemoveOrder($stbId){
	require_once 'model/ModelStb.php';
	require_once 'model/ModelSetting.php';
	require_once 'model/ModelRoomservice.php';

	$menuId = (empty($_GET['menuId']) ? 0 : $_GET['menuId']);
	$qty = (empty($_GET['qty']) ? 0 : $_GET['qty']);

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

	$r = ModelRoomservice::removeOrder($subscriberId, $roomId, $menuId);

	if ($r instanceof PDOException){
		echo errCompose($r);
		die();
	}

	echo json_encode( ['result'=>$r] );
}

function doCheckout($stbId){
	$secPin = (empty($_GET['secPin']) ? '' : $_GET['secPin']);

	require_once 'model/ModelStb.php';
	require_once 'model/ModelSetting.php';
	require_once 'model/ModelRoomservice.php';
	require_once 'model/ModelKitchen.php';

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

	$pin = $room['security_pin'];

	if ($pin!=$secPin){
		echo errCompose(ERR_INVALID_SECURITY_PIN);
		die();
	}

	$subscriberId = $room['subscriber_id'];
	if (is_null($subscriberId)){
		echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
		die();
	}

	$roomId = $room['room_id'];

	$kitchenId = ModelSetting::getDefaultKitchenId();

	$kitchen = ModelKitchen::get($kitchenId);
	$percentServiceCharge = $kitchen['percent_service_charge'];
	$percentTax = $kitchen['percent_tax'];
	$deliveryFee = $kitchen['delivery_fee'];
	$currency = $kitchen['currency'];
	$currencySign = $kitchen['currency_sign'];
	$kitchenName = $kitchen['name'];
	$paymentType = PAYMENT_TYPE_BILL_TO_ROOM;

	$r = ModelRoomservice::create($subscriberId, $roomId, $kitchenId, $percentServiceCharge, $percentTax, $deliveryFee, $kitchenName, $paymentType, $currency, $currencySign);

	//apabila null, artinya tdk ada order
	//
	if (is_null($r)){
		echo errCompose(ERR_NO_ORDER);
		die();
	}
	if ($r instanceof PDOException){
		echo errCompose($r);
		die();
	}

	echo json_encode( ['order_code'=>$r] );
}

function doGetPurchasedList($stbId){
	require_once 'model/ModelRoomservice.php';

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
	$roomId = $room['room_id'];

	$list = ModelRoomservice::getPurchasedList($subscriberId, $roomId);

	foreach($list as &$item){
		$amountPayable = $item['purchase_amount'] + $item['service_charge'] + $item['tax'] + $item['delivery_fee'];

		$orderCode = $item['order_code'];
		$details = ModelRoomservice::getPurchasedDetail($orderCode);
		$item['items'] = $details;
		$item['amount_payable'] = $amountPayable;
	}

	echo json_encode( ['count'=>count($list), 'list'=>$list]  );
}

function doClearOrder($stbId){
    require_once 'model/ModelStb.php';
    require_once 'model/ModelSetting.php';
    require_once 'model/ModelRoomservice.php';

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

    $r = ModelRoomservice::clearOrder($subscriberId, $roomId);

    if ($r instanceof PDOException){
        echo errCompose($r);
        die();
    }

    echo json_encode( ['result'=>$r] );
}
