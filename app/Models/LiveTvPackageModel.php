<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-05 11:03:40
 */
namespace App\Models;

class LiveTvPackageModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tpackage WHERE (package_id=?)';
    const SQL_MODIFY = 'UPDATE tpackage SET name=?, description=? WHERE (package_id=?)';
    const SQL_GET_ALL_BY_PACKAGEID = "SELECT package_id, livetv_id, name, url_station_logo FROM vpackage_livetv WHERE package_id=?";
    const SQL_GET_ALL_BY_PACKAGEID_REVERSE = "SELECT livetv_id, name, url_station_logo FROM tlivetv WHERE livetv_id NOT IN (SELECT livetv_id FROM tpackage_livetv WHERE package_id=?)";

    const SQL_DELETE_PACKAGE = "DELETE FROM tpackage WHERE package_id=?";
    const SQL_DELETE_LIVETV = "DELETE FROM tpackage_livetv WHERE package_id=?";
    const SQL_INSERT_LIVETV = "INSERT INTO tpackage_livetv (package_id, livetv_id) VALUES (?, ?)";

    protected $table      = 'tpackage';
    protected $primaryKey = 'package_id';
    protected $allowedFields = ['name', 'description', 'url_package_logo', 'price', 'currency', 'currency_sign', 'rent_duration', 'percent_tax', 'url_image', 'create_date', 'update_date'];

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

    public function get($packageId)
    {
        $r = $this
            ->where('package_id', $packageId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['package_id', 'name', 'description'];
//        return ['package_id', 'name', 'description', 'url_package_logo', 'price', 'currency', 'currency_sign', 'rent_duration', 'percent_tax', 'url_image', 'create_date', 'update_date'];
    }

    public function getAllByPackageId($packageId){
        $db = db_connect();
        return $db->query(self::SQL_GET_ALL_BY_PACKAGEID, [$packageId])->getResult('array');
    }

    public function getAllByPackageIdReverse($packageId){
        $db = db_connect();
        return $db->query(self::SQL_GET_ALL_BY_PACKAGEID_REVERSE, [$packageId])->getResult('array');
    }

    public function updateLiveTv($packageId, $ar){

        $db = db_connect();
        try{
            $db->transBegin();

            $db->query(self::SQL_DELETE_LIVETV, [$packageId]);

            foreach ($ar as $livetvId){
                $db->query(self::SQL_INSERT_LIVETV, [$packageId, $livetvId]);
            }

            $db->transCommit();

            return 1;

        }catch (\Exception $e){
            $db->transRollback();
        }

        return 0;
    }

    public function add($value)  {

        try
        {
            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            $value['description'] = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');
//            $value['url_package_logo'] = htmlentities($value['url_package_logo'], ENT_QUOTES, 'UTF-8');
//            $value['price'] = htmlentities($value['price'], ENT_QUOTES, 'UTF-8');
//            $value['currency'] = htmlentities($value['currency'], ENT_QUOTES, 'UTF-8');
//            $value['currency_sign'] = htmlentities($value['currency_sign'], ENT_QUOTES, 'UTF-8');
//            $value['percent_tax'] = htmlentities($value['percent_tax'], ENT_QUOTES, 'UTF-8');
//            $value['url_image'] = htmlentities($value['url_image'], ENT_QUOTES, 'UTF-8');

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

        $packageId = $value['package_id'];

        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        $description = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');
//        $urlPackageLogo = htmlentities($value['url_package_logo'], ENT_QUOTES, 'UTF-8');
//        $price = htmlentities($value['price'], ENT_QUOTES, 'UTF-8');
//        $currency = htmlentities($value['currency'], ENT_QUOTES, 'UTF-8');
//        $currencySign = htmlentities($value['currency_sign'], ENT_QUOTES, 'UTF-8');
//        $rentDuration = $value['rent_duration'];
//        $percentTax = htmlentities($value['percent_tax'], ENT_QUOTES, 'UTF-8');
//        $urlImage = htmlentities($value['url_image'], ENT_QUOTES, 'UTF-8');
//        $createDate = $value['create_date'];
//        $updateDate = $value['update_date'];

//        if (strlen($createDate)==0) $createDate = null;
//        if (strlen($updateDate)==0) $updateDate = null;

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$name, $description, $packageId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($packageId){
        $db = db_connect();
        try{
            $db->transBegin();

            $db->query(self::SQL_DELETE_LIVETV, [$packageId]);
            $db->query(self::SQL_DELETE_PACKAGE, [$packageId]);

            $db->transCommit();

            return 1;

        }catch (\Exception $e){
            $db->transRollback();
        }

        return 0;
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
