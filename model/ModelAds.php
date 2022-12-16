<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 5/16/2019
 * Time: 11:30 AM
 */
require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelAds
{
	const SQL_GET_BY_SYSTEM_TYPE = 'SELECT * FROM vads WHERE system_type=?';
	const SQL_GET_BY_ROOMID = 'SELECT * FROM vads_room WHERE spot_id=? AND room_id=?';

	static public function getBySystemType($systemType){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelAds::SQL_GET_BY_SYSTEM_TYPE);
			$stmt->execute( [$systemType] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getByRoomId($spotId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelAds::SQL_GET_BY_ROOMID);
			$stmt->execute( [$spotId, $roomId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

}