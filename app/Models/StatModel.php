<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-06 12:27:39
 */
namespace App\Models;

class StatModel extends BaseModel
{
    const SQL_GET_DISTINCT_SUBSCRIBER = 'SELECT count(DISTINCT subscriber_id) as count FROM tstat WHERE group_0=:group0 AND start_date>=:dt1 AND start_date<:dt2 AND (
		(TIME(start_date)>=:t1 AND TIME(end_date)<=:t2) OR
		(TIME(start_date)<:t1 AND TIME(end_date)>:t2) OR
		(TIME(start_date)>=:t1 AND TIME(start_date)<=:t2) OR
		(TIME(end_date)>=:t1 AND TIME(end_date)<=:t2))';

    const SQL_GET_SUM_GROUP_1 = 'SELECT group_1, MIN(`value`) as min, MAX(`value`) as max, SUM(`value`) as total, AVG(`value`) as avg, COUNT(*) as count FROM tstat WHERE group_0=:group0 AND value is not null AND start_date>=:dt1 AND end_date<:dt2 GROUP BY group_0, group_1 ORDER BY `total` DESC LIMIT 10';

    public $errCode;
    public $errMessage;

    public function getDistinctSubscriber(string $group0, string $t1, string $t2, string $dt1, string $dt2){

        try{
            $pdo = $this->openPdo(true); //harus true, karn kalo false akan error 'invalid parameter number'
            $stmt = $pdo->prepare(self::SQL_GET_DISTINCT_SUBSCRIBER);

            $stmt->bindValue(':group0', $group0, \PDO::PARAM_STR);
            $stmt->bindValue(':dt1', $dt1, \PDO::PARAM_STR);
            $stmt->bindValue(':dt2', $dt2, \PDO::PARAM_STR);
            $stmt->bindValue(':t1', $t1, \PDO::PARAM_STR);
            $stmt->bindValue(':t2', $t2, \PDO::PARAM_STR);

            $stmt->execute();

            $rows = $stmt->fetchAll();
            return $rows[0];

        }catch (\PDOException $e){
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();

            return $e;
        }
    }

    public function getSumGroup1(string $group0, string $dt1, string $dt2){

        try{
            $pdo = $this->openPdo(true); //harus true, karn kalo false akan error 'invalid parameter number'
            $stmt = $pdo->prepare(self::SQL_GET_SUM_GROUP_1);
            $stmt->bindValue(':group0', $group0, \PDO::PARAM_STR);
            $stmt->bindValue(':dt1', $dt1, \PDO::PARAM_STR);
            $stmt->bindValue(':dt2', $dt2, \PDO::PARAM_STR);

            $stmt->execute();

            $rows = $stmt->fetchAll();
            return $rows;

        }catch (\PDOException $e){
//            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

}
