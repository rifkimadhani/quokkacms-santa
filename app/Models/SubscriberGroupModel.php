<?php

/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/19/2022
 * Time: 9:12 AM
 */
namespace App\Models;

use App\Libraries\SSP;
use CodeIgniter\Model;

class SubscriberGroupModel extends Model
{
    protected $table      = 'tsubscriber_group';
    protected $primaryKey = 'group_id';
    protected $allowedFields = ['name', 'status'];

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

    public function modify($groupId, $value){
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

        $this->db = \Config\Database::connect();

        $columns = [];
        $field_list = $this->getFieldList();

        foreach($field_list as $key => $value)
        {
            //add semua field ke columns
            $columns[] = ['db' =>$value,'dt'=>$key];
        }

        $con = array(
            "host"=>'localhost',
            "user"=>'web',
            "pass"=>'P@ssword%',
            "db"=>'alpha',
        );
//        $con = array(
//            "host"=>$this->db->hostname,
//            "user"=>$this->db->username,
//            "pass"=>$this->db->password,
//            "db"=>$this->db->database,
//        );

        return SSP::simple($_GET, $con, $this->table, $this->primaryKey, $columns);
    }
}