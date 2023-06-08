<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-08 12:02:59
 */
namespace App\Models;

class KitchenModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tkitchen WHERE (kitchen_id=?)';
    const SQL_MODIFY = 'UPDATE tkitchen SET name=?, opening_hours=?, food_type=?, url_image_background=?, delivery_fee=?, currency=?, currency_sign=?, percent_service_charge=?, percent_tax=? WHERE (kitchen_id=?)';

    protected $table      = 'tkitchen';
    protected $primaryKey = 'kitchen_id';
    protected $allowedFields = ['name', 'opening_hours', 'food_type', 'url_image_background', 'delivery_fee', 'currency', 'currency_sign', 'percent_service_charge', 'percent_tax'];

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

    public function get($kitchenId)
    {
        $r = $this
            ->where('kitchen_id', $kitchenId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['kitchen_id', 'name', 'opening_hours', 'food_type', 'url_image_background', 'delivery_fee', 'currency', 'currency_sign', 'percent_service_charge', 'percent_tax', 'create_date', 'update_date'];
    }

    public function add($value)  {

        try
        {
            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            $value['opening_hours'] = htmlentities($value['opening_hours'], ENT_QUOTES, 'UTF-8');
            $value['food_type'] = htmlentities($value['food_type'], ENT_QUOTES, 'UTF-8');
            $value['url_image_background'] = htmlentities($value['url_image_background'], ENT_QUOTES, 'UTF-8');
            $value['delivery_fee'] = htmlentities($value['delivery_fee'], ENT_QUOTES, 'UTF-8');
            $value['currency'] = htmlentities($value['currency'], ENT_QUOTES, 'UTF-8');
            $value['currency_sign'] = htmlentities($value['currency_sign'], ENT_QUOTES, 'UTF-8');
            $value['percent_service_charge'] = htmlentities($value['percent_service_charge'], ENT_QUOTES, 'UTF-8');
            $value['percent_tax'] = htmlentities($value['percent_tax'], ENT_QUOTES, 'UTF-8');

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

        $kitchenId = $value['kitchen_id'];

        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        $openingHours = htmlentities($value['opening_hours'], ENT_QUOTES, 'UTF-8');
        $foodType = htmlentities($value['food_type'], ENT_QUOTES, 'UTF-8');
        $urlImageBackground = htmlentities($value['url_image_background'], ENT_QUOTES, 'UTF-8');
        $deliveryFee = htmlentities($value['delivery_fee'], ENT_QUOTES, 'UTF-8');
        $currency = htmlentities($value['currency'], ENT_QUOTES, 'UTF-8');
        $currencySign = htmlentities($value['currency_sign'], ENT_QUOTES, 'UTF-8');
        $percentServiceCharge = htmlentities($value['percent_service_charge'], ENT_QUOTES, 'UTF-8');
        $percentTax = htmlentities($value['percent_tax'], ENT_QUOTES, 'UTF-8');


        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$name, $openingHours, $foodType, $urlImageBackground, $deliveryFee, $currency, $currencySign, $percentServiceCharge, $percentTax, $kitchenId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($kitchenId){
        $r = $this
            ->where('kitchen_id', $kitchenId)
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
