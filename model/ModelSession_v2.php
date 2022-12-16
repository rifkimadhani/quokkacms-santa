<?php

/**
 * Created by PhpStorm.
 * User: echri
 * Date: 24/03/2020
 * Time: 10:17
 */

require_once __DIR__ .'/../../config/Koneksi.php';
require_once __DIR__ .'/../../library/Security.php';
require_once __DIR__ .'/../../library/Log.php';


class ModelSession_v2
{
    const SQL_UPDATE_DEVICE_INFO = 'UPDATE tsession SET device_type=?, version_name=?, version_code=?, version_api=?, application_id=?, device_name=?, ip=?, country_code=?, city_name=?, region_name=? WHERE session_id=?';
    const SQL_UPDATE_PAY_TV = "UPDATE tsession set pay_tv=? where sessionId=?"; //set pay_tv =0 jika paket sudah expired atau tidak aktif
    const SQL_DELETE = "DELETE from tsession where sessionId =?";

    /** tsessionsn */
    const SQL_SET_USER_SN = "INSERT INTO tsessionsn (sessionId,nopelanggan, sn) VALUES (?,?,?)";
    const SQL_COUNT_USAGE_SN = "SELECT COUNT(*) AS countsn FROM tsessionsn WHERE sn=?";
    const SQL_COUNT_USAGE_SN_BY_USER = "SELECT sessionId FROM tsessionsn WHERE sessionId = ? and nopelanggan=?";
    const SQL_GET_USER_SN_FIRST_INPUT = "SELECT * FROM tsessionsn ORDER BY create_date asc limit 0,1";
    const SQL_DELETE_USER_SN = "DELETE FROM tsessionsn WHERE sessionId=?";
    const SQL_GET_USER_SN= "SELECT * FROM tsessionsn WHERE sessionId=?";


    public static function updateDeviceInfo(string $sessionId, $applicationId, $deviceType, $versionName, int $versionCode, $versionApi, $deviceName, $ip, $cc, $cityName, $regionName){

        try {
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE_DEVICE_INFO);
            $stmt->execute([$deviceType, $versionName, $versionCode, $versionApi, $applicationId, $deviceName, $ip, $cc,$cityName, $regionName, $sessionId]);

            return $stmt->rowCount();

        } catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    //set username ketika data sama sekali tidak ada atau baru login dengan facebook tanpa guest
    public static function setUserNoPel($userId, $nopel, $sn)
    {
        try {
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_SET_USER_SN);
            $stmt->execute([$userId, $nopel,$sn]);
            return $stmt->rowCount();
        } catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return 0;
        }

    }

    public static function getCountUsageNoPel(string $nopel): int
    {

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_COUNT_USAGE_SN);
            $stmt->execute([$nopel]);
            return $stmt->rowCount();

        } catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return 0;
        }
    }

    public static function checkUserNoPelExists(string $userId, string $nopel)
    {

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_COUNT_USAGE_SN_BY_USER);
            $stmt->execute([$userId,$nopel]);
            return $stmt->rowCount();
        } catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return 0;
        }
    }

    public static function getUserSNFirstInput()
    {

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_USER_SN_FIRST_INPUT);
                $stmt->execute();
            $rows = $stmt->fetchAll();
            return $rows;
        } catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function deleteUserSN($userId)
    {

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_DELETE_USER_SN);
            $stmt->execute([$userId]);
            return $stmt->rowCount();
        } catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function updatePayTV($payTV, $sessionId){
        try {
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelSession_v2::SQL_UPDATE_PAY_TV);
            $stmt->execute([$payTV, $sessionId]);
            return $stmt->rowCount();

        } catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }


    public static function deleteSession($sessionId){
        try {
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelSession_v2::SQL_DELETE);
            $stmt->execute([$sessionId]);
            return $stmt->rowCount();

        } catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }


    public static function getUserSN($sessionId)
    {

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_USER_SN);
            $stmt->execute([$sessionId]);
            $rows = $stmt->fetchAll();
            return $rows;
        } catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }


}