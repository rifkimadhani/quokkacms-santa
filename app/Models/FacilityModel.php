<?php
/**
 * Created by PageBuilder
 * Date: 2023-03-30 15:24:23
 */
namespace App\Models;

use App\Libraries\StringUtil;

class FacilityModel extends BaseModel
{
    const VIEW = "vfacility_media";

    const SQL_INSERT = 'INSERT INTO tfacility (name, description) VALUES (?, ?)';
    const SQL_GET = 'SELECT * FROM tfacility WHERE (facility_id=?)';
    const SQL_MODIFY = 'UPDATE tfacility SET name=?, description=?, ord=? WHERE (facility_id=?)';
//    const SQL_INSERT = 'INSERT INTO tfacility (title, description, create_date, update_date, ord) VALUES (?, ?, ?, ?, ?)';
    const SQL_INSERT_MEDIA = 'INSERT INTO tfacility_image (facility_id, url_image, url_video) VALUES (?, ?, ?)';

    const SQL_REMOVE = 'DELETE FROM tfacility WHERE facility_id=?';
    const SQL_REMOVE_MEDIA = 'DELETE FROM tfacility_image WHERE facility_id=?';
    const SQL_SSP = 'select `tfacility`.`facility_id` AS `facility_id`,`tfacility`.`name` AS `name`,group_concat(`tfacility_image`.`url_image` separator \',\') AS `url_image`,group_concat(`tfacility_image`.`url_video` separator \',\') AS `url_video`,`tfacility`.`create_date` AS `create_date`,`tfacility`.`update_date` AS `update_date`, ord from (`tfacility` left join `tfacility_image` on(`tfacility`.`facility_id` = `tfacility_image`.`facility_id`)) group by `tfacility`.`facility_id`';

    protected $table      = 'tfacility';
    protected $primaryKey = 'facility_id';
    protected $allowedFields = ['name', 'description', 'ord'];

    public $errCode;
    public $errMessage;

    public function get($facilityId)
    {
        // $r = $this
        //     ->where('facility_id', $facilityId)
        //     ->find();
        // if ($r!=null) return $r[0];

        // return null;

        return $this->find($facilityId);
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['facility_id', 'name', 'url_image', 'url_video', 'ord', 'update_date'];
    }

    public function add($value)  {

        $rowCount = 0;
        $pdo = $this->openPdo();

        try
        {
            $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            $description = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');

            $pdo->beginTransaction();

            $stmt = $pdo->prepare(self::SQL_INSERT);
            $stmt->execute([$name, $description]);

            $rowCount = $stmt->rowCount();

            // insert into tfacility_image
            $facilityId = $pdo->lastInsertId();

            //url image bisa beberapa image
            //split
            $arUrl= explode(',', $value['url_image']);
            foreach ($arUrl as $url){
                //check apakah file .mp4 ??
                if (StringUtil::endsWith(strtolower($url), '.mp4')){
                    //file is mp4
                    $stmt = $pdo->prepare(self::SQL_INSERT_MEDIA);
                    $stmt->execute([$facilityId, null, $url]);
                } else {
                    //file is other
                    $stmt = $pdo->prepare(self::SQL_INSERT_MEDIA);
                    $stmt->execute([$facilityId, $url, null]);
                }
            }

            $pdo->commit();
        }
        catch (\Exception $e){
            $pdo->rollBack();

            $this->errCode = $e->getCode();
            $this->errMessage = "Insert failed: ".$e->getMessage();

            return $this->errMessage;
        }

        return $rowCount;
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

        $facilityId = $value['facility_id'];

        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        $description = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');
        $ord = htmlentities($value['ord'], ENT_QUOTES, 'UTF-8');

        $value['url_image'] = htmlentities($value['url_image'], ENT_QUOTES, 'UTF-8');

        $pdo = $this->openPdo();

        try{
            $pdo->beginTransaction();

            // update tfacility
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$name, $description, $ord, $facilityId] );
            $rowCount = $stmt->rowCount();

            // delete semua image
            $stmt = $pdo->prepare(self::SQL_REMOVE_MEDIA);
            $stmt->execute([$facilityId]);

            // update tfacility_image
            //hanya update bila ada media
            if (strlen($value['url_image'])>0){
                $arUrl= explode(',', $value['url_image']);
                foreach ($arUrl as $url){
                    //check apakah file .mp4 ??
                    if (StringUtil::endsWith(strtolower($url), '.mp4')){
                        //file is mp4
                        $stmt = $pdo->prepare(self::SQL_INSERT_MEDIA);
                        $stmt->execute([$facilityId, null, $url]);
                    } else {
                        //file is other
                        $stmt = $pdo->prepare(self::SQL_INSERT_MEDIA);
                        $stmt->execute([$facilityId, $url, null]);
                    }
                }
            }

            $pdo->commit();

            return $rowCount;

        }catch (\PDOException $e){
            
            log_message('error', json_encode($e));
            $pdo->rollBack();

            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            
            return -1;
        }
    }

    public function remove($facilityId){

        $rowCount = 0;
        $pdo = $this->openPdo();

        try
        {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(self::SQL_REMOVE_MEDIA);
            $stmt->execute( [$facilityId] );

            $stmt = $pdo->prepare(self::SQL_REMOVE);
            $stmt->execute( [$facilityId] );
            $rowCount = $stmt->rowCount();

            $pdo->commit();
        }
        catch (\Exception $e){
            $pdo->rollBack();
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
        }

        return $rowCount;
    }

    /**
     * di pakai utk datatables.js
     *
     * @return mixed
     */
    public function getSsp()
    {
//        return $this->_getSsp(self::VIEW, $this->primaryKey, $this->getFieldList());
        return $this->_getSspCustom(self::SQL_SSP, $this->getFieldList());
    }
}
