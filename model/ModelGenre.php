<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 7/3/2019
 * Time: 2:40 PM
 */
require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelGenre
{
	const SQL_GETALL = 'SELECT genre_id, genre FROM tgenre ORDER BY genre';
	const SQL_GET_GENRE = 'SELECT genre_id, genre FROM vgenre WHERE vod_id=?';

	static public function getAll(){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelGenre::SQL_GETALL);

			$stmt->execute( );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getGenre($vodId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelGenre::SQL_GET_GENRE);

			$stmt->execute( [$vodId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}
}