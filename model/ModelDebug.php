<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 6/13/2019
 * Time: 3:42 PM
 */

require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelDebug
{
	const SQL_CREATE = 'INSERT INTO tapp_log (app_id, stb_id, version_code, ip, type, tag, message, sequence) VALUES (?,?,?,?,?,?,?,?)';
	const SQL_GET_COUNTER = 'CALL spGetLogCounter (@year,@month,@day,@value)';

	static public function create($appId, $stbId, $versionCode, $ip, $type, $tag, $message){

		$sequence = self::getCounter();

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelDebug::SQL_CREATE);
			$stmt->execute( [ $appId, $stbId, $versionCode, $ip, $type, $tag, $message, $sequence ] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function getCounter(){

		try{
			$pdo = Koneksi::create();

			$stmt = $pdo->prepare(ModelDebug::SQL_GET_COUNTER);
			$stmt->execute();
			$stmt->closeCursor();

			$row = $pdo->query("SELECT @year,@month,@day,@value")->fetch(PDO::FETCH_ASSOC);

//			$year = $row['@year'];
//			$month = $row['@month'];
//			$day = $row['@day'];
			$value = $row['@value'];

			return $value;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}
}