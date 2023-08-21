<?php
/**
 * Created by PageBuilder
 * Date: 2023-03-28 12:20:30
 */
namespace App\Models;

use App\Libraries\SSP;

class LocalityModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tlocality WHERE (locality_id=?)';
    // const SQL_GET_MEDIA = 'SELECT tlocality.*, tlocality_media.url_image FROM tlocality LEFT JOIN tlocality_media ON tlocality.locality_id = tlocality_media.locality_id WHERE tlocality.locality_id = ?';
    const SQL_GET_ALL = 'SELECT tlocality.locality_id, tlocality.title, tlocality.description, tlocality_media.url_image, tlocality.ord FROM tlocality LEFT JOIN tlocality_media ON tlocality.locality_id = tlocality_media.locality_id ORDER BY tlocality.locality_id DESC';
    const SQL_MODIFY = 'UPDATE tlocality SET title=?, description=?, ord=? WHERE (locality_id=?)';
    const SQL_INSERT = 'INSERT INTO tlocality (title, description, create_date, update_date, ord) VALUES (?, ?, ?, ?, ?)';
    const SQL_INSERT_MEDIA = 'INSERT INTO tlocality_media (locality_id, url_image) VALUES (?, ?)';
    const SQL_UPDATE_MEDIA = 'UPDATE tlocality_media SET url_image=? WHERE locality_id=?';

    protected $table      = 'tlocality';
    protected $primaryKey = 'locality_id';
    protected $allowedFields = ['title', 'description', 'create_date', 'update_date', 'ord'];

    public $errCode;
    public $errMessage;

    public function get($localityId)
    {
        // $r = $this
        //     ->where('locality_id', $localityId)
        //     ->find();
        // if ($r!=null) {
        //     return $r[0];
        // }

        // return null;

        return $this->find($localityId);
    
    }

    public function getAll(){
        $db = db_connect();
        return $db->query(self::SQL_GET_ALL)->getResult();
    }

    public function getFieldList(){
        return ['locality_id', 'title', 'description', 'create_date', 'update_date', 'ord'];
    }

    public function add($value)  {

        try
        {
           
            $value['title'] = htmlentities($value['title'], ENT_QUOTES, 'UTF-8');
            $value['description'] = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');

            $this->db->transBegin();

            // insert into tlocality
            parent::insert($value);

            // insert into tlocality_media
            $localityId = $this->db->insertID();
//            $urlImage = $value['url_image'];

//            $this->db->query(self::SQL_INSERT_MEDIA, [$localityId, $urlImage]);

            $this->db->transCommit();

            return $localityId;
        }
        catch (\Exception $e){
            $this->db->transRollback();

            $this->errCode = $e->getCode();
            $this->errMessage = "Insert failed: ".$e->getMessage();

            return $e;
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

        $localityId = $value['locality_id'];

        $title = htmlentities($value['title'], ENT_QUOTES, 'UTF-8');
        $description = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');
        $ord = $value['ord'];

//        $urlImage = htmlentities($value['url_image'], ENT_QUOTES, 'UTF-8');

        try{

            $pdo = $this->openPdo();
            $pdo->beginTransaction();

            // update tlocality
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute([$title, $description, $ord, $localityId]);
            $rowCount = $stmt->rowCount();

            // update tlocality_media
//            $stmt = $pdo->prepare(self::SQL_UPDATE_MEDIA);
//            $stmt->execute([$urlImage, $localityId]);
//            $rowCount = $stmt->rowCount();

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


    public function remove($localityId){
        $r = $this
            ->where('locality_id', $localityId)
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
