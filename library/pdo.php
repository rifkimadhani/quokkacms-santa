<?php
/**
 * Created by PhpStorm.
 * User: echri
 * Date: 26/10/2022
 * Time: 14:05
 */

class PdoUtil {

    static public function update(PDO $conn, $table, $keys, $data){

        $where = self::_buildQuery($keys, ' AND');
        $set = self::_buildQuery($data, ',');

        $sql = "UPDATE $table SET $set WHERE $where";

        $statement = $conn->prepare($sql);
        self::_buildBindValue($statement, $data);
        self::_buildBindValue($statement, $keys);
        $count = $statement->execute();

        return $count;
    }

    static function _buildQuery($list, $stringExtra){
        $query = '';
        foreach($list as $key => $value)
        {
            if(strlen($query)==0){
                $query = "`$key`=:$key";
            } else {
                $query .= "$stringExtra `$key`=:$key";
            }
        }
        return $query;
    }

    static function _buildBindValue($stmt, $list){
        foreach($list as $key => $value)
        {
            $stmt->bindValue(':' . $key, $value);
        }
    }
}