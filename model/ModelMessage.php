<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/19/2019
 * Time: 12:20 PM
 */

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';


class ModelMessage {

	const SQL_GET_NEW_MESSAGE = 'SELECT count(*) `count` FROM tmessage WHERE subscriber_id=? AND status=\'NEW\'';
	const SQL_GET_BY_SUBSCRIBER = 'SELECT * FROM tmessage WHERE subscriber_id=? AND (expire_date IS NULL OR expire_date > ?) ORDER BY create_date DESC';
	const SQL_GET_BY_ROOM_AND_SUBS = 'SELECT * FROM tmessage WHERE room_id=? AND (tmessage.subscriber_id IS NULL OR subscriber_id=?) AND (expire_date IS NULL OR expire_date > ?) ORDER BY create_date DESC';
	const SQL_UPDATE_STATUS = 'UPDATE tmessage SET status=? WHERE message_id=?';
	const SQL_GET_ALL_IMAGE = 'SELECT * FROM vmessage_media WHERE subscriber_id=?';
	const SQL_GET_IMAGE_BY_ROOM = 'SELECT * FROM vmessage_media WHERE room_id=?';

	static public function getBySubscriber($subscriberId){
	    require_once __DIR__ . '/../library/DateUtil.php';

		try{
		    $now = DateUtil::now();
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMessage::SQL_GET_BY_SUBSCRIBER);
			$stmt->execute( [$subscriberId, $now] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

    /**
     * Ambil dari roomId dan subscriberId (boleh null)
     * @param $roomId
     * @param $subscriber
     * @return array|Exception|PDOException
     */
	static public function getByRoomAndSubs($roomId, $subscriber){
	    require_once __DIR__ . '/../library/DateUtil.php';

		try{
		    $now = DateUtil::now();
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMessage::SQL_GET_BY_ROOM_AND_SUBS);
			$stmt->execute( [$roomId, $subscriber, $now] );

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

	static public function getAllImage($subscriberId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMessage::SQL_GET_ALL_IMAGE);
			$stmt->execute( [$subscriberId ] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getImageByRoom($roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMessage::SQL_GET_IMAGE_BY_ROOM);
			$stmt->execute( [$roomId ] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

    static public function countNewMessage($subscriberId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelMessage::SQL_GET_NEW_MESSAGE);
            $stmt->execute( [$subscriberId ] );

            $rows = $stmt->fetchAll();
            return $rows;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }
}