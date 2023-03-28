<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 9/9/2019
 * Time: 1:33 PM
 */

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

class ModelVideo
{
	const SQL_CREATE = 'INSERT INTO tvideo (job_id, type, duration, path_thumb, url_thumb, path_video1, url_video1, bitrrate1, path_video2, url_video2, bitrrate2, path_video3, url_video3, bitrrate3) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
//	const SQL_CREATE_1_VIDEO = 'INSERT INTO tvideo (job_id, type, runtime, path_thumb, url_thumb, path_video1, path_video2, path_video3, path_video4, url_video1, url_video2, url_video3, url_video4, bitrrate1, bitrrate2, bitrrate3, bitrrate4) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';

	public static function create(int $jobId, $type, $duration, $pathThumb, $urlThumb, string $pathVideo1, string $urlVideo1, int $bitrate1) {

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelVideo::SQL_CREATE);
			$stmt->execute( [$jobId, $type, $duration, $pathThumb, $urlThumb, $pathVideo1, $urlVideo1, $bitrate1, null,null,null,null,null,null] );

			return $pdo->lastInsertId();

		}catch (PDOException $e){
//			if ($e->getCode()=='23000') return 0;
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

}