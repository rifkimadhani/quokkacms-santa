<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-12 12:48:42
 */
namespace App\Models;

class RoomServiceItemModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM troomservice_item WHERE order_code=?';

    public function get($orderCode)
    {
        $db = db_connect();
        return $db->query(self::SQL_GET, [$orderCode])->getResult('array');
    }
}
