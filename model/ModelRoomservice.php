<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 4/16/2019
 * Time: 10:04 AM
 */

const PAYMENT_TYPE_BILL_TO_ROOM = 'BILL-TO-ROOM';
const PAYMENT_TYPE_CASH = 'CASH';

const ROOMSERVICE_STATUS_CANCEL = 'CANCEL';

require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelRoomservice
{
	const SQL_GET_LIST = 'SELECT * FROM vsubsciber_order WHERE subscriber_id=? AND room_id=? AND kitchen_id=?';
	const SQL_ADD_ORDER = 'INSERT INTO tsubscriber_roomservice (subscriber_id, room_id, menu_id, qty) VALUES (?,?,?,?)';
	const SQL_UPDATE_ORDER = 'UPDATE tsubscriber_roomservice SET qty=? WHERE subscriber_id=? AND room_id=? AND menu_id=?';
	const SQL_REMOVE_ORDER = 'DELETE FROM tsubscriber_roomservice WHERE subscriber_id=? AND room_id=? AND menu_id=?';
	const SQL_CLEAR_ORDER = 'DELETE FROM tsubscriber_roomservice WHERE subscriber_id=? AND room_id=?';
	const SQL_GEN_ORDER_CODE  = 'CALL spGetRoomserviceOrderId (@year,@month,@day,@value)';
	const SQL_GET_PURCHASED_LIST  = 'SELECT *, purchase_amount+troomservice.service_charge+troomservice.tax+troomservice.delivery_fee as amount_payable FROM troomservice WHERE subscriber_id=? AND room_id=? AND `status`<>? ORDER BY create_date DESC';
	const SQL_GET_BILL  = 'SELECT *, purchase_amount+troomservice.service_charge+troomservice.tax+troomservice.delivery_fee as amount_payable FROM troomservice WHERE subscriber_id=? AND room_id=? AND `status`<>? AND payment_type=? ORDER BY create_date';
	const SQL_GET_PURCHASED_DETAIL  = 'SELECT menu_id, menu_name, qty, price, qty*price as total FROM troomservice_item WHERE order_code=? ORDER BY menu_name';

	const SQL_CREATE_A = 'INSERT INTO troomservice (order_code, subscriber_id, room_id, kitchen_id, purchase_amount, percent_service_charge, service_charge, percent_tax, tax, notes, currency, currency_sign, payment_type, delivery_fee, kitchen_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
	const SQL_CREATE_B = 'INSERT INTO troomservice_item (order_code, menu_id, qty, price, menu_name) VALUES (?,?,?,?,?)';
	const SQL_CREATE_C = 'DELETE FROM tsubscriber_roomservice WHERE subscriber_id=? AND room_id=? AND menu_id=?';

	const SQL_GET  = 'SELECT *, purchase_amount+troomservice.service_charge+troomservice.tax+troomservice.delivery_fee as amount_payable FROM troomservice WHERE subscriber_id=? AND room_id=? AND order_code=?';


	public static function getList($subscriberId, $roomId, $kitchenId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRoomservice::SQL_GET_LIST);
			$stmt->execute( [ $subscriberId, $roomId, $kitchenId] );

			$rows = $stmt->fetchAll();

			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}


	/**
	 * Update apabila sudah ada record, atau add kalo belum ada
	 *
	 * @param $subscriberId
	 * @param $roomId
	 * @param $menuId
	 * @param $qty
	 *
	 * @return Exception|int|PDOException,  return 0 karena salah menu id
	 */
	public static function addOrUpdateOrder($subscriberId, $roomId, $menuId, $qty){
		$r = self::updateOrder($subscriberId, $roomId, $menuId, $qty);

		//apabila ada error langsung exit
		if ($r instanceof PDOException) return $r;


		//update sudh sukses tdk perlu melakukan add
		if ($r==1) return 1;

		//apabila update tdk bisa artinya harus add
		$r = self::addOrder($subscriberId, $roomId, $menuId, $qty);

		return $r;
	}

	public static function addOrder($subscriberId, $roomId, $menuId, $qty){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRoomservice::SQL_ADD_ORDER);
			$stmt->execute( [ $subscriberId, $roomId, $menuId, $qty] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			if ($e->getCode()==23000) return 0;
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function updateOrder($subscriberId, $roomId, $menuId, $qty){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRoomservice::SQL_UPDATE_ORDER);
			$stmt->execute( [ $qty, $subscriberId, $roomId, $menuId ] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function removeOrder($subscriberId, $roomId, $menuId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRoomservice::SQL_REMOVE_ORDER);
			$stmt->execute( [ $subscriberId, $roomId, $menuId ] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function clearOrder($subscriberId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRoomservice::SQL_CLEAR_ORDER);
			$stmt->execute( [ $subscriberId, $roomId ] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function genOrderCode(){

		try{
			$pdo = Koneksi::create();

			$stmt = $pdo->prepare(ModelRoomservice::SQL_GEN_ORDER_CODE);
			$stmt->execute();
			$stmt->closeCursor();

			$row = $pdo->query("SELECT @year,@month,@day,@value")->fetch(PDO::FETCH_ASSOC);

			$year = $row['@year'];
			$month = $row['@month'];
			$day = $row['@day'];
			$value = $row['@value'];
//			$monthName = ModelCounter::MONTHS[$month-1];

			$code = sprintf('%d%s', $year, $value);

			return $code;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function create($subscriberId, $roomId, $kitchenId, $serviceChargePercent, $taxPercent, $deliveryFee, $kitchenName, $paymentType, $currency, $currencySign){

		//1. get all order from db
		$list = ModelRoomservice::getList($subscriberId, $roomId, $kitchenId);
		if (count($list)==0) return null;

		$orderCode = ModelRoomservice::genOrderCode();

		$notes = '';

		try {
			$pdo = Koneksi::create();
			$pdo->beginTransaction();


			$arRoomservice = array();
			$purchaseAmount = 0;
			foreach ($list as $item){
				$qty = $item['qty'];
				$price = $item['price'];
				$menuId = $item['menu_id'];
				$name = $item['name'];

				$arRoomservice[] = [ 'menuId'=>$menuId, 'qty'=>$qty, 'price'=>$price, 'name'=>$name ];

				$purchaseAmount += $qty * $price;

				//masukin order
				$stmt = $pdo->prepare(ModelRoomservice::SQL_CREATE_B);
				$stmt->execute( [$orderCode, $menuId, $qty, $price , $name] );

				//delete order
				$stmt = $pdo->prepare(ModelRoomservice::SQL_CREATE_C);
				$stmt->execute([$subscriberId, $roomId, $menuId]);
			}

			$serviceCharge = ($serviceChargePercent/100) * $purchaseAmount;

			$grandTotal = $purchaseAmount + $serviceCharge + $deliveryFee;

			//tax di hitung dari total final
			$tax = $grandTotal * ($taxPercent/100);

			$stmt = $pdo->prepare(ModelRoomservice::SQL_CREATE_A);
			$stmt->execute([$orderCode, $subscriberId, $roomId, $kitchenId, $purchaseAmount, $serviceChargePercent,
				$serviceCharge, $taxPercent, $tax, $notes, $currency, $currencySign, $paymentType, $deliveryFee, $kitchenName]);

			//commit
			$pdo->commit();

			return $orderCode;

		} catch (PDOException $e) {
			Log::writeErrorLn($e->getMessage());
			$pdo->rollBack();

			return $e;
		}

	}

	public static function getPurchasedList($subscriberId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRoomservice::SQL_GET_PURCHASED_LIST);
			$stmt->execute( [ $subscriberId, $roomId, ROOMSERVICE_STATUS_CANCEL] );
			$rows = $stmt->fetchAll();

			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function getBill($subscriberId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRoomservice::SQL_GET_BILL);
			$stmt->execute( [ $subscriberId, $roomId, ROOMSERVICE_STATUS_CANCEL, PAYMENT_TYPE_BILL_TO_ROOM] );
			$rows = $stmt->fetchAll();

			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function getPurchasedDetail($orderCode){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRoomservice::SQL_GET_PURCHASED_DETAIL);
			$stmt->execute( [ $orderCode] );
			$rows = $stmt->fetchAll();

			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	/**
	 * Ambil 1 record
	 *
	 * @param $subscriberId
	 * @param $roomId
	 * @param $orderCode
	 * @return Exception|null|PDOException
	 */
	public static function get($subscriberId, $roomId, $orderCode){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelRoomservice::SQL_GET);
			$stmt->execute( [$subscriberId, $roomId, $orderCode] );
			$rows = $stmt->fetchAll();

			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

}