<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 09/03/2022
 * Time: 9:17
 */
require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

class ModelLog
{
    const SQL_CREATE = 'INSERT INTO tlog (stb_id, ip, type, level, message) VALUES (?,?,?,?,?)';

    public static function create(int $stbId, string $ip, string $type, int $level, string $message){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelLog::SQL_CREATE);
            $stmt->execute( [$stbId, $ip, $type, $level, $message] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }
}