<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 10:41 AM
 */

namespace App\Models;

use App\Libraries\SSP;
use CodeIgniter\Model;

class BaseModel extends Model
{
    /**
     * open pdo utk melakukan update lewat pdo
     *
     * @return \PDO
     */
    protected function openPdo(){
        $username = $_ENV['database.default.username'];
        $password = $_ENV['database.default.password'];
        $hosting = $_ENV['database.default.hostname'];
        $database = $_ENV['database.default.database'];

        $dsn = "mysql:host=$hosting;dbname=$database;charset=utf8mb4";
        $opt = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false, //apabila ini false, maka semua field type akan muncul
            \PDO::MYSQL_ATTR_FOUND_ROWS => true //rowCount pada update tdk memberikan jumlah row yg di update apabila nilai yg mau di update sudh sama dgn yg ada pada table
        ];

        return new \PDO($dsn, $username, $password, $opt);
    }

    /**
     * internal implementation
     *
     * @param $table
     * @param $pk
     * @param $field_list
     * @return array
     */
    protected function _getSsp($table, $pk, $fieldList)
    {
        require_once __DIR__ . '/../../library/ssp.class.php';

        $columns = [];

        foreach($fieldList as $key => $value)
        {
            //add semua field ke columns
            $columns[] = ['db' =>$value,'dt'=>$key];
        }

        $con = array(
            "host"=>$_ENV['database.default.hostname'],
            "user"=>$_ENV['database.default.username'],
            "pass"=>$_ENV['database.default.password'],
            "db"=>$_ENV['database.default.database'],
        );

        return SSP::simple($_GET, $con, $table, $pk, $columns);
    }

    protected function _getSspComplex($table, $pk, $fieldList, $where)
    {
        require_once __DIR__ . '/../../library/ssp.class.php';

        $columns = [];

        foreach($fieldList as $key => $value)
        {
            //add semua field ke columns
            $columns[] = ['db' =>$value,'dt'=>$key];
        }

        $con = array(
            "host"=>$_ENV['database.default.hostname'],
            "user"=>$_ENV['database.default.username'],
            "pass"=>$_ENV['database.default.password'],
            "db"=>$_ENV['database.default.database'],
        );

        return SSP::complex($_GET, $con, $table, $pk, $columns, $where);
    }
}