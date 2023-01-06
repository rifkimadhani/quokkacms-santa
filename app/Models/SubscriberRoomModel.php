<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

namespace App\Models;

class SubscriberRoomModel extends BaseModel
{
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

    public function getssp($subscriberId){
        return $this->_getSspComplex(self::VIEW, 'room_id', self::FIELD_LIST, 'subscriber_id=' . $subscriberId);
    }
}