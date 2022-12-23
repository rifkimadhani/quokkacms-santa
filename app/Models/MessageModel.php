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
    const VIEW = 'vmessage';

    const SQL_GET_ALL   = "SELECT tmessage.message_id,(tmessage.subscriber_id)'Guest ID',(CASE WHEN tsubscriber.`name` IS NOT NULL THEN tsubscriber.`name` ELSE 'Empty Guest' END)'Guest Name',(tmessage.room_id)'Room ID',(troom.`name`)'Room',(tmessage.`from`)'From',(tmessage.title)'Title',(tmessage.message)'Message',(tmessage.`status`)'Status',(tmessage.create_date)'Create Date',(tmessage.update_date)'Update Date' FROM tmessage LEFT JOIN tsubscriber ON tmessage.subscriber_id = tsubscriber.subscriber_id INNER JOIN troom ON tmessage.room_id = troom.room_id ORDER BY tmessage.message_id DESC";
    const SQL_MODIFY = 'UPDATE tmessage SET subscriber_id=?, room_id=?, title=?, message=?, status=? WHERE message_id=?';

    protected $table      = 'tmessage';
    protected $primaryKey = 'message_id';
    protected $allowedFields = ['title', 'message', 'status'];

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
        return ['message_id', 'Guest ID', 'Guest Name', 'Room ID', 'Room', 'From', 'Title', 'Message', 'Status', 'Create Date', 'Update Date'];
    }

    /**
     * return array utk keperluan selection
     *
     * @return mixed
     */
    public function getAllActiveForSelect(){
        $result = $this->db->query(self::SQL_GET_ALL_ACTIVE_FOR_SELECT)->getResult();
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
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
        $roomId = $data['room_id'];
        $title = $data['title'];
        $messge = $data['message'];
        $status = $data['status'];

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$subscriberId, $roomId, $title, $messge, $status, $id] );

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
        return parent::insert($value);
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
        require_once __DIR__ . '/../../library/ssp.class.php';

        $columns = [];
        $field_list = $this->getFieldList();

        foreach($field_list as $key => $value)
        {
            //add semua field ke columns
            $columns[] = ['db' =>$value,'dt'=>$key];
        }

        $con = array(
            "host"=>$_ENV['database.default.hostname'],
            "user"=>$_ENV['database.default.username'],
            "pass"=>$_ENV['database.default.password'],
            "db"=>$_ENV['database.default.database'],
        );

        return SSP::simple($_GET, $con, self::VIEW, $this->primaryKey, $columns);
    }
}