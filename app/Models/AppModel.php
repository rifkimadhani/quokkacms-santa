<?php
/**
 * Created by PageBuilder
 * Date: 2023-03-01 10:20:34
 */
namespace App\Models;

class AppModel extends BaseModel
{
    // const SQL_GET_ALL ='SELECT * FROM tapp WHERE app_id = ?';
    // const SQL_GET_LATEST ='SELECT B.* FROM(SELECT app_id,MAX(version_code)version_code FROM `tapp` GROUP BY tapp.app_id ORDER BY tapp.app_id ASC )A LEFT JOIN tapp B ON (A.app_id = B.app_id AND A.version_code = B.version_code)';
    // const SQL_GET_VERSION ='SELECT * FROM {$this->table} WHERE version_code = ? AND app_id = ?';
    // const SQL_MODIFY = 'UPDATE tapp SET version_code=? WHERE app_id=?';

    protected $table      = 'tapp';
    protected $primaryKey = 'id';
    protected $allowedFields = ['app_id', 'version_code', 'version_name','main_activity'];

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
        return ['ID','app_id','version_code', 'version_name','main_activity'];
    }

    // public function findOneBy($namaColumn,$valueColumn)
    // {
    //     $result = $this->db->get_where($this->table, array($namaColumn => $valueColumn), 1, 0)->result_array();
    //     if($result AND count($result) > 0)
    //     {
    //         return $result[0]; 
    //     }
    //     return false;
    // }

    // public function getOneLatesApkEachGroup()
    // {
    //     $db = db_connect();
    //     return $db->query(self::SQL_GET_LATEST)->getResult('array');
    // }

    // public function getAllEachGroup($app_id)
	// {
	// 	$db = db_connect();
    //     return $db->query(self::SQL_GET_ALL, array($app_id))->getResult('array');
        
    // }

    // private function getOneByVersionCode($version_code,$app_id)
	// {
	// 	$db = db_connect();
    //     return $db->query(self::SQL_GET_VERSION, array($version_code,$app_id))->getResult('array');
    // }


    /**
     * update dgn cara PDO, karena dgn cara ci4 tdk ada rowCount, shg tdk tahu apakah update berhasil atau tdk
     *
     * @param $id
     * @param $name
     * @param $status
     * @return \PDOException|\Exception|int => 0/1 = count update, -1 = pdo exception
     */
    // public function modify($id, $version_code){
    //     // $name = htmlentities($name, ENT_QUOTES, 'UTF-8');//$_POST['name'];
    //     $this->errCode = '';
    //     $this->errMessage = '';

    //     try{
    //         $pdo = $this->openPdo();
    //         $stmt = $pdo->prepare(self::SQL_MODIFY);
    //         $stmt->execute( [$version_code, $id] );

    //         return $stmt->rowCount();

    //     }catch (\PDOException $e){
    //         log_message('error', json_encode($e));
    //         $this->errCode = $e->getCode();
    //         $this->errMessage = $e->getMessage();
    //         return -1;
    //     }
    // }

    // public function add($value)  {
    //     return parent::insert($value);
    // }

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
