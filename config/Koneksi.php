<?php
/**
* 
*/

//if (!defined("default_username")) define("default_username", "web");
//if (!defined("default_password")) define("default_password", "P@ssword%");
//if (!defined("default_hosting")) define("default_hosting", "127.0.0.1");
//if (!defined("database")) define("database", "ott3");

//include dbsetting.php
include_once __DIR__ . '/dbsetting.php';

class Koneksi{
	private $conn;

 	//Danang
	function connect(){

//	    //ambil setting dari php.ini
        $username = USERNAME; //get_cfg_var('setting_db_username');
        $password = PASSWORD; //get_cfg_var('setting_db_password');
        $hosting = HOSTING; //get_cfg_var('setting_db_host');
        $database = DATABASE; //get_cfg_var('setting_db_host');

        $this->conn = new mysqli($hosting, $username, $password, $database);

		if(mysqli_connect_error()){
			return "Failed to connect".mysqli_connect_error();
		}
		else{
			return $this->conn;
		}

	}

	/**
	 * open db connection using PDO
	 *
	 * @return PDO
	 */
	public static function create(){

        //ambil setting dari php.ini
        $username = USERNAME; //get_cfg_var('setting_db_username');
        $password = PASSWORD; //get_cfg_var('setting_db_password');
        $hosting = HOSTING; //get_cfg_var('setting_db_host');
        $database = DATABASE; //get_cfg_var('setting_db_host');

		$dsn = "mysql:host=$hosting;dbname=$database;charset=utf8mb4";
		$opt = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false, //apabila ini false, maka semua field type akan muncul
			PDO::MYSQL_ATTR_FOUND_ROWS => true //rowCount pada update tdk memberikan jumlah row yg di update apabila nilai yg mau di update sudh sama dgn yg ada pada table
		];

		return new PDO($dsn, $username, $password, $opt);
	}
}

?>