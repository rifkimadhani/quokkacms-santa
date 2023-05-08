<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 1:29 PM
 */

namespace App\Models;

use App\Libraries\SSP;

class RoomModel extends BaseModel
{
    const SQL_GET_BY_SUBSCRIBER = 'SELECT room_id AS id, name AS value FROM troom WHERE subscriber_id=?';
    const SQL_GET_FOR_SELECT = 'SELECT room_id AS id, name AS value FROM troom ORDER BY name';
    const SQL_GET_VACANT_FOR_SELECT = 'SELECT room_id AS id, name AS value FROM troom WHERE status=\'VACANT\' ORDER BY name';
    const SQL_GET = "SELECT troom.room_id, troom.name, troom.location, troom.room_type_id, troom_type.type AS `type`, troom.theme_id, ttheme.name AS `theme`, troom.package_id, tpackage.name AS `package`, troom.security_pin, troom.status, troom.subscriber_id, CONCAT(tsubscriber.salutation, tsubscriber.name, tsubscriber.last_name) AS `guest`, troom.create_date, troom.update_date FROM troom LEFT JOIN troom_type ON troom.room_type_id = troom_type.room_type_id LEFT JOIN ttheme ON troom.theme_id = ttheme.theme_id LEFT JOIN tpackage ON troom.package_id = tpackage.package_id LEFT JOIN tsubscriber ON troom.subscriber_id = tsubscriber.subscriber_id";
    
    const SQL_MODIFY = 'UPDATE troom SET name=?, location=?, room_type_id=?, theme_id=?, package_id=?, subscriber_id=?, create_date=?, update_date=?, status=?, security_pin=? WHERE (room_id=?)';

    protected $table      = 'troom';
    protected $primaryKey = 'room_id';
    protected $allowedFields = ['name', 'location', 'room_type_id', 'theme_id', 'package_id', 'subscriber_id', 'create_date', 'update_date', 'status', 'security_pin'];

    public function get($id){
        return $this->find($id);
    }

    public function getBySubscriber($subscriberId){
        return $this->where('subscriber_id', $subscriberId)->findAll();


        // return $this->db->query(self::SQL_GET_BY_SUBSCRIBER, ['subscriber_id'=>$subscriberId])->getResult('array');
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getForSelect(){
        $db = db_connect();
        return $db->query(self::SQL_GET_FOR_SELECT)->getResult('array');
    }

    public function getVacantForSelect(){
        $db = db_connect();
        return $db->query(self::SQL_GET_VACANT_FOR_SELECT)->getResult('array');
    }

    public function getFieldList(){
        // return ['room_id', 'name', 'location', 'room_type_id', 'type', 'theme_id', 'theme', 'package_id', 'package', 'security_pin', 'status', 'subscriber_id', 'guest',  'create_date', 'update_date'];
        return ['room_id', 'name', 'location', 'room_type_id', 'theme_id', 'package_id', 'security_pin', 'status', 'subscriber_id',  'create_date', 'update_date'];
    }

    public function add($value)  {

        try
        {
            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            $value['location'] = htmlentities($value['location'], ENT_QUOTES, 'UTF-8');
            $value['status'] = htmlentities($value['status'], ENT_QUOTES, 'UTF-8');
            $value['security_pin'] = htmlentities($value['security_pin'], ENT_QUOTES, 'UTF-8');

            parent::insert($value);

        }
        catch (\Exception $e){
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();

            return 0;
        }

        return $this->db->affectedRows();
    }

    /**
     * update dgn cara PDO, karena dgn cara ci4 tdk ada rowCount, shg tdk tahu apakah update berhasil atau tdk
     *
     * @param $id
     * @param $name
     * @param $status
     * @return \PDOException|\Exception|int => 0/1 = count update, -1 = pdo exception
     */
    public function modify($value){

        $this->errCode = '';
        $this->errMessage = '';

        $roomId = $value['room_id'];

        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        $location = htmlentities($value['location'], ENT_QUOTES, 'UTF-8');
        $roomTypeId = $value['room_type_id'];
        $themeId = $value['theme_id'];
        $packageId = $value['package_id'];
        $subscriberId = $value['subscriber_id'];
        // $createDate = $value['create_date'];
        $updateDate = $value['update_date'];
        $status = htmlentities($value['status'], ENT_QUOTES, 'UTF-8');
        $securityPin = htmlentities($value['security_pin'], ENT_QUOTES, 'UTF-8');

        // if (strlen($createDate)==0) $createDate = null;
        // if (strlen($updateDate)==0) $updateDate = null;

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$name, $location, $roomTypeId, $themeId, $packageId, $subscriberId, $status, $securityPin, $roomId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }

    public function remove($roomId){
        $r = $this
            ->where('room_id', $roomId)
            ->delete();

        return $this->db->affectedRows();
    }

    /**
     * di pakai utk datatables.js
     *
     * @return mixed
     */
    public function getSsp()
    {
        return $this->_getSsp($this->table, $this->primaryKey, $this->getFieldList());
    }
}