<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/4/2019
 * Time: 2:07 PM
 */

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

class ModelTheme
{
	const SQL_GET = 'SELECT * FROM vtheme_element WHERE theme_id=?';
	const SQL_GET_ELEMENT = 'SELECT * FROM vtheme_element WHERE theme_id=? AND element_id=?';

	public static function get($themeId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelTheme::SQL_GET);
			$stmt->execute( [ $themeId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

    /**
     * Ambil hanya 1 theme saja
     *
     * @param $themeId
     * @param $elementId
     * @return Exception|int|PDOException
     */
	public static function getElement($themeId, $elementId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelTheme::SQL_GET_ONE);
			$stmt->execute( [ $themeId, $elementId] );

			$rows = $stmt->fetchAll();

			if (sizeof($rows)==0) return 0;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}


}