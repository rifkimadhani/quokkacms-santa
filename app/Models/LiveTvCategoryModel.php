<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-05 10:36:36
 */
namespace App\Models;

class LiveTvCategoryModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tlivetv_category WHERE (livetv_category_id=?)';
    const SQL_MODIFY = 'UPDATE tlivetv_category SET category=?, create_date=?, update_date=? WHERE (livetv_category_id=?)';

    protected $table      = 'tlivetv_category';
    protected $primaryKey = 'livetv_category_id';
    protected $allowedFields = ['category', 'create_date', 'update_date'];

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

    public function get($livetvCategoryId)
    {
        $r = $this
            ->where('livetv_category_id', $livetvCategoryId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['livetv_category_id', 'category', 'create_date', 'update_date'];
    }

    public function add($value)  {

        try
        {
            $value['category'] = htmlentities($value['category'], ENT_QUOTES, 'UTF-8');

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

        $livetvCategoryId = $value['livetv_category_id'];

        $category = htmlentities($value['category'], ENT_QUOTES, 'UTF-8');
        $createDate = $value['create_date'];
        $updateDate = $value['update_date'];

        if (strlen($createDate)==0) $createDate = null;
        if (strlen($updateDate)==0) $updateDate = null;

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$category, $createDate, $updateDate, $livetvCategoryId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($livetvCategoryId){
        $r = $this
            ->where('livetv_category_id', $livetvCategoryId)
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
