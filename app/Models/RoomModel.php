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
    const SQL_GET_FOR_SELECT = 'SELECT room_id AS id, name AS value FROM troom ORDER BY name';
    const SQL_GET_VACANT_FOR_SELECT = 'SELECT room_id AS id, name AS value FROM troom WHERE status=\'VACANT\' ORDER BY name';

    protected $table      = 'troom';
    protected $primaryKey = 'room_id';
    protected $allowedFields = [];

    public function get($id){
        return $this->find($id);
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
}