<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-06 12:33:41
 */
namespace App\Models;

class SettingModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tsetting WHERE (setting_id=?)';
    const SQL_MODIFY = 'UPDATE tsetting SET name=?, value_int=?, value_float=?, value_string=? WHERE (setting_id=?)';

    const SETTING_TIMEZONE = 14;
    const SETTING_CURRENCY = 10;
    const SETTING_CURRENCY_SIGN = 11;
    const SETTING_THEME_DEFAULT = 1000;

    protected $table      = 'tsetting';
    protected $primaryKey = 'setting_id';
    protected $allowedFields = ['name', 'value_int', 'value_string', 'value_float'];

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

    public function get($settingId)
    {
        $r = $this
            ->where('setting_id', $settingId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['setting_id', 'name', 'value_int', 'value_float', 'value_string', 'create_date', 'update_date'];
    }

    public function add($value)  {

        try
        {
            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            $value['value_string'] = htmlentities($value['value_string'], ENT_QUOTES, 'UTF-8');
//            $value['value_float'] = htmlentities($value['value_float'], ENT_QUOTES, 'UTF-8');

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

        $settingId = $value['setting_id'];

        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        $valueInt = $value['value_int'];
        $valueFloat = $value['value_float'];
        $valueString = htmlentities($value['value_string'], ENT_QUOTES, 'UTF-8');
//        $valueFloat = htmlentities($value['value_float'], ENT_QUOTES, 'UTF-8');

        //make null apabila value tdk ada
        if (strlen($valueInt)==0) $valueInt = null;
        if (strlen($valueFloat)==0) $valueFloat = null;
        if (strlen($valueString)==0) $valueString = null;

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$name, $valueInt, $valueFloat, $valueString, $settingId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($settingId){
        $r = $this
            ->where('setting_id', $settingId)
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

    public function getTimeZone(){
        $row = $this->get(self::SETTING_TIMEZONE);
        if ($row==null) return null;
        return $row['value_string'];
    }

    /**
     * @return null IDR
     */
    public function getCurrency(){
        $row = $this->get(self::SETTING_CURRENCY);
        if ($row==null) return null;
        return $row['value_string'];
    }

    public function getCurrencySign(){
        $row = $this->get(self::SETTING_CURRENCY_SIGN);
        if ($row==null) return null;
        return $row['value_string'];
    }

    public function getThemeDefault(){
        $row = $this->get(self::SETTING_THEME_DEFAULT);
        if ($row==null) return 0;
        return $row['value_int'];
    }

    public function setThemeDefault($themeId){
        $data = [
            'value_int' => $themeId
        ];
        self::update(self::SETTING_THEME_DEFAULT, $data);

        return $this->db->affectedRows();
    }
}
