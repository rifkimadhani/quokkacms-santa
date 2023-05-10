<?php
/**
 * Created by PageBuilder
 * Date: 2023-05-10 11:25:21
 */
namespace App\Models;

class RoomTypeModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM troom_type WHERE (room_type_id=?)';
    const SQL_MODIFY = 'UPDATE troom_type SET type=?, type_order=? WHERE (room_type_id=?)';

    protected $table      = 'troom_type';
    protected $primaryKey = 'room_type_id';
    protected $allowedFields = ['room_type_id', 'type', 'type_order'];

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

    public function get($roomTypeId)
    {
        $r = $this
            ->where('room_type_id', $roomTypeId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['room_type_id', 'type', 'type_order'];
    }

    public function add($value)  {

        try
        {
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

        $roomTypeId = $value['room_type_id'];

        $type = htmlentities($value['type'], ENT_QUOTES, 'UTF-8');
        $typeOrder = $value['type_order'];


        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$type, $typeOrder, $roomTypeId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($roomTypeId){
        $r = $this
            ->where('room_type_id', $roomTypeId)
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
