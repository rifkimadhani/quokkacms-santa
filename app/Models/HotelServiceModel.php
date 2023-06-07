<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-06 16:24:59
 */
namespace App\Models;

class HotelServiceModel extends BaseModel
{
    const STATUS_NEW = 'NEW';
    const STATUS_ACK = 'ACK';
    const STATUS_CANCEL = 'CANCEL'; //user cancel
    const STATUS_FINISH = 'FINISH';

    const SQL_GET = 'SELECT * FROM vsubscriber_hotel_service WHERE task_id=?';
    const SQL_MODIFY_STATUS = 'UPDATE tsubscriber_hotel_service SET status=? WHERE task_id=?';
//    const SQL_GET_ALL_ACTIVE = "SELECT * FROM vsubscriber_hotel_service WHERE status IN ('NEW', 'ACK') ORDER BY status DESC, update_date DESC";
//    const SQL_GET_ALL_INACTIVE= "SELECT * FROM vsubscriber_hotel_service WHERE status NOT IN ('NEW', 'ACK') ORDER BY update_date DESC, FIELD(status, 'FINISH', 'CANCEL')";

    protected $view = 'vsubscriber_hotel_service';
    protected $table      = 'tsubscriber_hotel_service';
    protected $primaryKey = 'task_id';
    protected $allowedFields = ['room_id', 'subscriber_id', 'type', 'status', 'create_date', 'update_date', 'data'];

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

    public function get($taskId)
    {
        $db = db_connect();
        return $db->query(self::SQL_GET, [$taskId])->getResult('array')[0];

    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['task_id', 'room_id', 'room_name', 'subscriber_id', 'status_checkin', 'name', 'last_name', 'salutation', 'type', 'status', 'data', 'update_date'];
    }

    public function add($value)  {

        try
        {
            $value['type'] = htmlentities($value['type'], ENT_QUOTES, 'UTF-8');
            $value['status'] = htmlentities($value['status'], ENT_QUOTES, 'UTF-8');
            $value['data'] = htmlentities($value['data'], ENT_QUOTES, 'UTF-8');

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
     * @param $value
     * @return \PDOException|\Exception|int => 0/1 = count update, -1 = pdo exception
     */
    public function modifyStatus($value, $newStatus){

        $this->errCode = '';
        $this->errMessage = '';

        $taskId = $value['task_id'];

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY_STATUS);
            $stmt->execute( [$newStatus, $taskId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($taskId){
        $r = $this
            ->where('task_id', $taskId)
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
        $where = 'status IN (\'NEW\', \'ACK\')';
        return $this->_getSspComplex($this->view, $this->primaryKey, $this->getFieldList(), $where);
    }

    public function getSspOld()
    {
        $where = 'status IN (\'FINISH\', \'CANCEL\')';
        return $this->_getSspComplex($this->view, $this->primaryKey, $this->getFieldList(), $where);
    }
}
