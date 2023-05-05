<?php
/**
 * Created by PageBuilder
 * Date: 2023-04-04 14:18:55
 */
namespace App\Models;

use App\Libraries\SSP;

class ThemeModel extends BaseModel
{
    const VIEW = 'vtheme_element';

    // const SQL_GET = 'SELECT * FROM ttheme_element WHERE (theme_id=?) AND (element_id=?)';
    const SQL_GET = 'SELECT ttheme.theme_id AS theme_id, ttheme.name AS theme_name, telement.element_id AS element_id,telement.name AS element_name, ttheme_element.update_date AS update_date, ttheme_element.url_image AS url_image, ttheme_element.color_value AS color_value, telement.width AS width, telement.height AS height, telement.type AS type from ((ttheme JOIN ttheme_element ON(ttheme_element.theme_id = ttheme.theme_id)) JOIN telement ON(ttheme_element.element_id = telement.element_id))';
    const SQL_MODIFY = 'UPDATE ttheme_element SET url_image=?, color_value=? WHERE (theme_id=?) AND (element_id=?)';
    const SQL_GET_THEME_FOR_SELECT = 'SELECT theme_id as id, `name` as value FROM  ttheme ORDER BY theme_id';
    const SQL_GET_ELEMENT_FOR_SELECT = 'SELECT element_id as id, `name` as value FROM telement ORDER BY element_id';

    protected $table      = 'ttheme_element';
    protected $primaryKey = 'theme_id';
    protected $allowedFields = ['theme_id', 'element_id', 'url_image', 'width', 'height', 'color_value', 'create_date', 'update_date'];

    public $errCode;
    public $errMessage;


    public function get($themeId, $elementId)
    {
        $r = $this
            ->where('theme_id', $themeId)
            ->where('element_id', $elementId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        $db = db_connect();
        return $db->query(self::SQL_GET)->getResult('array');
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
            // $value['path'] = htmlentities($value['path'], ENT_QUOTES, 'UTF-8');
            $value['url_image'] = htmlentities($value['url_image'], ENT_QUOTES, 'UTF-8');
            $value['color_value'] = htmlentities($value['color_value'], ENT_QUOTES, 'UTF-8');

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


    public function remove($themeId, $elementId){
        $r = $this
            ->where('theme_id', $themeId)
            ->where('element_id', $elementId)
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
        return $this->_getSsp(self::VIEW, $this->primaryKey, $this->getFieldList());
    }
}
