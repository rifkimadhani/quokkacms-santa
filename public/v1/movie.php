<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/5/2019
 * Time: 10:51 AM
 */

const RENT_TYPE_PAYPERVIEW = 'PAYPERVIEW';
const RENT_TYPE_FREE = 'FREE';

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelStbCredential.php';

//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

//$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get_genre_list':
		doGetGenreList();
		break;
	case 'get_list':
		doGetList();
		break;
	case 'get_info':
		doGetInfo();
		break;
	case 'purchase_free':
		doPurchaseFree();
		break;
    case 'purchase_one':
        doPurchaseOne($stbId);
        break;
    case 'check_rent':
        doCheckRent($stbId);
        break;
    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function doGetList(){
	require_once '../config/Const.php';
	require_once 'model/ModelStb.php';
	require_once 'model/ModelMovie.php';
	require_once 'model/ModelSetting.php';

	$genreId = (int) (empty($_GET['genre_id']) ? 0 : $_GET['genre_id']);
	$offset = (int) (empty($_GET['offset']) ? 0 : $_GET['offset']);
	$limit = (int) (empty($_GET['limit']) ? LIMIT_RETURN  : $_GET['limit']);
    $query = empty($_GET['keyword']) ? '' : $_GET['keyword'];

    if (empty($query)){
        if ($genreId==0){
            //list by genre
            $list = ModelMovie::getAll($offset, $limit);
        } else {
            //list all by genreId
            $list = ModelMovie::getAllByGenre($genreId, $offset, $limit);
        }
    } else {
        if ($genreId==0){
            //search tanpa genre
            $list = ModelMovie::search($offset, $limit, $query);
        } else {
            //search dgn genre
            $list = ModelMovie::searchByGenreId($genreId, $offset, $limit, $query);
        }
    }

//	$hostImage = ModelSetting::getHostApi();
//	$hostVod = ModelSetting::getHostVod();
    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

	foreach ($list as &$item){
        unset($item['url_trailer']);
        unset($item['url_stream1']);
        unset($item['path_trailer']);
        unset($item['path_stream1']);
        unset($item['currency']);
        unset($item['currency_sign']);
        $item['url_poster'] = str_replace('{BASE-HOST}', $baseHost, $item['url_poster']);
	}

	echo json_encode(['count'=>count($list), 'list'=>$list]);
}

function doGetInfo(){
	require_once 'model/ModelStb.php';
	require_once 'model/ModelMovie.php';
	require_once 'model/ModelGenre.php';
	require_once 'model/ModelSetting.php';

	$movieId = (empty($_GET['movieId']) ? '' : $_GET['movieId']);

	$movie = ModelMovie::get($movieId);
	if (empty($movie)){
		echo errCompose(ERR_MOVIE_ID_NOT_VALID);
		die();
	}

	//hapus url stream, field ini akan muncul saat purchase
	unset($movie['url_stream1']);

	$hostVod = ModelSetting::getHostVod();
	$hostImage = ModelSetting::getHostApi();
    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

    //get genre utk movie ini
	$genreList = ModelGenre::getGenre($movieId);
	$movie['genre_list'] = $genreList;
	$movie['url_trailer'] = str_replace('{HOST-VOD}', $hostVod, $movie['url_trailer']);
	$movie['url_trailer'] = str_replace('{BASE-HOST}', $baseHost, $movie['url_trailer']);
	$movie['url_poster'] = str_replace('{HOST-IMAGE}', $hostImage, $movie['url_poster']);
	$movie['url_poster'] = str_replace('{BASE-HOST}', $baseHost, $movie['url_poster']);

	echo json_encode($movie);
}

function doGetGenreList(){
	require_once 'model/ModelGenre.php';

	$list = ModelGenre::getAll();

	echo json_encode( ['count'=>count($list), 'list'=>$list] );
}

function doPurchaseOne($stbId){
	require_once 'model/ModelRoom.php';
	require_once 'model/ModelMovie.php';
	require_once 'model/ModelSetting.php';
	require_once '../library/DateUtil.php';

	$pin = (empty($_GET['pin']) ? '' : $_GET['pin']);
	$vodId = (empty($_GET['vod_id']) ? 0 : $_GET['vod_id']);

	$room = ModelRoom::getRoomByStbId($stbId);

	if (is_null($room)){
		echo errCompose(ERR_STBID_NOT_VALID);
		die();
	}

	//check apakah room ini ada subscribernya ?
	$subscriberId = $room['subscriber_id'];

	//err if tdk ada subscriber
	if (is_null($subscriberId)){
		echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
		die();
	}

	//check pin
	$pinCurrent = $room['security_pin'];

	//err if pin salah
	if ($pinCurrent!==$pin){
		echo errCompose(ERR_INVALID_SECURITY_PIN);
		die();
	}

	//check apakah movie exist??
	$movie = ModelMovie::get($vodId);

	if (is_null($movie)){
		echo errCompose(ERR_MOVIE_ID_NOT_VALID);
		die();
	}

	$roomId = $room['room_id'];
	$title = $movie['title'];
	$rentDuration = $movie['rent_duration'];
	$expDate = DateUtil::getDateStrByHour($rentDuration);
	$amount = $movie['price'];
	$currency = $movie['currency'];
	$currencySign = $movie['currency_sign'];
	$percentTax = ModelSetting::getTaxMovie();
	$tax = $amount * $percentTax/100;
	$rentType = RENT_TYPE_PAYPERVIEW;

	//lakukan pembelian
	$r = ModelMovie::purchase($roomId, $subscriberId, $vodId, $title, $rentDuration, $expDate, $amount, $currency, $currencySign, $percentTax, $tax, $rentType);

	if ($r instanceof PDOException){
		echo errCompose($r);
		die();
	}

	if ($r==0){
		echo errCompose(ERR_UNKNOWN);
		die();
	}

	$hostVod = ModelSetting::getHostVod();
    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

	//apabila pembelian berhasil maka return url dari playback

	$urlStream = str_replace('{HOST-VOD}', $hostVod, $movie['url_stream1']);
	$urlStream = str_replace('{BASE-HOST}', $baseHost, $movie['url_stream1']);

	echo json_encode([ 'vod_id'=>$vodId, 'exp_date'=>$expDate, 'url_stream_full_movie'=>$urlStream, 'rent_type'=>$rentType ]);
}

function doPurchaseFree(){
    require_once 'model/ModelMovie.php';
    require_once 'model/ModelSetting.php';

    $vodId = (empty($_GET['vod_id']) ? 0 : $_GET['vod_id']);

    $movie = ModelMovie::getFree($vodId);

    if ($movie instanceof PDOException){
        echo errCompose($movie);
        exit();
    }

    if (is_null($movie)){
        echo errCompose(ERR_MOVIE_ID_NOT_FOR_FREE);
        exit();
    }

    $hostVod = ModelSetting::getHostVod();
    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini

    $urlStream = str_replace('{HOST-VOD}', $hostVod, $movie['url_stream1']);
    $urlStream = str_replace('{BASE-HOST}', $baseHost, $urlStream);

    $expDate = '9999-12-31 23:59:59';
    $rentType = RENT_TYPE_FREE;

    echo json_encode([ 'vod_id'=>$vodId, 'exp_date'=>$expDate, 'url_stream_full_movie'=>$urlStream, 'rent_type'=>$rentType ]);
}

function doCheckRent($stbId){

    require_once 'model/ModelRoom.php';
    require_once 'model/ModelSubscriber.php';
    require_once 'model/ModelMovie.php';
    require_once 'model/ModelSetting.php';

    $vodId= (empty($_GET['vod_id']) ? 0 : $_GET['vod_id']);

    $room = ModelRoom::getRoomByStbId($stbId);

    if (is_null($room)){
        echo errCompose(ERR_STBID_NOT_VALID);
        exit();
    }

    //check apakah room ini ada subscribernya ?
    $subscriberId = $room['subscriber_id'];

    //err if tdk ada subscriber
    if (is_null($subscriberId)){
        echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
        exit();
    }

    $rent = ModelSubscriber::getRent($subscriberId, $vodId);

    $url = null;
    if (is_null($rent)==false){
        //check apakah movie exist??
        $movie = ModelMovie::get($vodId);

        $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini
        $url = str_replace('{BASE-HOST}', $baseHost, $movie['url_stream1']);
    }

    echo json_encode([ 'rent'=>$rent, 'url_stream_full_movie'=>$url ]);
}


