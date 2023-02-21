<?php
/**
 * Created by PageBuilder
 * Date: __TODAY__
 */
namespace App\Models;

class __Model__ extends BaseModel
{
    const SQL_MODIFY = 'UPDATE __table__ SET __sql_update_fields__ WHERE __pk__=?';

    protected $table      = '__table__';
    protected $primaryKey = '__pk__';
    protected $allowedFields = [__allowedFields__];

    public $errCode;
    public $errMessage;

    public function get($id)
    {
        return $this->find($id);
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
    public function modify($id, $__modify_fields__){
//        $name = htmlentities($name, ENT_QUOTES, 'UTF-8');//$_POST['name'];
        $this->errCode = '';
        $this->errMessage = '';

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$__modify_fields__, $id] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }

    public function add($value)  {
        return parent::insert($value);
    }

    public function remove($id){
        return $this->delete($id);
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
