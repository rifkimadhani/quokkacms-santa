<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/19/2019
 * Time: 12:20 PM
 */

require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';


class ModelMessage {

	const SQL_GET_BY_SUBSCRIBERANDROOM = 'SELECT * FROM tmessage WHERE (room_id=0 OR room_id=?) AND (subscriber_id=? OR subscriber_id=0) AND (isnull(expire_date) OR expire_date>NOW()) ORDER BY create_date DESC';
	const SQL_UPDATE_STATUS = 'UPDATE tmessage SET status=? WHERE message_id=?';
	const SQL_GET_ALL_IMAGE = 'SELECT * FROM vmessage_media WHERE (room_id=0 OR room_id=?) AND (subscriber_id=? OR subscriber_id=0)';

	static public function getBySubscriberAndRoom($subscriberId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMessage::SQL_GET_BY_SUBSCRIBERANDROOM);
			$stmt->execute( [$roomId, $subscriberId ] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function updateStatus($subscriberId, $messageId, $newStatus){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMessage::SQL_UPDATE_STATUS);
			$stmt->execute( [$newStatus, $messageId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	static public function getAllImage($subscriberId, $roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMessage::SQL_GET_ALL_IMAGE);
			$stmt->execute( [$roomId, $subscriberId ] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}
}