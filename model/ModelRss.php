<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 7/23/2019
 * Time: 2:12 PM
 */

require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelRss
{
	const SQL_GET_ALL_CATEGORY = 'SELECT rss_category_id, title, description, ord FROM trss_category ORDER BY ord, title';
	const SQL_GET_SOURCE = 'SELECT rss_id, catagory_id, title, lang_code, url_source, url_thumb FROM trss_catalog WHERE catagory_id=? ORDER BY title';

	static public function getAllCategory(){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRss::SQL_GET_ALL_CATEGORY);
			$stmt->execute( [ ] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}
	static public function getSource($categoryId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRss::SQL_GET_SOURCE);
			$stmt->execute( [$categoryId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

}