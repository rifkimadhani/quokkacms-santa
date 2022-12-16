<?php

/**
 * Created by PhpStorm.
 * User: echri
 * Date: 18/08/2021
 * Time: 11:50
 */
require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelHotelService
{
    const SQL_CREATE = 'INSERT INTO tsubscriber_hotel_service (room_id, subscriber_id, type, status, data) VALUES (?, ?, ?, ?, ?)';
    const SQL_GET_ALL = "SELECT * FROM tsubscriber_hotel_service WHERE room_id=? AND subscriber_id=? AND status IN ('NEW','ACK') ORDER BY update_date DESC";
    const SQL_GET = 'SELECT * FROM tsubscriber_hotel_service WHERE room_id=? AND subscriber_id=? AND task_id=?';
    const SQL_CANCEL = "UPDATE tsubscriber_hotel_service SET status='CANCEL' WHERE status NOT IN ('FINISH', 'CANCEL') AND room_id=? AND subscriber_id=? AND task_id=?";

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