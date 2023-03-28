<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 4/25/2019
 * Time: 1:27 PM
 */

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

const STATUS_ACTIVE_KARAOKE_RENT = 'ACTIVE';

class ModelKaraoke
{
	const SQL_GET_RENT = 'SELECT * FROM tsubscriber_karaoke WHERE rent_date<=NOW() AND expired_date>=NOW() AND subscriber_id=? AND room_id=? AND `status`=?';
	const SQL_PURCHASE = 'INSERT INTO tsubscriber_karaoke (subscriber_id, room_id, rent_duration, expired_date, currency, currency_sign, purchase_amount, percent_tax, tax, title, `status`, marketing_karaoke_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)';
	const SQL_GET_MARKETING = 'SELECT * FROM tmarketing_karaoke WHERE marketing_karaoke_id=?';
	const SQL_GET_MARKETING_BANNER = 'SELECT * FROM tmarketing_karaoke WHERE `status`=? ORDER BY title';

	//ambil semua kitchen
	static public function getRent($subscriberId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelKaraoke::SQL_GET_RENT);
			$stmt->execute( [$subscriberId, $roomId, 'ACTIVE'] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function purchase($subscriberId, $roomId, $rentDuration, $curr, $currSign, $price, $percentTax, $title, $marketingId){

		$now = new DateTime();
		$expDate = $now->add(new DateInterval('PT' . $rentDuration . 'H'));
		$stamp = $expDate->format('Y-m-d H:i:s');

		$tax = $price * $percentTax / 100;

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelKaraoke::SQL_PURCHASE);
			$stmt->execute( [$subscriberId, $roomId, $rentDuration, $stamp, $curr, $currSign, $price, $percentTax, $tax, $title,STATUS_ACTIVE_KARAOKE_RENT, $marketingId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	//ambil semua kitchen
	static public function getMarketing($marketingId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelKaraoke::SQL_GET_MARKETING);
			$stmt->execute( [$marketingId] );

			$rows = $stmt->fetchAll();

			if (count($rows)==0) return null;

			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getMarketingBanner(){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelKaraoke::SQL_GET_MARKETING_BANNER);
			$stmt->execute( [ 'ACTIVE' ] );

			$rows = $stmt->fetchAll();

			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}


}