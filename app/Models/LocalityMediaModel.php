<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 8/21/2023
 * Time: 10:04 AM
 */

namespace App\Models;

class LocalityMediaModel extends BaseModel
{
    const SQL_GET_ALL = 'SELECT * FROM tlocality_media WHERE (locality_id=?)';
    const SQL_REMOVE = 'DELETE FROM tlocality_media WHERE (locality_id=?)';
    const SQL_INSERT = 'INSERT INTO tlocality_media (locality_id, url_image, url_video) VALUES (?,?,?)';

    protected $table      = 'tlocality_media';
    protected $primaryKey = 'locality_media_id';

    protected $allowedFields = ['url_image', 'url_video'];

    public $errCode;
    public $errMessage;

    public function get($localityId)
    {
        $db = db_connect();
        return $db->query(self::SQL_GET_ALL, [$localityId])->getResult('array');
    }

    public function modify($localityId, $urlMedia){
        $pdo = $this->openPdo();

        try{

            $pdo->beginTransaction();

            // remove tlocality_media
            $stmt = $pdo->prepare(self::SQL_REMOVE);
            $stmt->execute([$localityId]);

            $media = explode(',', $urlMedia);

            // insert tlocality_media
            foreach ($media as $item){

                //apakah file adalah video atau image >
                if ($this->isVideo($item)){
                    $stmt = $pdo->prepare(self::SQL_INSERT);
                    $stmt->execute([$localityId, null, $item]);
                } else {
                    $stmt = $pdo->prepare(self::SQL_INSERT);
                    $stmt->execute([$localityId, $item, null]);
                }
            }

            $pdo->commit();

            return 1;

        }catch (\PDOException $e){
            $pdo->rollBack();

            log_message('error', json_encode($e));

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

    //return true if file with extension .mp4
    private function isVideo($filename){
        $filename = strtolower($filename);
        if ($this->strCmpFromRight($filename, '.mp4')) return true;
        if ($this->strCmpFromRight($filename, '.mov')) return true;

        return false;
    }

    //compare as string and needle from right
    private function strCmpFromRight($str, $needle){
        $substr = substr($str, strlen($str)-strlen($needle));
        return $substr == $needle;
    }

}