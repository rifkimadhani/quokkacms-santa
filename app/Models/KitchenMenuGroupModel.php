<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-08 12:15:46
 */
namespace App\Models;

class KitchenMenuGroupModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tkitchen_menu_group WHERE (menu_group_id=?)';
    const SQL_MODIFY = 'UPDATE tkitchen_menu_group SET kitchen_id=?, group_name=?, description=?, url_thumb=?, service_open=?, service_close=?, seq=? WHERE (menu_group_id=?)';

    protected $table      = 'tkitchen_menu_group';
    protected $primaryKey = 'menu_group_id';
    protected $allowedFields = ['kitchen_id', 'group_name', 'description', 'url_thumb', 'service_open', 'service_close', 'seq'];

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

    public function get($menuGroupId)
    {
        $r = $this
            ->where('menu_group_id', $menuGroupId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['menu_group_id', 'kitchen_id', 'group_name', 'description', 'url_thumb', 'service_open', 'service_close', 'seq'];
    }

    public function add($value)  {

        try
        {
            $value['group_name'] = htmlentities($value['group_name'], ENT_QUOTES, 'UTF-8');
            $value['description'] = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');
            $value['url_thumb'] = htmlentities($value['url_thumb'], ENT_QUOTES, 'UTF-8');

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

        $menuGroupId = $value['menu_group_id'];

        $kitchenId = $value['kitchen_id'];
        $groupName = htmlentities($value['group_name'], ENT_QUOTES, 'UTF-8');
        $description = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');
        $urlThumb = htmlentities($value['url_thumb'], ENT_QUOTES, 'UTF-8');
        $serviceOpen = $value['service_open'];
        $serviceClose = $value['service_close'];
        $seq = $value['seq'];


        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$kitchenId, $groupName, $description, $urlThumb, $serviceOpen, $serviceClose, $seq, $menuGroupId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($menuGroupId){
        $r = $this
            ->where('menu_group_id', $menuGroupId)
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
