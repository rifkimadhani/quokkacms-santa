<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 4/25/2019
 * Time: 12:29 PM
 */

const LIMIT_RETURN = 50;
const JUKEBOX_GENRE_LIST = 'https://homeconnectserver.com/jukebox/v1/genre.php';
const JUKEBOX_SONG_BY_GENRE = 'https://homeconnectserver.com/jukebox/v1/song.php?type=genre&genreId=_genreId&offset=_offset&limit=_limit';
const JUKEBOX_SONG_BY_KEYWORD = 'https://homeconnectserver.com/jukebox/v1/song.php?type&keyword=_keyword&offset=_offset&limit=_limit';
const JUKEBOX_SONG_PLAYBACK_URL = 'https://homeconnectserver.com/jukebox/v1/song.php?type=playbackUrl&songId=_songId';

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelStbCredential.php';
require_once __DIR__ . '/model/ModelStb.php';

Log::writeLn('==========================================================================================================');
Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get_genre_list':
		doGetGenreList();
		break;

	case 'get_song_by_genre':
		doGetSongByGenre();
		break;

	case 'get_song_by_keyword':
		doGetSongByKeyword();
		break;

	case 'get_playback_url':
		doGetPlaybackUrl($stbId);
		break;

	case 'purchase':
		doPurchase($stbId);
		break;

	case 'get_rent_list':
		doGetRentList($stbId);
		break;

	case 'get_banner':
		doGetBanner();
		break;
}

die();

function doGetGenreList(){
	$content = file_get_contents(JUKEBOX_GENRE_LIST);

	echo $content;
}

function doGetSongByGenre(){

	//match the name with the JUKEBOX API
	$_genreId = (empty($_GET['genreId']) ? 0 : $_GET['genreId']);
	$_offset = (empty($_GET['offset']) ? 0 : $_GET['offset']);
	$_limit = (empty($_GET['limit']) ? LIMIT_RETURN : $_GET['limit']);

	$ar = compact('_genreId', '_offset', '_limit');

	$url = str_replace(array_keys($ar), $ar, JUKEBOX_SONG_BY_GENRE);
	$content = file_get_contents($url);

	echo $content;
}

function doGetSongByKeyword(){

	//match the name with the JUKEBOX API
	$_keyword = (empty($_GET['keyword']) ? '' : $_GET['keyword']);
	$_offset = (empty($_GET['offset']) ? 0 : $_GET['offset']);
	$_limit = (empty($_GET['limit']) ? LIMIT_RETURN : $_GET['limit']);
	$_keyword = urlencode($_keyword);

	$ar = compact('_keyword', '_offset', '_limit');

	$url = str_replace(array_keys($ar), $ar, JUKEBOX_SONG_BY_KEYWORD);
	$content = file_get_contents($url);

	echo $content;
}

function doGetPlaybackUrl($stbId){
	require_once 'model/ModelKaraoke.php';

	//check apakah room ini masuk dalam daftar sewa karaoke ??

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

	$rents = ModelKaraoke::getRent($subscriberId, $roomId);

	if(count($rents)==0){
		echo errCompose(ERR_KARAOKE_NOT_RENT);
		die();
	}

	$_songId = (empty($_GET['songId']) ? 0 : $_GET['songId']);

	$ar = compact('_songId');
	$url = str_replace(array_keys($ar), $ar, JUKEBOX_SONG_PLAYBACK_URL);

	$content = file_get_contents($url);
	echo $content;
}

function doPurchase($stbId){
	require_once 'model/ModelKaraoke.php';
	require_once 'model/ModelSetting.php';

	$marketingId = (empty($_GET['marketingId']) ? 0 : $_GET['marketingId']);
	$secPin = (empty($_GET['secPin']) ? '' : $_GET['secPin']);

	//ambil data dari marketing
	$marketing = ModelKaraoke::getMarketing($marketingId);
	if (is_null($marketing)){
		echo errCompose(ERR_INVALID_KARAOKE_MARKETING_ID);
		die();
	}
	if ($marketing instanceof PDOException){
		echo errCompose($marketing);
		die();
	}

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

	//check pin
	$pin = $room['security_pin'];

	if ($pin!=$secPin){
		echo errCompose(ERR_INVALID_SECURITY_PIN);
		die();
	}

	//apakah subscriber ada?
	//
	$subscriberId = $room['subscriber_id'];
	if (is_null($subscriberId)){
		echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
		die();
	}

	$roomId = $room['room_id'];

	$rentDuration = $marketing['rent_duration']; //satuan jam
	$curr = $marketing['currency'];
	$currSign = $marketing['currency_sign'];
	$price = $marketing['price'];
	$percentTax = $marketing['percent_tax'];
	$title = $marketing['title'];

	$r = ModelKaraoke::purchase($subscriberId, $roomId, $rentDuration, $curr, $currSign, $price, $percentTax, $title, $marketingId);

	echo json_encode( ['result'=>$r] );
}

function doGetRentList($stbId){
	require_once 'model/ModelKaraoke.php';

	//check apakah room ini masuk dalam daftar sewa karaoke ??

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

	$rent = ModelKaraoke::getRent($subscriberId, $roomId);

	echo json_encode(['count'=>count($rent), 'list'=>$rent]);
}

function doGetBanner(){
	require_once 'model/ModelKaraoke.php';

	$list = ModelKaraoke::getMarketingBanner();

	echo json_encode(['count'=>count($list), 'list'=>$list]);
}