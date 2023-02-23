<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/23/2023
 * Time: 8:44 AM
 */

namespace App\Models;

class BuilderModel extends BaseModel
{
    const SQL_GET_TABLE = 'SHOW TABLES';

    public function getTableList(){

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_GET_TABLE);
            $stmt->execute( [] );
            $rows = $stmt->fetchAll();

            //buat list baru yg berisikan hanya nama table saja
            $ar = [];
            foreach ($rows as $key=>$item){
                //nama table ada pada array item,
                //hanya bagian value saja yg di butuhkan
                $ar[] = array_values($item)[0]; //add nama table ke $ar
            }

            return $ar;

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }

}