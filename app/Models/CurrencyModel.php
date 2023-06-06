<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-06 12:27:39
 */
namespace App\Models;

class CurrencyModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tcurrency WHERE (currency=?)';
    const SQL_MODIFY = 'UPDATE tcurrency SET currency_sign=?, description=? WHERE (currency=?)';

    protected $table      = 'tcurrency';
    protected $primaryKey = 'currency';
    protected $allowedFields = ['currency', 'currency_sign', 'description'];

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

    public function get($currency)
    {
        $r = $this
            ->where('currency', $currency)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['currency', 'currency_sign', 'description'];
    }

    public function add($value)  {

        try
        {
            $value['currency_sign'] = htmlentities($value['currency_sign'], ENT_QUOTES, 'UTF-8');
            $value['description'] = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');

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

        $currency = $value['currency'];

        $currencySign = htmlentities($value['currency_sign'], ENT_QUOTES, 'UTF-8');
        $description = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');


        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$currencySign, $description, $currency] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($currency){
        $r = $this
            ->where('currency', $currency)
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
