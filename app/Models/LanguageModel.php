<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-06 12:18:00
 */
namespace App\Models;

class LanguageModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tlanguage WHERE (lang_code=?)';
    const SQL_MODIFY = 'UPDATE tlanguage SET language=? WHERE (lang_code=?)';

    protected $table      = 'tlanguage';
    protected $primaryKey = 'lang_code';
    protected $allowedFields = ['lang_code', 'language', 'crate_date', 'update_date'];

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

    public function get($langCode)
    {
        $r = $this
            ->where('lang_code', $langCode)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['lang_code', 'language'];
    }

    public function add($value)  {

        try
        {
            $value['language'] = htmlentities($value['language'], ENT_QUOTES, 'UTF-8');

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

        $langCode = $value['lang_code'];

        $language = htmlentities($value['language'], ENT_QUOTES, 'UTF-8');
//        $crateDate = $value['crate_date'];
//        $updateDate = $value['update_date'];

//        if (strlen($crateDate)==0) $crateDate = null;
//        if (strlen($updateDate)==0) $updateDate = null;

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$language, $langCode] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($langCode){
        $r = $this
            ->where('lang_code', $langCode)
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
