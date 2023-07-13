<?php

/**
 * Created by PhpStorm.
 * User: echri
 * Date: 18/08/2021
 * Time: 11:50
 */
require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

class ModelHotelService
{
    const SQL_GET_ACTIVE = 'SELECT
            tsubscriber_hotel_service.`task_id`, 
            tsubscriber_hotel_service.`data`, 
            tsubscriber_hotel_service.type, 
            tsubscriber_hotel_service.`status`, 
            tsubscriber_hotel_service.update_date,
            tsubscriber_group.`name` group_name, 
            tsubscriber.subscriber_id, 
            tsubscriber.group_id, 
            tsubscriber.salutation, 
            tsubscriber.`name` guest_name, 
            tsubscriber.last_name guest_last_name, 
            tsubscriber.`status` guest_status, 
            troom.`name`, 
            troom.location
        FROM
            tsubscriber_hotel_service
            INNER JOIN
            tsubscriber
            ON 
                tsubscriber_hotel_service.subscriber_id = tsubscriber.subscriber_id
            LEFT JOIN
            tsubscriber_group
            ON 
                tsubscriber.group_id = tsubscriber_group.group_id
            INNER JOIN
            troom
            ON 
                tsubscriber_hotel_service.room_id = troom.room_id
            WHERE tsubscriber_hotel_service.status IN (\'NEW\', \'ACK\')';
    const SQL_GET_HISTORY = 'SELECT
            tsubscriber_hotel_service.`task_id`, 
            tsubscriber_hotel_service.`data`, 
            tsubscriber_hotel_service.type, 
            tsubscriber_hotel_service.`status`, 
            tsubscriber_hotel_service.update_date,
            tsubscriber_group.`name` group_name, 
            tsubscriber.subscriber_id, 
            tsubscriber.group_id, 
            tsubscriber.salutation, 
            tsubscriber.`name` guest_name, 
            tsubscriber.last_name guest_last_name, 
            tsubscriber.`status` guest_status, 
            troom.`name`, 
            troom.location
        FROM
            tsubscriber_hotel_service
            INNER JOIN
            tsubscriber
            ON 
                tsubscriber_hotel_service.subscriber_id = tsubscriber.subscriber_id
            LEFT JOIN
            tsubscriber_group
            ON 
                tsubscriber.group_id = tsubscriber_group.group_id
            INNER JOIN
            troom
            ON 
                tsubscriber_hotel_service.room_id = troom.room_id
            WHERE tsubscriber_hotel_service.status IN (\'CANCEL\', \'FINISH\')';

    const SQL_UPDATE_STATUS = 'UPDATE tsubscriber_hotel_service SET status=? WHERE task_id=?';
    const SQL_CREATE = 'INSERT INTO tsubscriber_hotel_service (room_id, subscriber_id, type, status, data) VALUES (?, ?, ?, ?, ?)';
    const SQL_GET_ALL = "SELECT * FROM tsubscriber_hotel_service WHERE room_id=? AND subscriber_id=? AND status IN ('NEW','ACK') ORDER BY update_date DESC";
    const SQL_GET = 'SELECT * FROM tsubscriber_hotel_service WHERE room_id=? AND subscriber_id=? AND task_id=?';
    const SQL_CANCEL = "UPDATE tsubscriber_hotel_service SET status='CANCEL' WHERE status NOT IN ('FINISH', 'CANCEL') AND room_id=? AND subscriber_id=? AND task_id=?";

    /**
     * return semua yg active NEW & ACK
     *
     * @return array|Exception|PDOException
     */
    public static function getActive(){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_ACTIVE);
            $stmt->execute( [] );
            return $stmt->fetchAll();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function getHistory(){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_HISTORY);
            $stmt->execute( [] );
            return $stmt->fetchAll();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function get($roomId, $subscriberId, $taskId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelHotelService::SQL_GET);
            $stmt->execute( [$roomId, $subscriberId, $taskId] );

            $rows = $stmt->fetchAll();

            if (sizeof($rows)==0) return null;
            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function getAll($roomId, $subscriberId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelHotelService::SQL_GET_ALL);
            $stmt->execute( [$roomId, $subscriberId] );

            $rows = $stmt->fetchAll();
            return $rows;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function create($roomId, $subscriberId, $type, $status, $data){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelHotelService::SQL_CREATE);
            $stmt->execute( [$roomId, $subscriberId, $type, $status, $data] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function updateStatus($taskId, $status){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelHotelService::SQL_UPDATE_STATUS);
            $stmt->execute( [strtoupper($status), $taskId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function cancel($roomId, $subscriberId, $taskId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelHotelService::SQL_CANCEL);
            $stmt->execute( [$roomId, $subscriberId, $taskId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

}