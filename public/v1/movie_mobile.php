<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 5/10/2023
 * Time: 8:53 AM
 */

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../model/ModelStbCredential.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');

//TODO: session Id di matikan dulu, nantinya akan mempergukana session pada tuser_session
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
//    case 'purchase_one':
//        doPurchaseOne($stbId);
//        break;
//    case 'check_rent':
//        doCheckRent($stbId);
//        break;
    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function doGetList(){
    require_once '../../config/Const.php';
    require_once '../../model/ModelStb.php';
    require_once '../../model/ModelMovie.php';
    require_once '../../model/ModelSetting.php';

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
//        unset($item['url_trailer']);
//        unset($item['url_stream1']);
        unset($item['path_trailer']);
        unset($item['path_stream1']);
        unset($item['currency']);
        unset($item['currency_sign']);
        $item['url_poster'] = str_replace('{BASE-HOST}', $baseHost, $item['url_poster']);
    }

    echo json_encode(['count'=>count($list), 'list'=>$list]);
}

function doGetInfo(){
    require_once '../../model/ModelStb.php';
    require_once '../../model/ModelMovie.php';
    require_once '../../model/ModelGenre.php';
    require_once '../../model/ModelSetting.php';

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
    require_once '../../model/ModelGenre.php';

    $list = ModelGenre::getAll();

    echo json_encode( ['count'=>count($list), 'list'=>$list] );
}

function doPurchaseFree(){
    require_once '../../model/ModelMovie.php';
    require_once '../../model/ModelSetting.php';

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



