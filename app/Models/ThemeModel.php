<?php
/**
 * Created by PageBuilder
 * Date: 2023-04-04 14:18:55
 */
namespace App\Models;

class ThemeModel extends BaseModel
{
    const VIEW = 'vtheme_element';
    const TABLE_THEME = 'ttheme';

    const SQL_GET = 'SELECT * FROM ttheme WHERE theme_id=?';
    const SQL_CLONE = 'INSERT INTO ttheme_element (theme_id, element_id, path, url_image, color_value) SELECT ?, element_id, path, url_image, color_value FROM ttheme_element WHERE theme_id=?';
    const SQL_GET_ELEMENT = 'SELECT * FROM vtheme_element WHERE (theme_id=?) AND (element_id=?)';
    const SQL_GET_ALL = 'SELECT ttheme.theme_id AS theme_id, ttheme.name AS theme_name, telement.element_id AS element_id,telement.name AS element_name, ttheme_element.update_date AS update_date, ttheme_element.url_image AS url_image, ttheme_element.color_value AS color_value, telement.width AS width, telement.height AS height, telement.type AS type from ((ttheme JOIN ttheme_element ON(ttheme_element.theme_id = ttheme.theme_id)) JOIN telement ON(ttheme_element.element_id = telement.element_id))';
    const SQL_MODIFY = 'UPDATE ttheme_element SET url_image=?, color_value=? WHERE (theme_id=?) AND (element_id=?)';
    const SQL_MODIFY_THEME = 'UPDATE ttheme SET name=? WHERE theme_id=?';
    const SQL_GET_THEME_FOR_SELECT = 'SELECT theme_id as id, `name` as value FROM  ttheme ORDER BY theme_id';
    const SQL_GET_ELEMENT_FOR_SELECT = 'SELECT element_id as id, `name` as value FROM telement ORDER BY element_id';
    const SQL_UPDATE_LAST_UPDATE = 'UPDATE ttheme SET last_update=now() WHERE theme_id=?';
    const SQL_GET_LAST_UPDATE = 'SELECT update_date FROM ttheme_element WHERE theme_id=? ORDER BY update_date DESC LIMIT 1';

    protected $table      = 'ttheme_element';
    protected $primaryKey = 'theme_id';
    protected $allowedFields = ['theme_id', 'element_id', 'url_image', 'width', 'height', 'color_value', 'create_date', 'update_date'];

    public $errCode;
    public $errMessage;

    public function get($themeId)
    {
        $db = db_connect();
        $ar = $db->query(self::SQL_GET, [$themeId])->getResult('array');

        if (sizeof($ar)>0) return $ar[0];

        return null;
    }

    public function getLastUpdate($themeId)
    {
        $db = db_connect();
        $ar = $db->query(self::SQL_GET_LAST_UPDATE, [$themeId])->getResult('array');

        return $ar;
    }

    public function getElement($themeId, $elementId)
    {
        $db = db_connect();
        $ar = $db->query(self::SQL_GET_ELEMENT, [$themeId, $elementId])->getResult('array');

        if (sizeof($ar)>0) return $ar[0];

        return null;
    }

    public function getAll(){
        $db = db_connect();
        return $db->query(self::SQL_GET_ALL)->getResult('array');
    }

    public function getThemeFieldList(){
        return ['theme_id', 'name', 'create_date', 'update_date'];
    }

    public function getFieldList(){
        return ['theme_id', 'theme_name', 'element_id', 'element_name', 'type', 'update_date', 'url_image', 'width', 'height', 'color_value'];
    }

    public function getThemeForSelect(){
        $result = $this->db->query(self::SQL_GET_THEME_FOR_SELECT)->getResult('array');
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }
    public function getElementForSelect(){
        $result = $this->db->query(self::SQL_GET_ELEMENT_FOR_SELECT)->getResult('array');
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }

    public function add($value)  {

        try
        {
            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');

            $this->db->table(self::TABLE_THEME)->insert($value);
            return $this->db->insertID(); // Return the inserted record ID
        }
        catch (\Exception $e){
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();

            return 0;
        }

        return $this->db->affectedRows();
    }

    /**
     * Buat theme baru dan clone semua contentnya
     * @param $value
     * @return int|string
     */
    public function clone($themeId, $value)  {
        try
        {
            $db = $this->db;
            $db->transStart();

            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');

            //create new record pada ttheme
            $db->table(self::TABLE_THEME)->insert($value);
            $newId = $db->insertID(); // new themeId

            //clone semua ttheme_element, dari themeId --> newId
            $db->query(self::SQL_CLONE, [$newId, $themeId]);

            if ($db->transStatus() === FALSE) {
                // Handle transaction error
                $this->db->transRollback();
            } else {
                // Transaction was successful
                $this->db->transComplete();
                return $newId;
            }
        }
        catch (\Exception $e){
            $this->varDump($e);

            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();

            return 0;
        }

        return $this->db->affectedRows();
    }

    public function modifyTheme($value){

        $this->errCode = '';
        $this->errMessage = '';

        $themeId = $value['theme_id'];
         $name = $value['name'];

        // $path = htmlentities($value['path'], ENT_QUOTES, 'UTF-8');
        $name = htmlentities($name, ENT_QUOTES, 'UTF-8');

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY_THEME);
            $stmt->execute( [$name, $themeId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }

    public function updateLastTheme($themeId){
        $this->loge('updateLastTheme');

        $this->errCode = '';
        $this->errMessage = '';

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_UPDATE_LAST_UPDATE);
            $stmt->execute( [$themeId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
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

        $themeId = $value['theme_id'];
        $elementId = $value['element_id'];
        // $elementName = $value['element_name'];

        // $path = htmlentities($value['path'], ENT_QUOTES, 'UTF-8');
        $urlImage = htmlentities($value['url_image'], ENT_QUOTES, 'UTF-8');
        $colorValue = htmlentities($value['color_value'], ENT_QUOTES, 'UTF-8');
     
        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$urlImage, $colorValue, $themeId, $elementId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function removeTheme($themeId){
        try
        {
            $this->db->table(self::TABLE_THEME)->delete(['theme_id'=>$themeId]);
            return $this->db->affectedRows();
        }
        catch (\Exception $e){
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();

            return 0;
        }
    }

    /**
     * di pakai utk datatables.js
     *
     * @return mixed
     */
    public function getThemeSsp()
    {
        return $this->_getSsp(self::TABLE_THEME, $this->primaryKey, $this->getThemeFieldList());
    }

    /**
     * ssp utk theme_element
     *
     * @param $themeId
     * @return array
     */
    public function getSsp($themeId)
    {
        $where = 'theme_id=' . $themeId;
        return $this->_getSspComplex(self::VIEW, $this->primaryKey, $this->getFieldList(), $where);
    }
}
