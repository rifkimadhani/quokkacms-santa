<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/4/2019
 * Time: 12:40 PM
 */

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

class ModelStb
{
	const SQL_GET_FROM_MAC = 'SELECT * FROM vstb_mac_session WHERE mac_address=?';
	const SQL_GET_FROM_SESSIONID = 'SELECT * FROM vstb_mac_session WHERE session_id=?';
	const SQL_GET = 'SELECT * FROM vstb_room WHERE stb_id=?';
	const SQL_UPDATE_INFO = 'UPDATE tstb SET ip_address=?, app_id=?, version_name=?, version_code=?, android_version=?, android_api=? WHERE stb_id=?';
	const SQL_UPDATE_LAST_SEEN = 'UPDATE tstb SET last_seen=NOW() WHERE stb_id=?';
	const SQL_UPDATE_STB_SESSION_LAST_SEEN = 'UPDATE tstb_session SET last_seen=NOW() WHERE stb_id=? AND session_id=?';
	const SQL_UPDATE_STB_MAC_LAST_SEEN = 'UPDATE tstb_mac SET last_seen=NOW() WHERE stb_id=? AND mac_address=?';
	const SQL_REGISTER_MACADDRESS = 'INSERT INTO tstb_mac (mac_address, stb_id) VALUES (?, ?)';
	const SQL_CREATE_STBSESSION = 'INSERT INTO tstb_session (session_id, stb_id) VALUES (?, ?)';
	const SQL_GET_PARTIAL_BY_KEYWORD = 'SELECT * FROM vstb_room WHERE stb_name LIKE :_like LIMIT :_limit OFFSET :_offset';
	const SQL_GET_ALL = 'SELECT * FROM tstb';
	const SQL_GET_BY_ROOM = 'SELECT stb_id, ip_address FROM vstb_room WHERE vstb_room.room_id=? AND ip_address IS NOT NULL';
	const SQL_UPDATE_STATUS = 'UPDATE tstb SET `status`=? WHERE stb_id=?';
	const SQL_UPDATE_IPADDRESS = 'UPDATE tstb SET ip_address=?, last_seen=NOW() WHERE stb_id=?';
	const SQL_GET_ALL_ACTIVE = 'SELECT stb_id, ip_address FROM tstb WHERE status=1 AND ip_address IS NOT NULL';

	public static function getAll(){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_GET_ALL);
			$stmt->execute( );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function getByRoom($roomId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_GET_BY_ROOM);
			$stmt->execute( [$roomId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function getPartialByKeyword($keyword, $offset, $limit){

		$like = "%{$keyword}%";

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_GET_PARTIAL_BY_KEYWORD);
			$stmt->bindValue(':_like', $like, PDO::PARAM_STR);
			$stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
			$stmt->bindValue(':_limit', $limit, PDO::PARAM_INT);

			$stmt->execute();
//			$stmt->execute( [ $like, $limit, $offset] );

			$rows = $stmt->fetchAll();

			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	public static function get($stbId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_GET);
			$stmt->execute( [ $stbId] );

			$rows = $stmt->fetchAll();

			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function getFromMac($mac){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_GET_FROM_MAC);
			$stmt->execute( [ $mac] );

			$rows = $stmt->fetchAll();

			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}
	public static function getFromSessionId($sessionId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_GET_FROM_SESSIONID);
			$stmt->execute( [ $sessionId] );

			$rows = $stmt->fetchAll();

			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	public static function updateInfo($stbId, $ip, $appId, $versionName, $versionCode, $androidVersion, $androidApi){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_UPDATE_INFO);
			$stmt->execute( [$ip, $appId, $versionName, $versionCode, $androidVersion, $androidApi, $stbId ] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	public static function updateLastSeen($stbId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_UPDATE_LAST_SEEN);
			$stmt->execute( [$stbId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	public static function updateStbSessionLastSeen($stbId, $sessionId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_UPDATE_STB_SESSION_LAST_SEEN);
			$stmt->execute( [$stbId, $sessionId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	public static function updateStbMacLastSeen($stbId, $mac){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_UPDATE_STB_MAC_LAST_SEEN);
			$stmt->execute( [$stbId, $mac] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	public static function registerMacaddress($stbId, $macaddress){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_REGISTER_MACADDRESS);
			$stmt->execute( [$macaddress, $stbId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			if ($e->getCode()=='23000') return 0;
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	public static function createStbSession($stbId, $stb_session){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_CREATE_STBSESSION);
			$stmt->execute( [$stb_session, $stbId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			if ($e->getCode()=='23000') return 0;
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	public static function updateStatus(int $stbId, int $status){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_UPDATE_STATUS);
			$stmt->execute( [$status, $stbId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			return $e;
		}
	}

	public static function updateIpaddress(int $stbId, $ip){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelStb::SQL_UPDATE_IPADDRESS);
			$stmt->execute( [$ip, $stbId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			return $e;
		}
	}

    public static function getAllActive(){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelStb::SQL_GET_ALL_ACTIVE);
            $stmt->execute( );

            $rows = $stmt->fetchAll();
            return $rows;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

	/**
	 * Get STB by name, or create new one if not exists
	 * Used for batch installation where user provides device names
	 *
	 * @param string $name - Device name (e.g., "TV-Room-101")
	 * @param string $ipAddress - IP address of the device
	 * @return int|null - stb_id if found/created, null on error
	 */
	public static function getOrCreateByName($name, $ipAddress = null){
		try{
			$pdo = Koneksi::create();

			// Try to find existing STB by name
			$stmt = $pdo->prepare('SELECT stb_id FROM tstb WHERE name = ?');
			$stmt->execute([$name]);
			$rows = $stmt->fetchAll();

			if (count($rows) > 0) {
				$stbId = $rows[0]['stb_id'];

				// Update IP address if provided
				if ($ipAddress) {
					$updateStmt = $pdo->prepare('UPDATE tstb SET ip_address = ? WHERE stb_id = ?');
					$updateStmt->execute([$ipAddress, $stbId]);
				}

				return $stbId;
			}

			// Create new STB
			$insertStmt = $pdo->prepare('INSERT INTO tstb (name, ip_address, status) VALUES (?, ?, 1)');
			$insertStmt->execute([$name, $ipAddress]);

			return $pdo->lastInsertId();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return null;
		}
	}
}