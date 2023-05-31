<?php
/**
 * Created by PageBuilder
 * Date: 2023-05-30 16:17:57
 */
namespace App\Models;

use App\Libraries\SSP;

class LiveTVModel extends BaseModel
{
    const VIEW = 'vlivetv';

    const SQL_GET = 'SELECT * FROM tlivetv WHERE (livetv_id=?)';
    const SQL_GET_ALL = "SELECT tlivetv.livetv_id AS livetv_id, tlivetv.url_station_logo AS url_station_logo, tlivetv.name AS name, tlivetv.url_stream1 AS url_stream1, tlivetv.lang_code AS lang_code, tlanguage.language AS language, tlivetv.rating AS rating, tlivetv.channel_number AS channel_number, tlivetv.ord AS ord, tlivetv.livetv_category_id AS livetv_category_id, tlivetv_category.category AS category, tlivetv.create_date AS create_date, tlivetv.update_date AS update_date, tlivetv.running_text AS running_text, tlivetv.pay_tv AS pay_tv, tlivetv.countryId AS countryId, tlivetv.status AS status FROM (tlivetv INNER JOIN tlanguage ON tlivetv.lang_code = tlanguage.lang_code LEFT JOIN tlivetv_category ON tlivetv.livetv_category_id = tlivetv_category.livetv_category_id)";
    const SQL_MODIFY = 'UPDATE tlivetv SET name=?, url_stream1=?, lang_code=?, rating=?, channel_number=?, ord=?, url_station_logo=?, livetv_category_id=? WHERE (livetv_id=?)';
    const SQL_GET_LANG = 'SELECT lang_code AS id ,language AS value FROM tlanguage';
    const SQL_GET_CATEGORY = 'SELECT livetv_category_id AS id ,category AS value FROM tlivetv_category';

    protected $table      = 'tlivetv';
    protected $primaryKey = 'livetv_id';
    protected $allowedFields = ['name', 'url_stream1', 'lang_code', 'language', 'rating', 'channel_number', 'ord', 'url_station_logo', 'livetv_category_id', 'create_date', 'update_date', 'running_text', 'pay_tv', 'countryId', 'status'];

    public $errCode;
    public $errMessage;


    public function get($livetvId)
    {
        $r = $this
            ->where('livetv_id', $livetvId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        $db = db_connect();
        return $db->query(self::SQL_GET_ALL)->getResult('array');
           
    }

    public function getLangForSelect()
    {
		$result     = $this->db->query(self::SQL_GET_LANG)->getResult('array');
    	if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }

    public function getCategoryForSelect()
    {
        $result     = $this->db->query(self::SQL_GET_CATEGORY)->getResult('array');
    	if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }

    public function getFieldList(){
        return ['livetv_id', 'url_station_logo', 'name', 'url_stream1', 'lang_code', 'language', 'rating', 'channel_number', 'ord', 'livetv_category_id', 'category', 'create_date', 'update_date', 'running_text', 'pay_tv', 'countryId', 'status'];
    }

    public function add($value)  {

        try
        {
            $existingChannel = $this->db->table('tlivetv')
                                ->where('channel_number', $value['channel_number'])
                                ->get()
                                ->getRow();

            if ($existingChannel) {
                throw new \Exception('Channel number already exists.', 1);
            }

            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            $value['url_stream1'] = htmlentities($value['url_stream1'], ENT_QUOTES, 'UTF-8');
            $value['lang_code'] = htmlentities($value['lang_code'], ENT_QUOTES, 'UTF-8');
            $value['rating'] = htmlentities($value['rating'], ENT_QUOTES, 'UTF-8');
            $value['url_station_logo'] = htmlentities($value['url_station_logo'], ENT_QUOTES, 'UTF-8');
            $value['ord'] = $value['ord'];
            $value['livetv_category_id'] = $value['livetv_category_id'];
            // $value['running_text'] = htmlentities($value['running_text'], ENT_QUOTES, 'UTF-8');
            // $value['countryId'] = htmlentities($value['countryId'], ENT_QUOTES, 'UTF-8');
            // $value['status'] = htmlentities($value['status'], ENT_QUOTES, 'UTF-8');

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

        $livetvId = $value['livetv_id'];

        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        $urlStream1 = htmlentities($value['url_stream1'], ENT_QUOTES, 'UTF-8');
        $langCode = htmlentities($value['lang_code'], ENT_QUOTES, 'UTF-8');
        $rating = htmlentities($value['rating'], ENT_QUOTES, 'UTF-8');
        $channelNumber = $value['channel_number'];
        $ord = $value['ord'];
        $urlStationLogo = htmlentities($value['url_station_logo'], ENT_QUOTES, 'UTF-8');
        $livetvCategoryId = $value['livetv_category_id'];
        // $createDate = $value['create_date'];
        // $updateDate = $value['update_date'];
        // $runningText = htmlentities($value['running_text'], ENT_QUOTES, 'UTF-8');
        // $payTv = $value['pay_tv'];
        // $countryId = htmlentities($value['countryId'], ENT_QUOTES, 'UTF-8');
        // $status = htmlentities($value['status'], ENT_QUOTES, 'UTF-8');

        // if (strlen($createDate)==0) $createDate = null;
        // if (strlen($updateDate)==0) $updateDate = null;

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$name, $urlStream1, $langCode, $rating, $channelNumber, $ord, $urlStationLogo, $livetvCategoryId, $livetvId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($livetvId){
        $r = $this
            ->where('livetv_id', $livetvId)
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
