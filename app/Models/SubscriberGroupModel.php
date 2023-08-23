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

class SubscriberGroupModel extends BaseModel
{
    const SQL_MODIFY = 'UPDATE tsubscriber_group SET name=?, status=? WHERE group_id=?';
    const SQL_GET = 'SELECT * FROM tsubscriber_group WHERE group_id=?';
    const SQL_GET_ALL_ACTIVE_FOR_SELECT = 'SELECT group_id as id, name as value FROM tsubscriber_group WHERE status=\'ACTIVE\' ORDER BY name';

    protected $table      = 'tsubscriber_group';
    protected $primaryKey = 'group_id';
    protected $allowedFields = ['name', 'status'];

//    protected $db;
    public $errCode;
    public $errMessage;

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
        $result = $this->db->query(self::SQL_GET_ALL_ACTIVE_FOR_SELECT)->getResult('array');
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
    public function modify($groupId, $name, $status){
        $name = htmlentities($name, ENT_QUOTES, 'UTF-8');//$_POST['name'];
        $this->errCode = '';
        $this->errMessage = '';

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$name, $status, $groupId] );

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
        $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');//$_POST['name'];
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
    public function getSsp($isActive)
    {
        $where = ($isActive) ? 'status=\'ACTIVE\'' : 'status=\'INACTIVE\'';
        return $this->_getSspComplex($this->table, $this->primaryKey, $this->getFieldList(), $where);
    }
}