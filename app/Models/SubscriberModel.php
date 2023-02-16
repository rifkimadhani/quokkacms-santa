<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

namespace App\Models;

class SubscriberModel extends BaseModel
{
    const STATUS_VACANT= 'VACANT';
    const STATUS_OCCUPIED = 'OCCUPIED';
    const STATUS_CHECKIN = 'CHECKIN';
    const STATUS_CHECKOUT = 'CHECKOUT';

    const VIEW = 'vsubscriber';
    const PK = 'Subscriber ID'; //primary key yg di pergunakan pada SSP

    const SQL_ADD_1 = 'INSERT INTO tsubscriber (group_id, salutation, name, last_name, status, checkin_date) VALUES (?,?,?,?,?,now())';
    const SQL_ADD_2 = 'UPDATE troom SET subscriber_id=?, status=? WHERE status=\'VACANT\' AND room_id=?';
    const SQL_ADD_3 = 'INSERT INTO tsubscriber_room (subscriber_id, room_id, checkin_date, status) VALUES (?, ?, now(), ?)';

    const SQL_REMOVE_1 = 'DELETE FROM tsubscriber WHERE subscriber_id=?';
    const SQL_REMOVE_2 = 'DELETE FROM tsubscriber_room WHERE subscriber_id=?';
    const SQL_REMOVE_3 = 'UPDATE troom SET status=?, subscriber_id=null WHERE subscriber_id=?';

    const SQL_CHECKOUT_1 = 'UPDATE tsubscriber_room SET status=?, checkout_date=now() WHERE subscriber_id=? AND room_id=? AND status=?';
    const SQL_CHECKOUT_2 = 'UPDATE troom SET status=?, subscriber_id=null WHERE subscriber_id=? AND room_id=?';
    const SQL_CHECKOUT_3 = 'SELECT COUNT(*) as total FROM troom WHERE subscriber_id=?';
    const SQL_CHECKOUT_4 = 'UPDATE tsubscriber SET status=?, checkout_date=now() WHERE subscriber_id=?';

    const SQL_GET_FOR_SELECT = 'SELECT subscriber_id AS id, CONCAT(name, \' \', last_name) AS value FROM tsubscriber WHERE status=\'CHECKIN\' ORDER BY name';

    const SQL_MODIFY = 'UPDATE tsubscriber SET group_id=?, salutation=?, name=?, last_name=? WHERE subscriber_id=?';

    public $errCode;
    public $errMessage;

    protected $table = 'tsubscriber';
    protected $primaryKey = 'subscriber_id';
    protected $allowedFields = ['group_id', 'salutation', 'name', 'last_name', 'status', 'checkin_date', 'checkout_date'];

    public function checkin($value)  {

        if (empty($value['group_id'])) $groupId = null; else $groupId = $value['group_id'];

        //htmlentities utk xss
        $salutation = htmlentities($value['salutation'], ENT_QUOTES, 'UTF-8');//$value['salutation'];
        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');//$value['name'];
        $lastName = htmlentities($value['last_name'], ENT_QUOTES, 'UTF-8');//$value['last_name'];
        $status = 'CHECKIN';



        $rooms = $value['room_id'];

        try{
            $db = $this->openPdo();
            $db->beginTransaction();

            //insert tsubscriber
            $stmt = $db->prepare(self::SQL_ADD_1);
            $stmt->execute( [$groupId, $salutation, $name, $lastName, $status] );
            $subscriberId = $db->lastInsertId();

            $status = self::STATUS_OCCUPIED;
            $countSuccess = 0;
            foreach ($rooms as $roomId){
                //update room
                $stmt = $db->prepare(self::SQL_ADD_2);
                $stmt->execute( [$subscriberId, $status, $roomId] );
                $countSuccess += $stmt->rowCount();

                //insert tsubscriber_room
                $stmt = $db->prepare(self::SQL_ADD_3);
                $stmt->execute( [$subscriberId, $roomId, self::STATUS_CHECKIN] );
            }

            log_message('error', "addCheckin countSuccess={$countSuccess}");

            if ($countSuccess!=sizeof($rooms)){
                $db->rollBack();
                return 0;
            }

            $db->commit();

            return $countSuccess;

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }

        ////////////////////////////////////////

    }

    public function get($id){
        return $this->find($id);
    }

    public function getAll(){
        return $this->findAll();
    }

    /**
     * field list ini di pakai utk keperluan datatable,
     * shg bila ada penambahan field pada table/view tdk mempengaruhi output datatable
     *
     * @return array
     */
    public function getFieldList(){
        return ['Subscriber ID', 'Full name', 'theme', 'package', 'Room', 'status', 'Checkin Date', 'Checkout Date', 'Create Date', 'Update Date', 'group_name'];
    }

    /**
     * semua subscriber yg berstatus checkin
     * return khusus di format utk selection
     *
     * @return array di format khusus utk selection pada form
     */
    public function getCheckinForSelect(){
        $db = db_connect();
        return $db->query(self::SQL_GET_FOR_SELECT)->getResult('array');
    }

    public function getSsp()
    {
        return $this->_getSsp(self::VIEW, self::PK, $this->getFieldList());
    }

    /**
     * hapus record pada tsubscriber & tsubscriber_room
     * reset record pada troom
     *
     * @param $subscriberId
     */
    public function remove($subscriberId)  {


        try{
            $db = $this->openPdo();
            $db->beginTransaction();

            //hapus tsubscriber
            $stmt = $db->prepare(self::SQL_REMOVE_1);
            $stmt->execute( [$subscriberId] );

            //hapus tsubscriber_room
            $stmt = $db->prepare(self::SQL_REMOVE_2);
            $stmt->execute( [$subscriberId] );

            //hapus troom
            $stmt = $db->prepare(self::SQL_REMOVE_3);
            $stmt->execute( [self::STATUS_VACANT, $subscriberId] );

            $db->commit();

            return 1;

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }

    /**
     * @param $subscriberId
     * @return int
     */
    public function checkout($subscriberId, $rooms)  {

        try{
            $db = $this->openPdo();
            $db->beginTransaction();

            foreach ($rooms as $room){
                $roomId = $room['room_id'];

                log_message('error', "roomId={$roomId}");


                //rubah tsubscriber_room dgn status CHECKIN --> CHECKOUT
                $stmt = $db->prepare(self::SQL_CHECKOUT_1);
                $stmt->execute( [self::STATUS_CHECKOUT, $subscriberId, $roomId, self::STATUS_CHECKIN] );

                //rubah troom dari OCCUPIED --> VACANT
                $stmt = $db->prepare(self::SQL_CHECKOUT_2);
                $stmt->execute( [self::STATUS_VACANT, $subscriberId, $roomId] );
            }

            //hitung ada brp room yg masih link ke subscriber_id
            $stmt = $db->prepare(self::SQL_CHECKOUT_3);
            $stmt->execute( [$subscriberId] );

            $rows = $stmt->fetchAll();

            $count = (int) $rows[0]['total'];

            log_message('error', "rows=" . $count);


            //apabila room sdh tdk ada yg terhubung ke subscriber,
            //maka set CHECKOUT
            if ($count==0){
                log_message('error', "CHECKOUT subscriber");

                $stmt = $db->prepare(self::SQL_CHECKOUT_4);
                $stmt->execute( [self::STATUS_CHECKOUT, $subscriberId] );
            }

            $db->commit();

            return 1;

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }

    public function modify($subscriberId, $data){
        $this->errCode = '';
        $this->errMessage = '';

        if (empty($data['group_id'])) $group_id = null; else $group_id = $data['group_id'];

        //xss
        $salutation = htmlentities($data['salutation'], ENT_QUOTES, 'UTF-8');//$data['salutation'];
        $name = htmlentities($data['name'], ENT_QUOTES, 'UTF-8');//$data['name'];
        $lastName = htmlentities($data['last_name'], ENT_QUOTES, 'UTF-8');//$data['last_name'];

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$group_id, $salutation, $name, $lastName, $subscriberId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }
}