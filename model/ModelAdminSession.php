<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 7/14/2023
 * Time: 9:28 AM
 */


require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

class ModelAdminSession
{
    const SQL_UPDATE = 'UPDATE tadmin_session SET device_type=?, expire_date=?, version_code=?, version_name=?, fcm_token=? WHERE admin_session_id=?';

    public static function update($sessionId, $deviceType, $expDate, $versionCode, $versionName, $fcmToken){
        try{

            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE);
            $stmt->execute( [$deviceType, $expDate, $versionCode, $versionName, $fcmToken, $sessionId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }
}