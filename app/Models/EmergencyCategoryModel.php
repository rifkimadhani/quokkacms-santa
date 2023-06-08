<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-07 15:53:32
 */
namespace App\Models;

class EmergencyCategoryModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM temergency WHERE (emergency_code=?)';
    const SQL_MODIFY = 'UPDATE temergency SET name=?, url_image=? WHERE (emergency_code=?)';

    protected $table      = 'temergency';
    protected $primaryKey = 'emergency_code';
    protected $allowedFields = ['emergency_code', 'name', 'path', 'url_image', 'create_date', 'update_date'];

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

    public function get($emergencyCode)
    {
        $r = $this
            ->where('emergency_code', $emergencyCode)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['emergency_code', 'name', 'path', 'url_image', 'create_date', 'update_date'];
    }

    public function add($value)  {

        try
        {
            $value['emergency_code'] = htmlentities($value['emergency_code'], ENT_QUOTES, 'UTF-8');
            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            // $value['path'] = htmlentities($value['path'], ENT_QUOTES, 'UTF-8');
            $value['url_image'] = htmlentities($value['url_image'], ENT_QUOTES, 'UTF-8');

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

        $emergencyCode = $value['emergency_code'];

        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        // $path = htmlentities($value['path'], ENT_QUOTES, 'UTF-8');
        $urlImage = htmlentities($value['url_image'], ENT_QUOTES, 'UTF-8');
        // $createDate = $value['create_date'];
        // $updateDate = $value['update_date'];

        // if (strlen($createDate)==0) $createDate = null;
        // if (strlen($updateDate)==0) $updateDate = null;

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$name, $urlImage, $emergencyCode] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($emergencyCode){
        $r = $this
            ->where('emergency_code', $emergencyCode)
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
