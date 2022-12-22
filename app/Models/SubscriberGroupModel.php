<?php

/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/19/2022
 * Time: 9:12 AM
 */
namespace App\Models;

use CodeIgniter\Model;

class SubscriberGroupModel extends Model
{
    protected $table      = 'tsubscriber_group';
    protected $primaryKey = 'group_id';

    const SQL_GET = 'SELECT * FROM tsubscriber_group WHERE group_id=?';
    const SQL_GET_ALL_ACTIVE_FOR_SELECT = 'SELECT group_id as id, name as value FROM tsubscriber_group WHERE status=\'ACTIVE\' ORDER BY name';

    protected $db;
    protected $errCode;
    protected $errMessage;

    public function get($groupId)
    {
        return $this->find($groupId);

//        $result = $this->db->query(self::SQL_GET, [$groupId])->result_array();
//        if (count($result)>0) return $result[0];
//        return false;
    }

    public function getAll(){
        return $this->db->get(self::TABLE)->result();
    }

    public function getFieldList(){
        return ['group_id', 'name', 'status', 'create_date', 'update_date'];
    }

    /**
     * return array utk keperluan selection
     *
     * @return mixed
     */
    public function getAllActiveForSelect(){
        $result = $this->db->query(self::SQL_GET_ALL_ACTIVE_FOR_SELECT)->result_array();
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }

    public function updateData($groupId, $value){

        $this->db->update(self::TABLE, $value, array(self::PK => $groupId));

        //check error
        $err = $this->db->error();
        if ($err['code']>0) {
            log_message('error', $err['message']);
            return $err['message'];
        }

        return '';
    }

    public function insertData($value)  {

        parent::insert($value);

//        //check error
//        $err = $db->error();
//
//        if ($err['code']>0) {
//            log_message('error', $err['message']);
//            return $err['message'];
//        }
//
//        return $err;
    }

    public function deleteData($groupId){
        $this->db->db_debug = FALSE; //disable debugging

        $r = $this->db->delete(self::TABLE, [self::PK => $groupId]);

        //check error
        $err = $this->db->error();
        if ($err['code']>0) {
            $this->errCode = $err['code'];
            $this->errMessage = $err['message'];
//            log_message('error', $err['message']);
        }

        return $err;
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

        return SSP::simple($_GET, $this->getFullConnection(), self::TABLE, self::PK, $columns);
    }
}