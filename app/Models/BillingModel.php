<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 8/24/2023
 * Time: 10:07 AM
 */

namespace App\Models;

class BillingModel
{
    const SQL_SUMMARY_ROOMSERVICE = "SELECT SUM(subtotal) total FROM(SELECT (purchase_amount + service_charge + tax + IFNULL( delivery_fee, 0)) subtotal FROM troomservice WHERE payment_type IN ('BILL-TO-ROOM') AND subscriber_id = ? AND room_id = ?) as sub";
    const SQL_SUMMARY_VOD = " SELECT sum(subtotal) total FROM( SELECT ( purchase_amount + tax) subtotal FROM tsubscriber_vod WHERE subscriber_id = ? AND room_id = ?) sub";

    public function getSummaryRoomService($subscriberId, $roomId)
    {
        $db = db_connect();
        $r = $db->query(self::SQL_SUMMARY_ROOMSERVICE, [$subscriberId, $roomId])->getResult('array');
        if (sizeof($r)==0) return 0;
        return (int) $r[0]['total'];
    }

    public function getSummaryVod($subscriberId, $roomId)
    {
        $db = db_connect();
        $r = $db->query(self::SQL_SUMMARY_VOD, [$subscriberId, $roomId])->getResult('array');
        if (sizeof($r)==0) return 0;
        return (int) $r[0]['total'];
    }

}