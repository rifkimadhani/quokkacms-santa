<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

namespace App\Models;

class SubscriberRoomModel extends BaseModel
{
    const SQL_GET_ALL_BY_SUBSCRIBER = 'SELECT room_id FROM vsubscriber_room WHERE subscriber_id=?';
    const FIELD_LIST = ['subscriber_id', 'room_id', 'name', 'security_pin'];
    const VIEW = 'vsubscriber_room';

    public $errCode;
    public $errMessage;

    protected $table = 'tsubscriber_room';

    public function getFieldList(){
        return self::FIELD_LIST;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getAllBySubscriber($subscriberId){
        $db = db_connect();
        return $db->query(self::SQL_GET_ALL_BY_SUBSCRIBER, [$subscriberId])->getResult('array');
    }

    public function getssp($subscriberId){
        return $this->_getSspComplex(self::VIEW, 'room_id', self::FIELD_LIST, 'subscriber_id=' . $subscriberId);
    }
}