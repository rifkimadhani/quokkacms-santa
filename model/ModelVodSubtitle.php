<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 01/10/2022
 * Time: 12:54
 */

require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelVodSubtitle
{
    const SQL_GET_BY_VODID = 'SELECT * FROM vvod_subtitle WHERE vod_id=?';

    /**
     * ambil semua subtitle berdasarkan vodId
     *
     * @param $vodId
     * @return array|Exception|PDOException
     */
    static public function getByVodId($vodId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_BY_VODID);
            $stmt->execute( [$vodId] );

            return $stmt->fetchAll();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }
}