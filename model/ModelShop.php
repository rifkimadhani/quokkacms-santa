<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 15/12/2021
 * Time: 15:04
 */

require_once __DIR__ . '/../../config/Koneksi.php';
require_once __DIR__ . '/../../library/Log.php';

class ModelShop
{
    const STATUS_ORDER_NEW = 'NEW';
    const STATUS_PRODUCT_AVAILABLE = 'AVAILABLE';

    const SQL_GETALL_AVAILABLE = 'SELECT * FROM tshop_product WHERE status="AVAILABLE"';
    const SQL_GETALL_ORDER = 'SELECT * FROM tshop_order WHERE subscriber_id=? ORDER BY create_date DESC';
    const SQL_GETALL_SELLER  = 'SELECT * FROM tshop_seller';
    const SQL_GET = 'SELECT * FROM tshop_product WHERE product_id=?';
    const SQL_GET_PRODUCT_AVAILABILITY = 'SELECT * FROM tshop_product WHERE product_id=? AND status=?';
    const SQL_GEN_ORDER_CODE  = 'CALL spGetShopOrderId (@year,@month,@day,@value)';
    const SQL_CREATE_ORDER = 'INSERT INTO tshop_order (order_code, subscriber_id, payment_type, note, total_amount, order_json, status) VALUES (?,?,?,?,?,?,?)';

    public static function createOrder(string $orderCode, int $subscriberId, string $paymentType, string $note, $totalAmount, string $json){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelShop::SQL_CREATE_ORDER);
            $stmt->execute( [$orderCode, $subscriberId, $paymentType, $note, $totalAmount, $json, ModelShop::STATUS_ORDER_NEW] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function getAllOrder(int $subscriberId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelShop::SQL_GETALL_ORDER);
            $stmt->execute( [$subscriberId] );

            $rows = $stmt->fetchAll();

            return $rows;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function getAllAvailable(){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelShop::SQL_GETALL_AVAILABLE);
            $stmt->execute( [] );

            $rows = $stmt->fetchAll();

            return $rows;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function getAllSeller(){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelShop::SQL_GETALL_SELLER);
            $stmt->execute( [] );

            $rows = $stmt->fetchAll();

            return $rows;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function get(int $productId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelShop::SQL_GET);
            $stmt->execute( [$productId] );

            $rows = $stmt->fetchAll();

            if (count($rows)==0) return null;

            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function genOrderCode(){

        try{
            $pdo = Koneksi::create();

            $stmt = $pdo->prepare(ModelShop::SQL_GEN_ORDER_CODE);
            $stmt->execute();
            $stmt->closeCursor();

            $row = $pdo->query("SELECT @year,@month,@day,@value")->fetch(PDO::FETCH_ASSOC);

            $year = $row['@year'];
            $month = $row['@month'];
            $day = $row['@day'];
            $value = $row['@value'];
//			$monthName = ModelCounter::MONTHS[$month-1];

            $code = sprintf('SP-%d%d%s', $year, $month, $value);

            return $code;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

}