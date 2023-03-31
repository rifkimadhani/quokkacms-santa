<?php
/**
 * Created by PageBuilder
 * Date: 2023-03-31 13:21:41
 */
namespace App\Models;

use App\Libraries\SSP;

class ElementModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM telement WHERE (element_id=?)';
    const SQL_MODIFY = "UPDATE telement SET `name`=?, `group`=?, `type`=? WHERE (element_id=?)";

    protected $table      = 'telement';
    protected $primaryKey = 'element_id';
    protected $allowedFields = ['element_id', 'name', 'group', 'type', 'width', 'height', 'create_date', 'update_date'];

    public $errCode;
    public $errMessage;

//    public function get2($adminId, $username){
//        $r = $this
//            ->where('admin_id', $adminId)
//            ->where('username', $username)
//            ->find();
//        if ($r!=null) return $r[0];
//        return null;
//    }

    public function get($elementId)
    {
        $r = $this
            ->where('element_id', $elementId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['element_id', 'name', 'group', 'type', 'width', 'height', 'create_date', 'update_date'];
    }

    public function add($value)  {

        try
        {
            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            $value['group'] = htmlentities($value['group'], ENT_QUOTES, 'UTF-8');
            $value['type'] = htmlentities($value['type'], ENT_QUOTES, 'UTF-8');

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

        $elementId = $value['element_id'];

        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        $group = htmlentities($value['group'], ENT_QUOTES, 'UTF-8');
        $type = htmlentities($value['type'], ENT_QUOTES, 'UTF-8');

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$name, $group, $type, $elementId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($elementId){
        $r = $this
            ->where('element_id', $elementId)
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
