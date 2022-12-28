<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/28/2022
 * Time: 10:52 AM
 */

namespace App\Models;

class MessageMediaModel extends BaseModel
{
    const SQL_GET_ALL = 'SELECT * FROM tmessage_media WHERE message_id=?';

    const SQL_WRITE_1 = 'DELETE FROM tmessage_media WHERE message_id=?';
    const SQL_WRITE_2 = 'INSERT INTO tmessage_media (message_id, url_image) VALUES (?, ?)';

    protected $table      = 'tmessage_media';
    protected $primaryKey = 'message_media_id';
    protected $allowedFields = ['url_image', 'url_video', 'caption'];

    public function getAll($messageId){
        return $this->db->query(self::SQL_GET_ALL, [$messageId])->getResult('array');
    }

    /**
     * 1. hapus semua record
     * 2. insert satu persaya record
     *
     * @param $messageId
     * @param $urlImage = comma seperated value
     *
     * @return true/false
     */
    public function write($messageId, $urlImage){

        $this->db->transStart();

        //delete
        $this->db->query(self::SQL_WRITE_1, [$messageId]);

        $ar = explode(',', $urlImage);

        //insert one by one
        foreach ($ar as $url){
            $this->db->query(self::SQL_WRITE_2, [$messageId, $url]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return false;
        }

        return true;
    }


}