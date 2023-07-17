<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/27/2019
 * Time: 11:58 AM
 */

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

class ModelAdmin
{
    //CRUD = 1,2,4,8
    const ACCESS_CREATE = 1;
    const ACCESS_READ = 2; //always true
    const ACCESS_UPDATE = 4;
    const ACCESS_DELETE = 8;

	const SQL_GET_ADMIN_FROM_SESSIONID = 'SELECT * FROM tadmin_session WHERE admin_session_id=? AND expire_date>NOW()';
	const SQL_CHECK_LOGIN = 'SELECT * FROM tadmin WHERE username=? AND hash_password=?';
	const SQL_ADD_SESSION = 'INSERT INTO tadmin_session (admin_id, admin_session_id, salt) VALUES (?, ?, ?)';
	const SQL_GET_ACL = 'SELECT * FROM vadmin_acl WHERE admin_id=?';
	const SQL_GET = 'SELECT * FROM tadmin WHERE admin_id=?';

    public static function get($adminId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET);
            $stmt->execute( [$adminId] );

            $rows = $stmt->fetchAll();
            if (count($rows)==0) return null;
            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function getAcl($adminId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelAdmin::SQL_GET_ACL);
			$stmt->execute( [$adminId] );

			//buat list ACL dgn acl_code sebagai key nya, dan access sebagai value
			$acl = array();
			$list = $stmt->fetchAll();
			foreach ($list as $row){
			    $access = ModelAdmin::ACCESS_READ; //read access always true
			    $code = $row['acl_code'];
			    if ($row['can_create']==1) $access |= ModelAdmin::ACCESS_CREATE;
//                if ($row['can_read']==1) $access |= ModelAdmin::ACCESS_READ;
			    if ($row['can_update']==1) $access |= ModelAdmin::ACCESS_UPDATE;
			    if ($row['can_delete']==1) $access |= ModelAdmin::ACCESS_DELETE;
			    $acl[$code] = $access;
            }

            return $acl;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function getAdminFromSessionId($sessionId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelAdmin::SQL_GET_ADMIN_FROM_SESSIONID);
			$stmt->execute( [$sessionId] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function checkLogin($username, $hashPassword){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelAdmin::SQL_CHECK_LOGIN);
			$stmt->execute( [$username, $hashPassword] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function addSession($adminId, $sessionId, $salt){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelAdmin::SQL_ADD_SESSION);
			$stmt->execute( [$adminId, $sessionId, $salt] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}



}