<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 7/12/2019
 * Time: 10:17 AM
 */

require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelEmegencyState
{
	const SQL_GET_BY_CODE = 'SELECT * FROM temergency WHERE emergency_code=?';

	static public function getByCode($code){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelEmegencyState::SQL_GET_BY_CODE);
			$stmt->execute( [$code] );

			$rows = $stmt->fetchAll();

			if (count($rows) == 0) return null;

			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

}