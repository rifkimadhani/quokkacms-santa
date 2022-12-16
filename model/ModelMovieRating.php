<?php

/**
 * Created by PhpStorm.
 * User: erick
 * Date: 11/9/2022
 * Time: 9:25 AM
 */
define('RATING_MAX', 100);

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/Koneksi.php';

class ModelMovieRating
{
    const SQL_CREATE = 'INSERT INTO tvod_rating (vod_id, user_id, rating) VALUES (?, ?, ?)';
    const SQL_GET_ONE = 'SELECT * FROM tvod_rating WHERE vod_id=? AND user_id=?';
    const SQL_CALC = 'SELECT SUM(rating) as rating, COUNT(user_id) as count FROM tvod_rating WHERE vod_id=? GROUP BY vod_id';
    const SQL_DELETE = 'DELETE FROM tvod_rating WHERE vod_id=? AND user_id=?';

    static function create(int $vodId, int $userId, $rating) {

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_CREATE);
            $stmt->execute( [$vodId, $userId, $rating] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            //return as success apabila duplicate
            if ($e->getCode()=='23000') return 1;
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function getOne(int $vodId, $userId){

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_ONE);

            $stmt->execute([ $vodId,  $userId]);

            $rows = $stmt->fetchAll();

            if (count($rows)==0) return null;

            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    /**
     * hitung total rating dan jumlah user
     *
     * @param int $vodId
     * @return Exception|null|PDOException
     */
    static public function calc(int $vodId){

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_CALC);

            $stmt->execute([ $vodId]);

            $rows = $stmt->fetchAll();

            if (count($rows)==0) return null;

            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function delete(int $vodId, int $userId){

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_DELETE);

            $stmt->execute([ $vodId, $userId]);

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }


}