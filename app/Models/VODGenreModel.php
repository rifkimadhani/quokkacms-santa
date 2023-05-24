<?php
/**
 * Created by PageBuilder
 * Date: 2023-05-11 13:48:16
 */
namespace App\Models;

use App\Libraries\SSP;

class VODGenreModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tgenre WHERE (genre_id=?)';
    const SQL_MODIFY = 'UPDATE tgenre SET genre=? WHERE (genre_id=?)';
    const SQL_GET_FOR_SELECT = 'SELECT genre_id AS id, genre AS value FROM tgenre ORDER BY genre';

    protected $table      = 'tgenre';
    protected $primaryKey = 'genre_id';
    protected $allowedFields = ['genre', 'create_date', 'update_date'];

    public $errCode;
    public $errMessage;

    public function get($genreId)
    {
        $r = $this->where('genre_id', $genreId)->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getForSelect(){
        // $db = db_connect();
        // return $db->query(self::SQL_GET_FOR_SELECT)->getResult('array');
        $result = $this->db->query(self::SQL_GET_FOR_SELECT)->getResult('array');
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
        
    }

    public function getGenresForVOD($vodId)
    {
        $genreIds = $this->db->table('tvod_genre')
            ->select('genre_id')
            ->where('vod_id', $vodId)
            ->get()
            ->getResultArray();
    
        $genreIds = array_column($genreIds, 'genre_id');

        // Convert genre IDs to integers
        $genreIds = array_map('intval', $genreIds);
    
        return $genreIds;
    }
    

    public function getFieldList(){
        return ['genre_id', 'genre', 'create_date', 'update_date'];
    }

    public function add($value)  {

        try
        {
            $value['genre'] = htmlentities($value['genre'], ENT_QUOTES, 'UTF-8');

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

        $genreId = $value['genre_id'];

        $genre = htmlentities($value['genre'], ENT_QUOTES, 'UTF-8');
        // $createDate = $value['create_date'];
        // $updateDate = $value['update_date'];

        // if (strlen($createDate)==0) $createDate = null;
        // if (strlen($updateDate)==0) $updateDate = null;

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$genre, $genreId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($genreId){
        $r = $this
            ->where('genre_id', $genreId)
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
