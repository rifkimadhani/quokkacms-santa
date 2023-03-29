<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 10/9/2019
 * Time: 12:54 PM
 */


require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../model/ModelStbCredential.php';

if (defined('HTTP_BAD_REQUEST')==false) define('HTTP_BAD_REQUEST', 400);
if (defined('HTTP_NOT_FOUND')==false) define('HTTP_NOT_FOUND', 404);


//Log::writeLn('==========================================================================================================');
//Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'quick':
		doQuick($stbId);
		break;
	case 'create':
		doCreate($stbId);
		break;
	case 'update':
		doUpdateValue();
		break;
    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

/**
 * utk DISCREETE / CONTINOUS
 *
 * continous tdk perlu 2x call api, cukup 1x saja.
 * unit di set 'second' maka adjusted_date akan di isikan dgn tanggal mundur verdasrkan nilai value
 *
 * statId akan selalu return 0, krn tdk di pakai lagi
 *
 * @param $stbId
 */
function doQuick($stbId){

    //langsung disconnect user dari api, tanpa menunggu script selesai
    disconnect(json_encode([ 'stat_id'=>0 ]));

//	$statDefId = (empty($_GET['stat_def_id']) ? 0 : $_GET['stat_def_id']);
    $unit = (empty($_GET['unit']) ? '' : $_GET['unit']);
    $type = (empty($_GET['type']) ? '' : $_GET['type']);
    $value = (empty($_GET['value']) ? 0 : $_GET['value']);
    $group0 = (empty($_GET['group0']) ? null : $_GET['group0']);
    $group1 = (empty($_GET['group1']) ? null : $_GET['group1']);
    $group2 = (empty($_GET['group2']) ? null : $_GET['group2']);
    $group3 = (empty($_GET['group3']) ? null : $_GET['group3']);
    $group4 = (empty($_GET['group4']) ? null : $_GET['group4']);

    require_once '../../config/ErrorAPI.php';
    require_once '../../model/ModelStat.php';
    require_once '../../model/ModelStb.php';

    //cari subscriberId utk stb ini
    $subscriberId = 0;
    $stb = ModelStb::get($stbId);
    if (isset($stb)){
        $subscriberId = $stb['subscriber_id'];
        if (is_null($subscriberId)) $subscriberId = 0;
    }

    if (strtolower($unit)=='second'){
        //apabila unit == seond, maka adjusted_date akan di isi dgn date mundur berdasarkan dari value
        $statId = ModelStat::createContinous($stbId, $group0, $group1, $group2, $group3, $group4, $value, $unit, $subscriberId);
    } else {
        $statId = ModelStat::create($stbId, $type, $group0, $group1, $group2, $group3, $group4, $unit, $subscriberId);
    }
}

function doCreate($stbId){

	require_once '../../config/ErrorAPI.php';
	require_once '../../model/ModelStat.php';
	require_once '../../model/ModelStb.php';

//	$statDefId = (empty($_GET['stat_def_id']) ? 0 : $_GET['stat_def_id']);
	$type = (empty($_GET['type']) ? null : $_GET['type']);
    $unit = (empty($_GET['unit']) ? null : $_GET['unit']);
	$group0 = (empty($_GET['group0']) ? null : $_GET['group0']);
	$group1 = (empty($_GET['group1']) ? null : $_GET['group1']);
	$group2 = (empty($_GET['group2']) ? null : $_GET['group2']);
	$group3 = (empty($_GET['group3']) ? null : $_GET['group3']);
	$group4 = (empty($_GET['group4']) ? null : $_GET['group4']);

	//cari subscriberId utk stb ini
	$subscriberId = 0;
	$stb = ModelStb::get($stbId);
	if (isset($stb)){
		$subscriberId = $stb['subscriber_id'];
		if (is_null($subscriberId)) $subscriberId = 0;
	}

//	$type = $stat['type'];
//	$valueUnit = $stat['value_unit'];
//	$group0 = $stat['group_0'];

	$statId = ModelStat::create($stbId, $type, $group0, $group1, $group2, $group3, $group4, $unit, $subscriberId);

	if ($statId instanceof PDOException){
		echo errCompose($statId);
        http_response_code(400);
        exit();
	}

	//return stat id apabila sukses
	echo json_encode([ 'stat_id'=>$statId ]);
}

function doUpdateValue(){

	require_once '../../model/ModelStat.php';

	$statId = (empty($_GET['stat_id']) ? 0 : $_GET['stat_id']);
//	$value = (empty($_GET['value']) ? 0 : $_GET['value']);

	$r = ModelStat::updateValue($statId);

	echo json_encode([ 'success'=>$r ]);
}

function disconnect($json)
{
    // let's free the user, but continue running the
    // script in the background
    ignore_user_abort(true);
    header("Connection: close");
    ob_start();

    header("Content-Length: " . mb_strlen($json));
    header('Content-type: application/json; charset=utf-8');
    echo $json;

    ob_end_flush();
    ob_flush();
    flush();
}