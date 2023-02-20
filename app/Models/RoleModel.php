<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/17/2023
 * Time: 2:33 PM
 */

namespace App\Models;

class RoleModel extends BaseModel
{
    const SQL_GET_ALL_FOR_SELECT = 'SELECT role_id AS id, role_name AS value FROM trole';

    public function getAllForSelect(){
        $result = $this->db->query(self::SQL_GET_ALL_FOR_SELECT)->getResult('array');
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }

}