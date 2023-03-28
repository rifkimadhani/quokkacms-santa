<?php

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Security.php';
require_once __DIR__ . '/../library/Log.php';

class ModelLiveTvEpg
{
    const SQL_GET_LIST  = 'SELECT epg_id, start_date, end_date, duration, name, sinopsis FROM tlivetv_epg WHERE livetv_id=? AND create_date>?';

    const SQL_INSERT = 'INSERT INTO tlivetv_epg ( livetv_id, start_date, end_date, duration, name, sinopsis) VALUES (?,?,?,?,?,?)';
    const SQL_UPDATE = 'UPDATE tlivetv_epg SET end_date=?, duration=?, name=?, sinopsis=? WHERE livetv_id=? AND start_date=?';

    /**
     * Get list dari livetv id dan tanggal awal sampai semua epg yg ada
     *
     * @param int $livetvId
     * @param string $fromDate
     * @return Exception|int|PDOException|string
     */
    static public function getList(int $livetvId, string $fromDate){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelLiveTvEpg::SQL_GET_LIST);
            $stmt->execute( [$livetvId, $fromDate] );

            $rows = $stmt->fetchAll();
            return $rows;

        }catch (PDOException $e){
            if ($e->getCode()==='23000') return 0; //Duplicate entry
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function insert(int $livetvId, string $startDate, string $endDate, int $duration, string $name, string $sinopsis){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelLiveTvEpg::SQL_INSERT);
            $stmt->execute( [$livetvId, $startDate, $endDate, $duration, $name, $sinopsis] );

            $epgId = $pdo->lastInsertId();

            return $epgId;

        }catch (PDOException $e){
            if ($e->getCode()==='23000') return 0; //Duplicate entry
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    /**
     * Melakukan update berdasarkan dari livetvId dan startDate
     *
     * @param int $livetvId
     * @param string $startDate
     * @param string $endDate
     * @param int $duration
     * @param string $name
     * @return Exception|PDOException|string
     */
    static public function update(int $livetvId, string $startDate, string $endDate, int $duration, string $name, string $sinopsis){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelLiveTvEpg::SQL_UPDATE);
            $stmt->execute( [$endDate, $duration, $name, $sinopsis, $livetvId, $startDate] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    /**
     * Melakukan insert terlebih dahulu kemudian update apabila terjadi duplcate
     *
     * @param int $livetvId
     * @param string $startDate
     * @param string $endDate
     * @param int $duration
     * @param string $name
     * @return int -1 = gagal, > 0 berhasi di insert, 0 = berhasil di update;
     */
    static public function insertOrUpdate(int $livetvId, string $startDate, string $endDate, int $duration, string $name, string $sinopsis) : int {

        $epgId = self::insert($livetvId, $startDate, $endDate, $duration, $name, $sinopsis);

        //insert gagal, maka lakukan update
        if ($epgId==0){

            $count = self::update($livetvId, $startDate, $endDate, $duration, $name, $sinopsis);
            if ($count==0) return -1;
        }

        return $epgId;
    }
}