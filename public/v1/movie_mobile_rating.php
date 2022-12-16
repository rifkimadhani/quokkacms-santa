<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 11/9/2022
 * Time: 9:08 AM
 */


require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelSession.php';
require_once __DIR__ . '/model/ModelMovieRating.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json; charset=utf-8');


$sessionId = (empty($_GET['session_id']) ? '' : $_GET['session_id']);
$session = ModelSession::get($sessionId);

if (is_null($session)){
    echo errCompose(ERR_SESSIONID_NOT_FOUND);
    exit();
}

if ($session instanceof PDOException){
    echo errCompose($session);
    exit();
}

$userId = $session['user_id'];

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
    case 'toggle';
        doToggle($userId);
        break;

    case 'submit':
        doSubmit($userId);
        break;
    case 'delete':
        doDelete($userId);
        break;

    default:
        http_response_code(HTTP_NOT_FOUND);
        break;
}

exit();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * set rating atau delete rating
 *
 * @param $userId
 */
function doToggle($userId){
    $vodId = isset($_GET['vod_id']) ? $_GET['vod_id'] : 0;

    $rating = ModelMovieRating::getOne($vodId, $userId);
    if ($rating instanceof PDOException){
        echo errCompose($rating);
        return;
    }

    //submit apabila blm ada
    if (is_null($rating)) {
        doSubmit($userId);
        return;
    }

    //delete apabila sdh ada
    doDelete($userId);
}

/**
 * set rating apabila blm ada
 *
 * @param $userId
 */
function doSubmit($userId){
    require_once __DIR__ . '/model/ModelMovie.php';

    $vodId = isset($_GET['vod_id']) ? $_GET['vod_id'] : 0;
    $rating = isset($_GET['rating']) ? $_GET['rating'] : 0; //value 0 - 100

    if ($rating<0 || $rating>100){
        echo errCompose(ERR_VOD_RATING_INVALID_RATING_VALUE);
        return;
    }

    //check vodId
    $vod = ModelMovie::get($vodId);
    if ($vod instanceof PDOException){
        echo errCompose($vod);
        return;
    }

    if (is_null($vod)){
        echo errCompose(ERR_VODID_INVALID);
        return;
    }

    //check apakah rating sdh di submit sebelumnya
    $movieRating = ModelMovieRating::getOne($vodId, $userId);

    if ($movieRating instanceof PDOException){
        echo errCompose($movieRating);
        return;
    }

    //rating blm ada, maka buat rating
    if (is_null($movieRating)){

        $r = ModelMovieRating::create($vodId, $userId, $rating);

        if ($r instanceof PDOException){
            echo errCompose($r);
            return;
        }

        require_once __DIR__ . '/model/ModelMovie.php';

        $data = updateRating($vodId);
        $rating = $data['rating'];
        $count = $data['count'];
        echo json_encode(['vod_id'=>$vodId, 'action'=>'submit', 'result'=>$r, 'rating'=>$rating, 'count'=>$count]);

    } else {
        echo errCompose(ERR_VOD_RATING_ALREADY_SUBMIT);
        return;
    }

}

function doDelete($userId){
    require_once __DIR__ . '/model/ModelMovie.php';

    $vodId = isset($_GET['vod_id']) ? $_GET['vod_id'] : 0;

    $r = ModelMovieRating::delete($vodId, $userId);

    if ($r instanceof PDOException){
        echo errCompose($r);
        return;
    }

    $data = updateRating($vodId);

    $rating = $data['rating'];
    $count = $data['count'];
    echo json_encode(['vod_id'=>$vodId, 'action'=>'delete', 'result'=>$r, 'rating'=>$rating, 'count'=>$count]);
}

function updateRating($vodId){
    //hitung rating & total user
    $data = ModelMovieRating::calc($vodId);

    $count = $data['count'];
    $rating = round($data['rating'] / $count);

    //update rating ke db
    ModelMovie::updateRating($vodId, $rating, $count);

    return ['rating'=>$rating, 'count'=>$count];
}