<?php
/**
 * Created by PageBuilder
 * Date: __TODAY__
 */
namespace App\Models;

class __Model__ extends BaseModel
{
    const SQL_GET = 'SELECT * FROM __table__ WHERE __pk_where__';
    const SQL_MODIFY = 'UPDATE __table__ SET __sql_update_fields__ WHERE __pk_where__';

    protected $table      = '__table__';
    protected $primaryKey = '__pk__';
    protected $allowedFields = [__allowedFields__];

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

    public function get($__pk_parameter__)
    {
        $r = $this
            //__get_cmd__
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return [__fieldList__];
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

//__field_declare__
//__field_extra__
        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$__modify_fields__, $__pk_field__] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }

    public function add($value)  {

//__field_declare_add__
        return parent::insert($value);
    }

    public function remove($__pk_parameter__){
        $r = $this
            //__get_cmd__
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
