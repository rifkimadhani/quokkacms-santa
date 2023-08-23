<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 11/7/2019
 * Time: 2:00 PM
 */

/**
 * api ini untuk playback mp4 di browser,
 * dgn authentication
 *
 */

require_once __DIR__ . '/../../library/Log.php';
Log::writeRequestUri();

exit();

require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../../model/ModelSetting.php';
require_once __DIR__ . '/../../model/ModelMovie.php';

$vodId = (empty($_GET['vod_id']) ? 0 : $_GET['vod_id']);

$vod = ModelMovie::get($vodId);

if (is_null($vod)){
    header('Content-type: application/json; charset=utf-8');
    echo errCompose(ERR_MOVIE_ID_NOT_VALID);
    exit();
}

exitOnPdoException($vod);

//cari path utk app ini, yg nanti akan di append ke posisi file
$basePath= ModelSetting::getBasePath('/../../'); //mundur 3 level, dari posisi api ini


//file yg harus di stream
$file = str_replace('{BASE-PATH}', $basePath, $vod['path_stream1']);;

//var_dump($basePath);
//var_dump($file);
//exit();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// IMPLEMENTASI STREAM

//streamable content
$fp = @fopen($file, 'rb');

$size = filesize($file); // File size
$length = $size;           // Content length
$start = 0;               // Start byte
$end = $size - 1;       // End byte

header('Content-type: video/mp4');

//header("Accept-Ranges: 0-$length");
header("Accept-Ranges: bytes");
if (isset($_SERVER['HTTP_RANGE'])) {
	$c_start = $start;
	$c_end = $end;

	list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
	if (strpos($range, ',') !== false) {
		header('HTTP/1.1 416 Requested Range Not Satisfiable');
		header("Content-Range: bytes $start-$end/$size");
		exit;
	}
	if ($range == '-') {
		$c_start = $size - substr($range, 1);
	} else {
		$range = explode('-', $range);
		$c_start = $range[0];
		$c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
	}
	$c_end = ($c_end > $end) ? $end : $c_end;
	if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
		header('HTTP/1.1 416 Requested Range Not Satisfiable');
		header("Content-Range: bytes $start-$end/$size");
		exit;
	}
	$start = $c_start;
	$end = $c_end;
	$length = $end - $start + 1;
	fseek($fp, $start);
	header('HTTP/1.1 206 Partial Content');
}
header("Content-Range: bytes $start-$end/$size");
header("Content-Length: " . $length);

$buffer = 1024 * 8;
while (!feof($fp) && ($p = ftell($fp)) <= $end) {

	if ($p + $buffer > $end) {
		$buffer = $end - $p + 1;
	}
	set_time_limit(0);
	echo fread($fp, $buffer);
	flush();
}

fclose($fp);
exit();