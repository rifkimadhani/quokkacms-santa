<?php
/**
 * Created by PageBuilder
 * Date: 2023-03-30 15:24:23
 */
namespace App\Models;

use App\Libraries\SSP;

class FacilityModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tfacility WHERE (facility_id=?)';
    const SQL_MODIFY = 'UPDATE tfacility SET name=?, description=? WHERE (facility_id=?)';
    const SQL_INSERT = 'INSERT INTO tfacility (title, description, create_date, update_date, ord) VALUES (?, ?, ?, ?, ?)';
    const SQL_INSERT_MEDIA = 'INSERT INTO tfacility_image (facility_id, url_image) VALUES (?, ?)';
    const SQL_UPDATE_MEDIA = 'UPDATE tfacility_image SET url_image=? WHERE facility_id=?';

    protected $table      = 'tfacility';
    protected $primaryKey = 'facility_id';
    protected $allowedFields = ['name', 'description', 'create_date', 'update_date'];

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
        return ['facility_id', 'name', 'description', 'create_date', 'update_date'];
    }

    public function add($value)  {

        try
        {
            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            $value['description'] = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');

            $this->db->transBegin();

            // insert into tfacility
            parent::insert($value);

            // insert into tfacility_image
            $facilityId = $this->db->insertID();
            $urlImage = $value['url_image'];

            $this->db->query(self::SQL_INSERT_MEDIA, [$facilityId, $urlImage]);

            $this->db->transCommit();
        }
        catch (\Exception $e){
            $this->db->transRollback();

            $this->errCode = $e->getCode();
            $this->errMessage = "Insert failed: ".$e->getMessage();

            return $this->errMessage;
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

        $facilityId = $value['facility_id'];

        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        $description = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');
        
        $urlImage = htmlentities($value['url_image'], ENT_QUOTES, 'UTF-8');

        try{
            $pdo = $this->openPdo();
            $pdo->beginTransaction();

            // update tfacility
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$name, $description, $facilityId] );
            $rowCount = $stmt->rowCount();

            // update tfacility_image
            $stmt = $pdo->prepare(self::SQL_UPDATE_MEDIA);
            $stmt->execute([$urlImage, $facilityId]);
            $rowCount = $stmt->rowCount();

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
        $r = $this
            ->where('facility_id', $facilityId)
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
