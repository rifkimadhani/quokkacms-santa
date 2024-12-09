<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 12:01 PM
 */

namespace App\Models;

use App\Libraries\SSP;

class MessageModel extends BaseModel
{
    const SQL_GET_ALL   = "SELECT `tmessage`.`message_id` AS `message_id`, `tmessage`.`subscriber_id` AS `Guest ID`, CASE WHEN `tsubscriber`.`name` IS NOT NULL THEN `tsubscriber`.`name` ELSE '-' END AS `Guest Name`, `tsubscriber`.`group_id` AS `group_id`, `tsubscriber`.`status` AS `Guest Status`, `tsubscriber_group`.`name` AS `Guest Group`, `tmessage`.`room_id` AS `Room ID`, `troom`.`name` AS `Room`, `tmessage`.`from` AS `From`, `tmessage`.`title` AS `Title`, `tmessage`.`message` AS `Message`, `tmessage`.`status` AS `Status`, `tmessage`.`create_date` AS `Create Date`, `tmessage`.`update_date` AS `Update Date` FROM `tmessage` LEFT JOIN `tsubscriber` ON `tmessage`.`subscriber_id` = `tsubscriber`.`subscriber_id` LEFT JOIN `tsubscriber_group` ON `tsubscriber`.`group_id` = `tsubscriber_group`.`group_id` LEFT JOIN `troom` ON `tmessage`.`room_id` = `troom`.`room_id` WHERE `tsubscriber`.`status` = 'CHECKIN' OR tmessage.subscriber_id IS NULL ORDER BY `tmessage`.`message_id` DESC";
    const SQL_GET_ALL_HISTORY  = "SELECT `tmessage`.`message_id` AS `message_id`, `tmessage`.`subscriber_id` AS `Guest ID`, CASE WHEN `tsubscriber`.`name` IS NOT NULL THEN `tsubscriber`.`name` ELSE '-' END AS `Guest Name`, `tsubscriber`.`group_id` AS `group_id`, `tsubscriber`.`status` AS `Guest Status`, `tsubscriber_group`.`name` AS `Guest Group`, `tmessage`.`room_id` AS `Room ID`, `troom`.`name` AS `Room`, `tmessage`.`from` AS `From`, `tmessage`.`title` AS `Title`, `tmessage`.`message` AS `Message`, `tmessage`.`status` AS `Status`, `tmessage`.`create_date` AS `Create Date`, `tmessage`.`update_date` AS `Update Date` FROM `tmessage` LEFT JOIN `tsubscriber` ON `tmessage`.`subscriber_id` = `tsubscriber`.`subscriber_id` LEFT JOIN `tsubscriber_group` ON `tsubscriber`.`group_id` = `tsubscriber_group`.`group_id` LEFT JOIN `troom` ON `tmessage`.`room_id` = `troom`.`room_id` WHERE `tsubscriber`.`status` <> 'CHECKIN' ORDER BY `tmessage`.`message_id` DESC";
    const SQL_MODIFY = 'UPDATE tmessage SET subscriber_id=?, title=?, message=?, status=? WHERE message_id=?';
    const SQL_GET_ROOM_FOR_SELECT = 'SELECT room_id AS id, name AS value FROM troom ORDER BY name';
    const SQL_GET_SUBSCRIBER_ROOM = 'SELECT troom.room_id AS id, troom.name AS value FROM troom INNER JOIN tsubscriber_room ON troom.room_id = tsubscriber_room.room_id WHERE tsubscriber_room.subscriber_id = ?';
    const SQL_GET_SUBSCRIBER_BY_GROUP = "SELECT subscriber_id FROM tsubscriber WHERE status = 'CHECKIN' AND group_id = ?";

    protected $table      = 'tmessage';
    protected $primaryKey = 'message_id';
    protected $allowedFields = ['subscriber_id', 'room_id', 'title', 'message', 'status', 'from'];

//    protected $db;
    public $errCode;
    public $errMessage;

    public function get($messageId){
        return $this->find($messageId);
    }

    public function getAll(){
        $db = db_connect();
        return $db->query(self::SQL_GET_ALL)->getResult();
    }

    public function getFieldList(){
        return ['message_id', 'Guest ID', 'Guest Name', 'group_id', 'Guest Status', 'Guest Group', 'Room ID', 'Room', 'From', 'Title', 'Message', 'Status', 'Create Date', 'Update Date'];
    }

    public function getSubscribersByGroup($groupId){
        $db = db_connect();
        return $db->query(self::SQL_GET_SUBSCRIBER_BY_GROUP, [$groupId])->getResult('array');
    }

    // /**
    //  * return array utk keperluan selection
    //  *
    //  * @return mixed
    //  */
    // public function getAllActiveForSelect(){
    //     $result = $this->db->query(self::SQL_GET_ALL_ACTIVE_FOR_SELECT)->getResult();
    //     if(sizeof($result) > 0)
    //     {
    //         return $result;
    //     }
    //     return [];
    // }

    /**
     * get room for select based on subscriber_id
     */
    public function getRoomForSelect(){
        $db = db_connect();
        return $db->query(self::SQL_GET_ROOM_FOR_SELECT)->getResult('array');
    }

    public function getSubscriberRoom($subscriberId){
        $db = db_connect();
        return $db->query(self::SQL_GET_SUBSCRIBER_ROOM, [$subscriberId])->getResult('array');
    }

    /**
     * update dgn cara PDO, karena dgn cara ci4 tdk ada rowCount, shg tdk tahu apakah update berhasil atau tdk
     *
     * @param $groupId
     * @param $name
     * @param $status
     * @return \PDOException|\Exception|int => 0/1 = count update, -1 = pdo exception
     */
    public function modify($id, $data){
        $this->errCode = '';
        $this->errMessage = '';

        $subscriberId = $data['subscriber_id'];
        // $roomId = $data['room_id'];

        //xss
        $title = htmlentities($data['title'], ENT_QUOTES, 'UTF-8');//$data['title'];
        $message = htmlentities($data['message'], ENT_QUOTES, 'UTF-8');//$data['message'];

        $status = $data['status'];

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$subscriberId, $title, $message, $status, $id] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }
    public function modify_old($groupId, $value){
        return $this->update($groupId, $value);
    }

    public function add($value)  {

        //xss
        $value['title'] = htmlentities($value['title'], ENT_QUOTES, 'UTF-8');
        $value['message'] = htmlentities($value['message'], ENT_QUOTES, 'UTF-8');

        parent::insert($value);
        return $this->getInsertID();
    }

    public function remove($groupId){
        return $this->delete($groupId);
    }

    /**
     * di pakai utk datatables.js
     *
     * @return mixed
     */
    public function getSsp()
    {
        return $this->_getSspCustom(self::SQL_GET_ALL, $this->getFieldList());
    }

    // for Simple Settings
    public function getSspHistory()
    {
        return $this->_getSspCustom(self::SQL_GET_ALL_HISTORY, $this->getFieldList());
    }
}