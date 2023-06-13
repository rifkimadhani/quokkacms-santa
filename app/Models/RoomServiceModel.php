<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-12 12:48:42
 */
namespace App\Models;

class RoomServiceModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM vroomservice_order WHERE order_code=?';
    const SQL_MODIFY_STATUS = 'UPDATE troomservice SET status=? WHERE order_code=?';

    protected $view      = 'vroomservice_order';
    protected $table      = 'troomservice';
    protected $primaryKey = 'order_code';
    protected $allowedFields = ['order_code', 'subscriber_id', 'room_id', 'kitchen_id', 'kitchen_name', 'order_date', 'purchase_amount', 'percent_service_charge', 'service_charge', 'percent_tax', 'tax', 'notes', 'status', 'payment_type', 'delivery_fee'];

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

    public function get($orderCode)
    {
        $db = db_connect();
        return $db->query(self::SQL_GET, [$orderCode])->getResult('array')[0];
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['order_code', 'subscriber_id', 'salutation', 'first_name', 'last_name', 'status_checkin', 'room_id', 'name', 'location', 'status', 'kitchen_id', 'notes', 'kitchen_name', 'order_date'];
    }

    public function add($value)  {

        try
        {
            $value['kitchen_name'] = htmlentities($value['kitchen_name'], ENT_QUOTES, 'UTF-8');
            $value['purchase_amount'] = htmlentities($value['purchase_amount'], ENT_QUOTES, 'UTF-8');
            $value['percent_service_charge'] = htmlentities($value['percent_service_charge'], ENT_QUOTES, 'UTF-8');
            $value['service_charge'] = htmlentities($value['service_charge'], ENT_QUOTES, 'UTF-8');
            $value['percent_tax'] = htmlentities($value['percent_tax'], ENT_QUOTES, 'UTF-8');
            $value['tax'] = htmlentities($value['tax'], ENT_QUOTES, 'UTF-8');
            $value['notes'] = htmlentities($value['notes'], ENT_QUOTES, 'UTF-8');
            $value['status'] = htmlentities($value['status'], ENT_QUOTES, 'UTF-8');
            $value['payment_type'] = htmlentities($value['payment_type'], ENT_QUOTES, 'UTF-8');
            $value['delivery_fee'] = htmlentities($value['delivery_fee'], ENT_QUOTES, 'UTF-8');

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
    public function modifyStatus($orderCode, $newStatus){

        $this->errCode = '';
        $this->errMessage = '';

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY_STATUS);
            $stmt->execute( [$newStatus, $orderCode] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }

    public function remove($orderCode){
        $r = $this
            ->where('order_code', $orderCode)
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
        $where = 'status IN (\'NEW\', \'PROCESS\', \'ENROUTE\')';
        return $this->_getSspComplex($this->view, $this->primaryKey, $this->getFieldList(), $where);
    }

    public function getSspHistory()
    {
        $where = 'status IN (\'DELIVERED\', \'\')';
        return $this->_getSspComplex($this->view, $this->primaryKey, $this->getFieldList(), $where);
    }
}
