<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-08 12:29:19
 */
namespace App\Models;

class KitchenMenuModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tkitchen_menu WHERE (menu_id=?)';
    const SQL_MODIFY = 'UPDATE tkitchen_menu SET kitchen_id=?, menu_group_id=?, name=?, description=?, price=?, in_room_dining=?, url_image=? WHERE (menu_id=?)';

    protected $table      = 'tkitchen_menu';
    protected $primaryKey = 'menu_id';
    protected $allowedFields = ['kitchen_id', 'menu_group_id', 'name', 'description', 'price', 'in_room_dining', 'url_image'];

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

    public function get($menuId)
    {
        $r = $this
            ->where('menu_id', $menuId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['menu_id', 'kitchen_id', 'menu_group_id', 'name', 'description', 'price', 'currency', 'currency_sign', 'in_room_dining', 'url_image', 'create_date', 'update_date'];
    }

    public function add($value)  {

        try
        {
            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            $value['description'] = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');
            $value['price'] = htmlentities($value['price'], ENT_QUOTES, 'UTF-8');
            $value['in_room_dining'] = htmlentities($value['in_room_dining'], ENT_QUOTES, 'UTF-8');

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

        $menuId = $value['menu_id'];

        $kitchenId = $value['kitchen_id'];
        $menuGroupId = $value['menu_group_id'];
        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        $description = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');
        $price = $value['price'];
        $inRoomDining = (int) $value['in_room_dining'];
        $urlImage = $value['url_image'];

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$kitchenId, $menuGroupId, $name, $description, $price, $inRoomDining, $urlImage, $menuId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($menuId){
        $r = $this
            ->where('menu_id', $menuId)
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
