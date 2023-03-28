<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 10/9/2019
 * Time: 12:39 PM
 */

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

class ModelStat
{
    const TYPE_CONTINOUS = 'CONTINOUS';

	const SQL_GET_DEFINITION = 'SELECT * FROM tstat_def WHERE stat_def_id=?';
	const SQL_CREATE =           'INSERT INTO tstat (stb_id, type, group_0, group_1, group_2, group_3, group_4, value_unit, subscriber_id, start_date) VALUES (?,?,?,?,?,?,?,?,?, NOW())';
    const SQL_CREATE_CONTINOUS = 'INSERT INTO tstat (stb_id, type, group_0, group_1, group_2, group_3, group_4, value_unit, subscriber_id, `value`, end_date, start_date) VALUES (?,?,?,?,?,?,?,?,?,?,NOW(),TIMESTAMPADD(SECOND,-?,NOW()))';

	const SQL_UPDATE_VALUE = 'UPDATE tstat SET `value`=TIMESTAMPDIFF(SECOND, start_date, NOW()), end_date=NOW() WHERE stat_id=?';
	const SQL_GET_SUM_ALL = 'SELECT sum(`value`) FROM tstat WHERE stat_def_id=? GROUP BY stat_def_id';
	const SQL_GET_SUM_GROUP_1 = 'SELECT group_1, MIN(`value`) as min, MAX(`value`) as max, SUM(`value`) as total, AVG(`value`) as avg, COUNT(*) as count FROM tstat WHERE group_0=:group0 AND value is not null AND start_date>=:dt1 AND end_date<:dt2 GROUP BY group_0, group_1 ORDER BY `total` DESC LIMIT 10';
	const SQL_GET_SUM_GROUP_1_PERIODIC = 'SELECT MIN(`value`) as min, MAX(`value`) as max, SUM(`value`) as total, AVG(`value`) as avg, COUNT(*) as count FROM tstat WHERE stat_def_id=:def_id AND value is not null AND (
		(TIME(start_date)>=:t1 AND TIME(end_date)<=:t2) OR 
		(TIME(start_date)<:t1 AND TIME(end_date)>:t2) OR
		(TIME(start_date)>=:t1 AND TIME(start_date)<=:t2)	OR
		(TIME(end_date)>=:t1 AND TIME(end_date)<=:t2)	) GROUP BY stat_def_id';

	const SQL_GET_DISTINCT_SUBSCRIBER = 'SELECT count(DISTINCT subscriber_id) as count FROM tstat WHERE group_0=:group0 AND start_date>=:dt1 AND start_date<:dt2 AND (
		(TIME(start_date)>=:t1 AND TIME(end_date)<=:t2) OR
		(TIME(start_date)<:t1 AND TIME(end_date)>:t2) OR
		(TIME(start_date)>=:t1 AND TIME(start_date)<=:t2)	OR
		(TIME(end_date)>=:t1 AND TIME(end_date)<=:t2))';

	const SQL_GET_TOP_GROUP_1 = 'SELECT group_1, COUNT(DISTINCT subscriber_id) as count FROM tstat WHERE stat_def_id=:def_id AND  start_date>=:dt1 AND start_date<:dt2 GROUP BY stat_def_id, group_1 ORDER BY COUNT DESC, group_1 LIMIT :_limit';
	const SQL_GET_COUNT_GROUP_0 = 'SELECT COUNT(DISTINCT subscriber_id) as count FROM tstat WHERE stat_def_id=:def_id AND  start_date>=:dt1 AND start_date<:dt2 GROUP BY stat_def_id ORDER BY COUNT DESC';

	static public function getDefinition($statDefId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStat::SQL_GET_DEFINITION);
			$stmt->execute( [ $statDefId] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

    /**
     * FIELD  end_date = NOW()
     * FIELD value = TIMESTAMPDIFF(SECOND, start_date, NOW())
     *
     * FUNC ini di panggil utk melengkapi FUNC create pada $type continous.
     * tdk perlu masukin value lagi, krn value akan di hitung otomatis dari perbedaan waktu
     * antara NOW() dan start_date
     *
     * @param $statId
     * @return Exception|int|PDOException
     */
	static public function updateValue($statId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStat::SQL_UPDATE_VALUE);
			$stmt->execute( [ $statId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

    /**
     * FIELD start_date = NOW()
     * utk $type continous maka harus di lanjutkan dgn updateValue
     *
     * @param $stbId
     * @param $type
     * @param $group0
     * @param $group1
     * @param $group2
     * @param $group3
     * @param $group4
     * @param $valueUnit
     * @param $subscriberId
     * @return Exception|PDOException|string
     */
	static public function create($stbId, $type, $group0, $group1, $group2, $group3, $group4, $valueUnit, $subscriberId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStat::SQL_CREATE);
			$stmt->execute( [ $stbId, $type, $group0, $group1, $group2, $group3, $group4, $valueUnit, $subscriberId ] );

			return $pdo->lastInsertId();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

    /**
     * FIELD start_date = NOW() - $value
     * FIELD end_date = NOW()
     *
     * func ini di panggil di akhir dari streaming, shg nilai start_date adalah mundur dari end_date
     *
     * @param $stbId
     * @param $group0
     * @param $group1
     * @param $group2
     * @param $group3
     * @param $group4
     * @param $value
     * @param $valueUnit
     * @param $subscriberId
     * @return Exception|PDOException|string
     */
	static public function createContinous($stbId, $group0, $group1, $group2, $group3, $group4, $value, $valueUnit, $subscriberId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStat::SQL_CREATE_CONTINOUS);
			$stmt->execute( [ $stbId, ModelStat::TYPE_CONTINOUS, $group0, $group1, $group2, $group3, $group4, $valueUnit, $subscriberId, $value, $value ] );

			return $pdo->lastInsertId();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getSumAll(int $statId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStat::SQL_GET_SUM_ALL);
			$stmt->execute( [ $statId] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getSumGroup1(string $group0, string $dt1, string $dt2){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStat::SQL_GET_SUM_GROUP_1);
			$stmt->bindValue(':group0', $group0, PDO::PARAM_STR);
			$stmt->bindValue(':dt1', $dt1, PDO::PARAM_STR);
			$stmt->bindValue(':dt2', $dt2, PDO::PARAM_STR);

			$stmt->execute();

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	/**
	 * Ambil statistik group 0 dgn range waktu (TIME)
	 *
	 * @param int $defId
	 * @param string $t1
	 * @param string $t2
	 * @return array|Exception|PDOException
	 */
	static public function getGroup0ByRangeTime(int $defId, string $t1, string $t2){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStat::SQL_GET_SUM_GROUP_1_PERIODIC);

			$stmt->bindValue(':def_id', $defId, PDO::PARAM_INT);
			$stmt->bindValue(':t1', $t1, PDO::PARAM_STR);
			$stmt->bindValue(':t2', $t2, PDO::PARAM_STR);

			$stmt->execute();

			$rows = $stmt->fetchAll();
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	/**
	 * @param int $defId
	 * @param string $t1
	 * @param string $t2
	 * @param string $dt1
	 * @param string $dt2 tanggal akhir dari query,
	 * @return Exception|PDOException
	 */
	static public function getDistinctSubscriber(string $group0, string $t1, string $t2, string $dt1, string $dt2){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStat::SQL_GET_DISTINCT_SUBSCRIBER);

			$stmt->bindValue(':group0', $group0, PDO::PARAM_STR);
			$stmt->bindValue(':t1', $t1, PDO::PARAM_STR);
			$stmt->bindValue(':t2', $t2, PDO::PARAM_STR);
			$stmt->bindValue(':dt1', $dt1, PDO::PARAM_STR);
			$stmt->bindValue(':dt2', $dt2, PDO::PARAM_STR);

			$stmt->execute();

			$rows = $stmt->fetchAll();
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

//	static public function getTopGroup1(int $defId, string $dt1, string $dt2, $limit){
//
//		try{
//			$pdo = Koneksi::create();
//			$stmt = $pdo->prepare(ModelStat::SQL_GET_TOP_GROUP_1);
//
//			$stmt->bindValue(':def_id', $defId, PDO::PARAM_INT);
//			$stmt->bindValue(':dt1', $dt1, PDO::PARAM_STR);
//			$stmt->bindValue(':dt2', $dt2, PDO::PARAM_STR);
//			$stmt->bindValue(':_limit', $limit, PDO::PARAM_INT);
//
//			$stmt->execute();
//
//			$rows = $stmt->fetchAll();
//			return $rows;
//
//		}catch (PDOException $e){
//			Log::writeErrorLn($e->getMessage());
//			return $e;
//		}
//	}

}