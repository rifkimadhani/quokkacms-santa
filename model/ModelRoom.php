<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 7/18/2019
 * Time: 10:00 AM
 */

require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelRoom
{
	const SQL_GET_ROOM_BY_STBID = 'SELECT * FROM vstb_room WHERE stb_id=?';

	static public function getRoomByStbId($stbId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRoom::SQL_GET_ROOM_BY_STBID);
			$stmt->execute( [$stbId] );

			$rows = $stmt->fetchAll();

			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

}