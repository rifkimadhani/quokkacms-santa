<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/28/2022
 * Time: 10:52 AM
 */

namespace App\Models;

class FacilityMediaModel extends BaseModel
{
    const SQL_GET_ALL = 'SELECT * FROM tfacility_image WHERE facility_id=?';

    const SQL_WRITE_1 = 'DELETE FROM tfacility_image WHERE facility_id=?';
    const SQL_WRITE_2 = 'INSERT INTO tfacility_image (facility_id, url_image) VALUES (?, ?)';

    protected $table      = 'tfacility_image';
    protected $primaryKey = 'facility_media_id';
    protected $allowedFields = ['url_image', 'url_video', 'caption'];

    public function getAll($facilityId){
        return $this->db->query(self::SQL_GET_ALL, [$facilityId])->getResult('array');
    }

    /**
     * 1. hapus semua record
     * 2. insert satu persatu record
     *
     * @param $facilityId
     * @param $urlImage = comma seperated value
     *
     * @return true/false
     */
    public function write($facilityId, $urlImage){

        $this->db->transStart();

        //delete
        $this->db->query(self::SQL_WRITE_1, [$facilityId]);

        //url kosong tdk perlu simpan image
        if (strlen($urlImage)==0) {
            $this->db->transComplete();
            return;
        }

        $ar = explode(',', $urlImage);

        //insert one by one
        foreach ($ar as $url){
            $this->db->query(self::SQL_WRITE_2, [$facilityId, $url]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return false;
        }

        return true;
    }


}