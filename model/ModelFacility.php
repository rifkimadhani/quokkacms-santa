<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/19/2019
 * Time: 10:13 AM
 */

require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';


class ModelFacility {

	const SQL_GET_ALL = 'SELECT * FROM tfacility ORDER BY name';
	const SQL_GET_ALL_IMAGE = 'SELECT facility_id, facility_media_id, url_image, url_video, caption, update_date FROM tfacility_image';

	static public function getAll(){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelFacility::SQL_GET_ALL);
			$stmt->execute(  );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getAllImage(){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelFacility::SQL_GET_ALL_IMAGE);
			$stmt->execute(  );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}
}