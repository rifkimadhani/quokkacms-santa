<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 9/5/2019
 * Time: 12:40 PM
 *
 *
 * type
 * 1. Movie / VOD
 * 2. HOTEL SERVICE
 * 3. TOURIST INFO
 * 4. MESSAGE
 *
 *
 *
 * 1. receive file, to folder sh
 * 2. transcode file
 * 3. call back
 *
 *
 */
require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelStbCredential.php';

define('FILENAME', 'filename');
define('MAX_SIZE', 500000000);

define('STATUS_NEW', 'NEW');
define('STATUS_FINISH', 'FINISH');

define('TRANS_LOCALITY', 'TRANS-LOCALITY');
define('TRANS_VOD', 'TRANS-VOD');
define('TRANS_VOD_TRAILER', 'TRANS-VOD-TRAILER');

define('TYPE_LOCALITY', 'locality');
define('TYPE_FACILITY', 'facility');
define('TYPE_MESSAGE', 'message');
define('TYPE_ADS', 'ads');
define('TYPE_VOD', 'vod');
define('TYPE_VOD_TRAILER', 'vod_trailer');

Log::writeLn('==========================================================================================================');
Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

//$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'upload':
		doUpload();
		break;

	case 'callback':
		doCallback();
		break;
}

die();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function doUpload(){

	require_once 'model/ModelSetting.php';

	$type = empty($_GET['type']) ? '' : $_GET['type'];
	$id = (int) (empty($_GET['id']) ? 0 : $_GET['id']);

	$pathApp = ModelSetting::getPathApplication();
	$pathContent = ModelSetting::getPathContent();
	$pathContent = str_replace('{PATH-APP}', $pathApp, $pathContent);

	switch ($type){
		case TYPE_LOCALITY:
			processUpload(TRANS_LOCALITY, $type, $id, 'locality_media_id', $pathContent);
			break;
		case TYPE_FACILITY:
			break;
		case TYPE_MESSAGE:
			break;
		case TYPE_ADS:
			break;
		case TYPE_VOD:
			//khusus utk vod, beda path
			$pathContent = ModelSetting::getPathVod();
			$pathContent = str_replace('{PATH-APP}', $pathApp, $pathContent);
			processUpload(TRANS_VOD, $type, $id, 'vod_id', $pathContent);
			break;
	}

	echo 'done';
}

/**
 * @param string $type locality /
 * @param int $id id utk media yg di maksud
 * @param string $paramId nama parameter
 */
function processUpload(string $jobType, string $type, int $id, string $paramId, string $pathResult){

	require_once '../library/Security.php';
	require_once 'model/ModelSetting.php';
	require_once 'model/ModelJob.php';

	$pathSh = __DIR__ . '/../sh/';
	$path = ModelSetting::getPathUploadVideo() . '/';//__DIR__ . '/../sh/upload/';
	$filename = "{$type}-{$id}-" . time();

	//terima upload file, return filename with ext
	$newFilename = receive($path . $filename);
	$filenameOnly = pathinfo($newFilename, PATHINFO_FILENAME);

	//set path
//	$pathResult = oModelSetting::getPathVideo();
	$pathThumb = $pathResult;

	//create job, utk mendapatkan jobid
	$jobId = ModelJob::create($jobType, STATUS_NEW);

	$urlCallback = ModelSetting::getHostApi() . "/v1/video.php?action=callback&job_id={$jobId}";

	//execute shell
	$cmd = "cd {$pathSh} && ./process_video.sh {$jobId} {$path}{$newFilename} \"{$pathResult}\" \"{$path}\" \"{$pathThumb}\"  \"{$urlCallback}\" > {$path}{$filenameOnly}.log 2>&1 &";
	shell_exec($cmd);

	//update job utk parameter yg di input ke shell
	$param = json_encode(['type'=>$type, $paramId=>$id, 'filename'=>$newFilename, 'path_result'=>$pathResult, 'path_thumb'=>$pathThumb, 'cmd'=>$cmd ]);
	ModelJob::updateParamIn($jobId, $param);

	echo $param;
}

/**
 * @param string $filename filename utk file yg di upload, tanpa ext
 * @return null|string filename + ext
 */
function receive(string $filename){

	try {

		// Undefined | Multiple Files | $_FILES Corruption Attack
		// If this request falls under any of them, treat it invalid.
		if (
			!isset($_FILES[FILENAME]['error']) ||
			is_array($_FILES[FILENAME]['error'])
		) {
			throw new RuntimeException('Invalid parameters.');
		}

		switch ($_FILES[FILENAME]['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new RuntimeException('No file sent.');
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new RuntimeException('Exceeded filesize limit.');
			default:
				throw new RuntimeException('Unknown errors.');
		}

		// You should also check filesize here.
		if ($_FILES[FILENAME]['size'] > MAX_SIZE) {
			throw new RuntimeException('Exceeded filesize limit.');
		}

		// DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
		// Check MIME Type by yourself.
//		$finfo = new finfo(FILEINFO_MIME_TYPE);
//		if (false === $ext = array_search(
//				$finfo->file($_FILES[FILENAME]['tmp_name']),
//				array(
//					'jpg' => 'image/jpeg',
//					'png' => 'image/png',
//					'gif' => 'image/gif',
//				),
//				true
//			)) {
//			throw new RuntimeException('Invalid file format.');
//		}


		$originalFilename = $_FILES[FILENAME]['name'];
		$ext = pathinfo($originalFilename, PATHINFO_EXTENSION);

		// You should name it uniquely.
		// DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
		// On this example, obtain safe unique name from its binary data.
		$newFilename = sprintf('%s.%s', $filename, $ext);
		if (!move_uploaded_file($_FILES[FILENAME]['tmp_name'], $newFilename)) {
			throw new RuntimeException('Failed to move uploaded file.' . $filename);
		}

//		echo 'File is uploaded successfully.';

		return basename($newFilename);

	} catch (RuntimeException $e) {

		echo $e->getMessage();
	}

	return null;
}

/**
 * Terima proses callback, setelah video selesai di proses oleh ffmpeg
 */
function doCallback(){
	require_once 'model/ModelSetting.php';
	require_once 'model/ModelJob.php';
	require_once 'model/ModelVideo.php';

	$jobId = (empty($_GET['job_id']) ? 0 : $_GET['job_id']);
	$duration = (int) (empty($_GET['duration']) ? 0 : $_GET['duration']);
	$pathThumb = (empty($_GET['path_thumb']) ? '' : $_GET['path_thumb']);
	$pathVideos = (empty($_GET['path_videos']) ? '' : $_GET['path_videos']);
	$bitrates = (empty($_GET['bitrates']) ? '' : $_GET['bitrates']);

	$paramOut = json_encode($_GET);
	ModelJob::updateParamOut($jobId, $paramOut, STATUS_FINISH);

	$job = ModelJob::get($jobId);

	//exit apabila ada error atau not found
	if ($job==null) die();
	if ($job instanceof PDOException) die();

	//conver paramin sebagai object
	$paramIn = json_decode($job['param_in']);
	$jobType = $job['type'];

	//path di format mempergunakan {PATH-APP}
	$pathApp = ModelSetting::getPathApplication();
	$pathThumb = str_replace($pathApp, '{PATH-APP}', $pathThumb);

	switch ($jobType){
		case TRANS_LOCALITY:
			processLocality($jobId, $paramIn->locality_media_id, $duration, $bitrates, $pathVideos, $pathThumb);
			break;

		case TRANS_VOD:
			processVod($jobId, $paramIn, $duration, $bitrates, $pathVideos, $pathThumb);
			break;
		case TRANS_VOD_TRAILER:
			break;
	}
}

function processLocality($jobId, $localityMediaId, $duration, $bitrates, $pathVideos, $pathThumb){
	require_once 'model/ModelLocality.php';
	require_once 'model/ModelSetting.php';

	$bitrate1 = $bitrates[0];

	//path di format mempergunakan {PATH-APP}
	$pathVideo1 = $pathVideos[0];
	$pathApp = ModelSetting::getPathApplication();
	$pathVideo1 = str_replace($pathApp, '{PATH-APP}', $pathVideo1);

	$urlVideo1 = ModelSetting::getUrlContentVideo();
	$urlVideo1 = str_replace('{FILENAME}', basename($pathVideos[0]), $urlVideo1);

	//compose urlThumb untuk content
	$urlThumb = ModelSetting::getUrlContentThumbnail();
	$urlThumb= str_replace('{FILENAME}', basename($pathThumb), $urlThumb);

	//input hasil transcode ke tvideo
	$r = ModelVideo::create($jobId, TYPE_LOCALITY, $duration, $pathThumb, $urlThumb, $pathVideo1, $urlVideo1, $bitrate1 );
	if ($r instanceof PDOException) echo $r;
//	$localityMediaId = $paramIn->locality_media_id;

	ModelLocality::updateMedia($localityMediaId, $urlVideo1, $urlThumb);
}

function processVod($jobId, $paramIn, $duration, $bitrates, $pathVideos, $pathThumb){
	require_once 'model/ModelMovie.php';
	require_once 'model/ModelSetting.php';

	$bitrate1 = $bitrates[0];

	//path di format mempergunakan {PATH-APP}
	$pathVideo1 = $pathVideos[0];
	$pathApp = ModelSetting::getPathApplication();
	$pathVideo1 = str_replace($pathApp, '{PATH-APP}', $pathVideo1);

	$urlVideo1 = ModelSetting::getUrlVodVideo();
	$urlVideo1 = str_replace('{FILENAME}', basename($pathVideos[0]), $urlVideo1);

	//untuk vod url berbeda
	$urlThumb = ModelSetting::getUrlVodThumbnail();
	$urlThumb= str_replace('{FILENAME}', basename($pathThumb), $urlThumb);

//	var_dump($urlVideo1);
//	var_dump($urlThumb);
//
//	var_dump($pathVideo1);
//	var_dump($pathThumb);

	//input hasil transcode ke tvideo
//	$r = ModelVideo::create($jobId, TYPE_VOD, $duration, $pathThumb, $urlThumb, $pathVideo1, $urlVideo1, $bitrate1 );
//	if ($r instanceof PDOException) echo $r;

	$vodId = $paramIn->vod_id;

	//ambil data dari vod utk check poster
	$vod = ModelMovie::get($vodId);

	ModelMovie::updateStream($vodId, $pathVideo1, $urlVideo1, $duration);

	//update poster dari thumbnail apabila empty
	if (empty($vod['url_poster'])) {
		ModelMovie::updatePoster($vodId, $pathThumb, $urlThumb);
	}
}

