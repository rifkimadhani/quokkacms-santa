<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 4/9/2019
 * Time: 12:09 PM
 */
require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

class ModelKitchen
{
	const SQL_GET_ALL = 'SELECT * FROM tkitchen ORDER BY name';
	const SQL_GET_ALL_MENU_GROUP  = 'SELECT * FROM tkitchen_menu_group WHERE kitchen_id=? ORDER BY seq DESC';
	const SQL_GET_ALL_MENU = 'SELECT * FROM tkitchen_menu WHERE menu_group_id=?';
	const SQL_GET = 'SELECT * FROM tkitchen WHERE kitchen_id=?';


	//ambil semua kitchen
	static public function getAll(){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelKitchen::SQL_GET_ALL);
			$stmt->execute(  );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getAllMenuGroup($kitchenId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelKitchen::SQL_GET_ALL_MENU_GROUP);
			$stmt->execute( [$kitchenId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getAllMenu($menuGroupId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelKitchen::SQL_GET_ALL_MENU);
			$stmt->execute( [$menuGroupId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function get($kitchenId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelKitchen::SQL_GET);
			$stmt->execute( [$kitchenId] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;

			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

}