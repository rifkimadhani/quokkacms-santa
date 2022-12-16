<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/20/2019
 * Time: 10:24 AM
 */

require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelSubscriber
{
	const SQL_GET = 'SELECT * FROM tsubscriber WHERE subscriber_id=?';
	const SQL_GET_SUBSCRIBER_PACKAGE_RENT = 'SELECT package_id FROM tsubscriber_tvpackage WHERE rent_date<=NOW() AND expired_date>=NOW() AND subscriber_id=? AND room_id=?';
	const SQL_GET_TV_BILL = 'SELECT *, purchase_amount + tsubscriber_tvpackage.tax as amount_payable FROM tsubscriber_tvpackage WHERE subscriber_id=? AND room_id=? AND `status`<>?';
	const SQL_GET_KARAOKE_BILL = 'SELECT *, purchase_amount+tsubscriber_karaoke.tax as amount_payable FROM tsubscriber_karaoke WHERE subscriber_id=? AND room_id=? AND `status`<>?';
	const SQL_GET_VOD_BILL = 'SELECT *, purchase_amount+tsubscriber_vod.tax as amount_payable FROM tsubscriber_vod WHERE subscriber_id=? AND room_id=? AND `status`<>?';
	const SQL_GET_PMS_BILL = 'SELECT * FROM tsubscriber_pms WHERE subscriber_id=? AND room_id=?';
	const SQL_GET_STB = 'SELECT stb_id, ip_address FROM vstb_subscriber WHERE subscriber_id=?';
	const SQL_GET_RENT = 'SELECT * FROM tsubscriber_vod WHERE subscriber_id=? AND vod_id=? AND rent_date<=NOW() AND NOW()<=expired_date LIMIT 1';

	static public function get($subscriberId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSubscriber::SQL_GET);
			$stmt->execute( [$subscriberId] );

			$rows = $stmt->fetchAll();

			if (count($rows)==0) return null;

			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getSubscriberPackageRent($subscriberId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSubscriber::SQL_GET_SUBSCRIBER_PACKAGE_RENT);
			$stmt->execute( [ $subscriberId, $roomId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getTvBill($subscriberId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSubscriber::SQL_GET_TV_BILL);
			$stmt->execute( [ $subscriberId, $roomId, 'CANCEL'] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getKaraokeBill($subscriberId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSubscriber::SQL_GET_KARAOKE_BILL);
			$stmt->execute( [ $subscriberId, $roomId, 'CANCEL'] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getVodBill($subscriberId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSubscriber::SQL_GET_VOD_BILL);
			$stmt->execute( [ $subscriberId, $roomId, 'CANCEL'] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getPmsBill($subscriberId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSubscriber::SQL_GET_PMS_BILL);
			$stmt->execute( [ $subscriberId, $roomId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getStb($subscriberId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSubscriber::SQL_GET_STB);
			$stmt->execute( [ $subscriberId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

    /**
     * return 1 record yg masih valid (tdk expired)
     *
     * @param $subscriberId
     * @param $vodId
     * @return array|Exception|PDOException
     */
	static public function getRent($subscriberId, $vodId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSubscriber::SQL_GET_RENT);
			$stmt->execute( [ $subscriberId, $vodId] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}



}