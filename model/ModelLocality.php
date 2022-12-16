<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 4/9/2019
 * Time: 12:57 PM
 */
require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelLocality
{
	const GET_ALL = 'SELECT * FROM tlocality ORDER BY ord, title';
	const GET_ALL_IMAGE = 'SELECT tlocality_media.* FROM tlocality INNER JOIN tlocality_media ON tlocality.locality_id = tlocality_media.locality_id';
	const UPDATE_MEDIA = 'UPDATE tlocality_media SET url_image=?, url_video=? WHERE locality_media_id=?';

	static public function getAll(){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelLocality::GET_ALL);
			$stmt->execute(  );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

    /**
     * Ambil semua image yg relevant dgn tlocality
     *
     * @return array|Exception|PDOException
     */
	static public function getAllImage(){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelLocality::GET_ALL_IMAGE);
			$stmt->execute(  );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function updateMedia(int $localityMediaId, $urlVideo, $urlImage=null){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelLocality::UPDATE_MEDIA);
			$stmt->execute( [$urlImage, $urlVideo, $localityMediaId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
//			if ($e->getCode()=='23000') return 0;
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

}