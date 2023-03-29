<?php

require_once '../../library/Log.php';
require_once '../../library/http_errorcodes.php';

require_once '../../config/ErrorAPI.php';

require_once __DIR__ . '/../../model/ModelLivetv.php';
require_once __DIR__ . '/../../model/ModelLiveTvEpg.php';

//Log::writeLn("=====================================================================");
//Log::writeRequestUri();

ini_set('display_errors', 'on');

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

$action = isset($_GET['action']) ? $_GET['action'] : '';
//$sessionId = isset($_GET['sessionId']) ? $_GET['sessionId'] : null;

//$sesObj = new ModelSession();
//$userId = $sesObj->validate($sessionId);

switch ($action) {
    case 'get_list':
        doGetList();
        break;

    case 'parse_xml':
        doParseXml();
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();


function doGetList(){
    $livetvId= isset($_GET['livetv_id']) ? $_GET['livetv_id'] : 0;

    //apabila livetv id tdk di tentukan maka ambil semua schedule
    if ($livetvId==0)
    {
        doGetAllList();
        return;
    }

    $list = ModelLiveTv::getActive($livetvId);

    //seharusnya ini dynamic sesuai dgn tgl hari ini
    $now = '2020-02-01';

    foreach ($list as &$item){
        unset($item['status']);
        unset($item['url_stream1']);
        unset($item['countryId']);

        $livetvId = $item['livetv_id'];

        $item['schedules'] = ModelLiveTvEpg::getList($livetvId, $now);
    }

    echo json_encode(['count'=>count($list), 'list'=>$list]);
}

function doGetAllList(){


    $list = ModelLiveTv::getAllActive();

    //langsung exit apabila ada PDOException pada list
    exitOnPdoException($list);

    //seharusnya ini dynamic sesuai dgn tgl hari ini
    $now = '2020-02-01';

    foreach ($list as &$item){
        unset($item['status']);
        unset($item['url_stream1']);
        unset($item['countryId']);

        $livetvId = $item['livetv_id'];
        $schedules= ModelLiveTvEpg::getList($livetvId, $now);

        $item['schedules'] = $schedules;
    }

    echo json_encode(['count'=>count($list), 'list'=>$list]);
}

/**
 * function ini hanya utk test saja, bukan utk real application
 *
 * parse xml dari topas yg kemudian di simpan ke db
 * nama channel akan di cari pada db, apabila blm exist maka tdk bisa di save
 * 
 */
function doParseXml(){

    require_once '../../model/ModelLiveTv.php';
    require_once '../../model/ModelLiveTvEpg.php';


    $xmlString = $_POST['xml'];
    $xml = simplexml_load_string($xmlString);

    //parse xml
    $epg = parseXmlFrom_novel_supertv_version_2($xml);

    $channelName = $epg['channelName'];

//    var_dump($channelName);

    //cari live tv bedasarkan nama channel
    $livetv = ModelLiveTv::getByName($channelName);

    //exit apabila tdk ada
    if ($livetv==null){
        echo errCompose(ERR_LIVETV_INVALID_NAME);
        exit();
    }

    exitOnPdoException($livetv); //exit apabila ada error pdo exception

    $livetvId = $livetv['livetvId'];

    foreach ($epg['events'] as $item){


        $name = $item['name'];
        $duration = $item['duration'];
        $sinopsis = $item['sinopsis'];

        $startDate = $item['startDate']->format('Y-m-d H:i:s');;

        //create interval utk endDate
        $interval = new DateInterval("PT{$duration}S");

        //add duration ke startDate
        $item['startDate']->add($interval);
        $endDate = $item['startDate']->format('Y-m-d H:i:s');

        $id = ModelLiveTvEpg::insertOrUpdate($livetvId, $startDate, $endDate, $duration, $name, $sinopsis);
        if ($id<0){
            echo errCompose(ERR_LIVETV_EPG_SAVE_ERROR);
            exit();
        }
    }

    echo json_encode(['success'=>1]);
}

/**
 * This will parse xml from TOPASTV
 *
 * @param $xml
 * @return array
 */
function parseXmlFrom_novel_supertv_version_2($xml){

    //nama tv
    $channelName = (string) $xml->SchedulerData->Channel->ChannelText->ChannelName;
//    var_dump($channelName);

    //prepare array to hold events
    $ar = array();

    $channel = $xml->SchedulerData->Channel->children();
    foreach ($channel as $event){

        if ($event->getName()==='Event'){
            $attributes = $event->attributes();
            $beginTime = DateTime::createFromFormat('YmdGis', (string) $attributes['begintime']);
            $duration = convertDuration((string) $attributes['duration']);

//            var_dump(['beginTime'=>$beginTime, 'duration'=>$duration]);

            $name = (string) $event->EventText->Name;
            $shortDesc = (string) $event->EventText->ShortDescription;
//            $extendedDescription = (string) $event->EventText->ExtendedDescription;

            $item = [ 'name'=>$name, 'sinopsis'=>$shortDesc, 'startDate'=>$beginTime, 'duration'=>$duration ];
            $ar[] = $item; //add item to ar
        }
    }

    return [ 'channelName'=>$channelName, 'events'=>$ar ];
}

/**
 * convert duration dari format HHMMSS --> seconds
 * @param string $duration
 * @return int
 */
function convertDuration(string $duration) : int {

    $pattern='/^(\d\d)(\d\d)(\d\d)$/m';
    if (preg_match($pattern, $duration, $match)){

        $hour = ((int) $match[1]) * 60 * 60;
        $minute = ((int) $match[2]) * 60;
        $second = (int) $match[3];

        return $hour + $minute + $second;
    }

    return 0;
}