<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/5/2019
 * Time: 10:14 AM
 */

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelStbCredential.php';
require_once __DIR__ . '/model/ModelStb.php';

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get_list':
		doGetList($stbId);
		break;
	case 'get_banner':
		doGetBanner();
		break;
	case 'get_channel_list':
		doGetChannelList();
		break;
	case 'purchase':
		doPurchase($stbId);
		break;
    case 'get_running_text':
        doGetRunningText();
        break;
}

die();

/**
 * Basic package Id di ambil dari 3 sumber
 *
 * 1. Room, apabil subscriber tdk ada
 * 2. Subscriber apabila di set
 * 3. Default
 *
 * tsubscriber_tvpackage di pergunakan apabila ada tvpackage yg di beli oleh room
 *
 * @param $stbId
 */
function doGetList($stbId)
{
	require_once 'model/ModelStb.php';
	require_once 'model/ModelLivetv.php';
	require_once 'model/ModelSubscriber.php';
	require_once 'model/ModelSetting.php';

	//1. Ambil room dari stbId
	//
	$room = ModelStb::get($stbId);
	if(empty($room)){
		echo errCompose(ERR_STB_HAS_NO_ROOM);
		die();
	}

	//2. Ambil subscriberId dari room
	$subscriberId = $room['subscriber_id'];

	//3. load subscriber apabila ada
	if (isset($subscriberId)){
		$subscriber = ModelSubscriber::get($subscriberId);
		if ($subscriber instanceof PDOException){
			echo errCompose($subscriber);
			die();
		}

		$packageId = $subscriber['package_id'];
	} else {
		$packageId = null;
	}

	//ambil packageId dari room apabila ada
	if (empty($packageId)){
		$packageId = $room['package_id'];
	}

	//ambil packageId dari default
	if (empty($packageId)){
		$packageId = ModelSetting::getDefaultPackageId();
	}

	//ini adalah basic packageId
	$basicPackageId = $packageId;

    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

    //apabila subscriber tdk ada maka cukup sampai di level ini
	//
	if (empty($subscriberId)){
		$list = ModelLivetv::getByPackage($basicPackageId);

		if (empty($list)){
			echo errCompose(ERR_PACKAGE_NOT_DEFINED);
			die();
		}

		$hostTv = ModelSetting::getHostTv();
		$host = ModelSetting::getHostApi();

		foreach ($list as &$item){
			$item['url_stream1'] = str_replace('{HOST-TV}', $hostTv, $item['url_stream1']);
			$item['url_station_logo'] = str_replace('{HOST}', $host, $item['url_station_logo']);

            $item['url_station_logo'] = str_replace('{BASE-HOST}', $baseHost, $item['url_station_logo']);
		}

		echo json_encode( [ 'count'=>sizeof($list), 'list'=>$list] );
		die();

	}

	//apabila ada subscriber, maka check apakah ada rent package ??
	//

	$roomId = $room['room_id'];
	$list = ModelSubscriber::getSubscriberPackageRent($subscriberId, $roomId);

	//semua package masukin ke dalam array
	$arPackage = array();
	$arPackage[] = $basicPackageId;

	foreach ($list as $item){
		//prevent packageId yg null
		if (isset($item['package_id'])){
			$arPackage [] = $item['package_id'];
		}
	}

	$list = ModelLivetv::getPackageByArray($arPackage);

	$hostTv = ModelSetting::getHostTv();

	foreach ($list as &$item){
		$item['url_stream1'] = str_replace('{HOST-TV}', $hostTv, $item['url_stream1']);
        $item['url_station_logo'] = str_replace('{BASE-HOST}', $baseHost, $item['url_station_logo']);
	}


	echo json_encode( ['count'=>sizeof($list), 'list'=>$list] );
}

/**
 * Menampilkan promo live tv package, hanya menampilkan package yg memiliki price
 *
 */
function doGetBanner(){
	require_once 'model/ModelLivetv.php';

	$list = ModelLivetv::getMarketingBanner();

	echo json_encode(['count'=>count($list), 'list'=>$list]);
}

/**
 * Untuk melihat channel apa saja yg ada di dalam package
 */
function doGetChannelList(){
	require_once 'model/ModelSetting.php';
	require_once 'model/ModelLivetv.php';

	$packageId = (empty($_GET['packageId']) ? 0 : $_GET['packageId']);

	$list = ModelLivetv::getChannelList($packageId);

	$host = ModelSetting::getHostApi();

	foreach ($list as &$item){
		$item['url_station_logo'] = str_replace('{HOST}', $host, $item['url_station_logo']);
	}

	echo json_encode(['count'=>count($list), 'list'=>$list]);
}

function doPurchase($stbId){
	require_once 'model/ModelLivetv.php';

	$packageId = (empty($_GET['packageId']) ? 0 : $_GET['packageId']);

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

	//purchase package, sebuah room harus memiliki subscriber
	if (is_null($subscriberId)){
		echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
		die();
	}

	$roomId = $room['room_id'];

	$package = ModelLivetv::getPackage($packageId);

	//exit apabila package tdk ada
	if (is_null($package)){
		echo errCompose(ERR_PACKAGE_NOT_DEFINED);
		die();
	}
	if ($package instanceof PDOException){
		echo errCompose($package);
		die();
	}

	$rentDuration = $package['rent_duration'];
	$price = $package['price'];
	$curr = $package['currency'];
	$currSign = $package['currency_sign'];
	$percentTax = $package['percent_tax'];
	$packageName = $package['name'];
	$title = 'Purchase of ' . $packageName . ' for ' . $rentDuration . ' hours';

	$r = ModelLivetv::purchase($subscriberId, $roomId, $rentDuration, $price, $curr, $currSign, $percentTax, $packageId, $packageName, $title);

	echo json_encode(['result'=>$r]);
}

function doGetRunningText(){
    require_once 'model/ModelLivetv.php';

    $name = (empty($_GET['name']) ? '' : $_GET['name']);

    $text = ModelLivetv::getRunningText($name);

    if ($text instanceof PDOException){
        $text = '';
    }

    echo json_encode(['text'=>$text]);
}