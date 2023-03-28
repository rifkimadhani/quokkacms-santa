<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 9/6/2019
 * Time: 1:08 PM
 */

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

class ModelJob
{
	const SQL_GET = 'SELECT * FROM tjob WHERE job_id=?';
	const SQL_CREATE = 'INSERT INTO tjob (type, `status`) VALUES (?,?)';
	const SQL_UPDATE_PARAMIN = 'UPDATE tjob SET param_in=? WHERE job_id=?';
	const SQL_UPDATE_PARAMOUT = 'UPDATE tjob SET param_out=?, `status`=? WHERE job_id=?';


	static public function get($jobId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelJob::SQL_GET);
			$stmt->execute( [$jobId] );

			$rows = $stmt->fetchAll();

			if (count($rows)==0) return null;

			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}


	/**
	 * @param $type LOCALITY / FACILITY / MESSAGE / ADS
	 * @param $param
	 * @return Exception|int|PDOException|string jobId
	 */
	public static function create(string $type, string $status) {

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelJob::SQL_CREATE);
			$stmt->execute( [$type, $status] );

			return $pdo->lastInsertId();

		}catch (PDOException $e){
//			if ($e->getCode()=='23000') return 0;
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	/**
	 * @param int $jobId
	 * @param string $paramIn parameter yg di kirim ke shell
	 * @return Exception|int|PDOException
	 */
	public static function updateParamIn(int $jobId, string $paramIn){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelJob::SQL_UPDATE_PARAMIN);
			$stmt->execute( [$paramIn, $jobId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
//			if ($e->getCode()=='23000') return 0;
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	/**
	 * @param int $jobId
	 * @param string $paramOut parameter yg keluar dari shells
	 * @return Exception|int|PDOException
	 */
	public static function updateParamOut(int $jobId, string $paramOut, string $status){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelJob::SQL_UPDATE_PARAMOUT);
			$stmt->execute( [$paramOut, $status, $jobId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
//			if ($e->getCode()=='23000') return 0;
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}
}