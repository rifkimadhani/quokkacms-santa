<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

namespace App\Models;

use App\Libraries\SSP;

class SubscriberModel extends BaseModel
{
    const SQL_GET_FOR_SELECT = 'SELECT subscriber_id AS id, CONCAT(name, \' \', last_name) AS value FROM tsubscriber WHERE status=\'CHECKIN\' ORDER BY name';

    protected $table      = 'tsubscriber';
    protected $primaryKey = 'subscriber_id';
    protected $allowedFields = [];

    public function get($id){
        return $this->find($id);
    }

    public function getAll(){
        return $this->findAll();
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
}