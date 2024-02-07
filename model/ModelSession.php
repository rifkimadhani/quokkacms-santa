<?php

require_once __DIR__ . '/ModelLog.php';
require_once __DIR__ . '/../config/Koneksi.php';

class ModelSession {
    const SQL_CREATE = 'INSERT INTO tsession (session_id,user_id,salt,exp_date, device_type, login_type) VALUES (?, ?, ?, DATE_ADD(now(),INTERVAL 36500 DAY), ?, ?)';
    const SQL_GET = 'SELECT * FROM tsession WHERE session_id=?';
    const SQL_UPDATE_TOKEN = 'UPDATE tsession SET device_type=?, device_token=? WHERE session_id=?';

    static public function create($userId, $deviceType, $loginType)
    {
        require_once __DIR__ . '/../library/Security.php';

        //Keep create until user is created;
        do {
                $sessionId = Security::random(24);
                $salt = Security::random(12);
                $result = self::_create($userId, $sessionId, $salt, $deviceType, $loginType);
                if ($result == 1) return $sessionId;
        } while (true);

        return null;
    }

    static function _create($userId, $sessionId, $salt, $deviceType, $loginType) {

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_CREATE);
            $stmt->execute( [$sessionId, $userId, $salt, $deviceType, $loginType] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            if ($e->getCode()=='23000') return 0;
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function get($sessionId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET);
            $stmt->execute( [$sessionId] );

            $rows = $stmt->fetchAll();
            if (count($rows)==0) return null;
            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    /**
     * @param $sessionId
     * @return null=not found or expired, user_id
     */
    static public function validate($sessionId)
    {
        $item = self::get($sessionId);

        if (isset($item) == false) return null;

        $now = time();
        $expDate = strtotime($item['exp_date']);
        //return null apabila sudah expired
        if ($expDate < $now) return null;

        return $item['user_id'];
    }

    static public function updateToken($sessionId, $deviceType, $token){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE_TOKEN);
            $stmt->execute( [$deviceType, $token, $sessionId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }

    }
}

//class SessionItem
//{
//    var $sessionId;
//    var $userId;
//    var $salt;
//    var $createdDate;
//    var $expDate;
//}
//
//define('DEVICE_TYPE_ANDROID_STB', 'ANDROID-STB'); //android stb
//define('DEVICE_TYPE_ANDROID', 'ANDROID'); //android hp
//define('DEVICE_TYPE_IOS', 'IOS'); //iphone

//class ModelSession
//{
//    const TAG = "ModelSession";
//
//    const SQL_CREATEA = "INSERT INTO tsession (sessionId,userId,salt,expDate, device_type) VALUES (?, ?, ?, DATE_ADD(now(),INTERVAL 36500 DAY), ?)";
//    const SQL_GET = "SELECT sessionId, userId, salt, createdDate, expDate FROM tsession WHERE sessionId=?";
//    const SQL_GETFROMUSERID = "SELECT sessionId, userId, salt, createdDate, expDate FROM tsession Where userId = ? LIMIT 1";
//    const SQL_UPDATEEXPDATE = "UPDATE tsession SET expDate=DATE_ADD(now(),INTERVAL 7 DAY) WHERE sessionId=?";
//    const SQL_GETSALT = "SELECT salt FROM tsession WHERE sessionId=?";
//    //lebih kecil dari expired date
//    const SQL_VALIDATE_EXP_SESS = "SELECT sessionId, userId, salt, createdDate, expDate FROM tsession Where sessionId = ? AND expDate < now() LIMIT 1";
//    //lebih besar dari expired date
//    const SQL_VALIDATE_EXP_SESSION = "SELECT sessionId, userId, salt, createdDate, expDate FROM tsession Where sessionId = ? AND expDate > now() LIMIT 1";
//    const UPDATE_DEVICE_TYPE = "UPDATE tsession SET deviceToken = ?, type = ?  WHERE sessionId = ?";
//    const SQL_GET_DEVICETOKEN = "SELECT deviceToken FROM tsession WHERE sessionId = ?";
//    const SQL_GET_GETDEVICETOKENS_FROM_USERID = "SELECT deviceToken FROM vdevicetoken WHERE userId= ? AND type='fcm'";
//
//    function __construct()
//    {
//        require_once '../config/Koneksi.php';
//        require_once '../library/Security.php';
//        require_once '../library/Log.php';
//    }
//
//    public function create($userId, $deviceType=DEVICE_TYPE_ANDROID)
//    {
//
//        //Keep create until session is created;
//        do {
//            $sessionId = Security::random(24);
//            $result = self::createA($userId, $sessionId, $deviceType);
//            if (isset($result)) return $result;
//        } while (true);
//    }
//
//    /***
//     * Create session
//     * @param unknown $userId
//     * @param unknown $sessionId
//     * @return number return object if success, null if not success (duplicate sessionId)
//     *
//     * expired di set 100 thn
//     */
//    private function createA(string $userId, string $sessionId, $deviceType=DEVICE_TYPE_ANDROID): ?SessionItem
//    {
//
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_CREATEA);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//
//        $salt = Security::random(12);
//        $stmt->bind_param("ssss", $sessionId, $userId, $salt, $deviceType);
//        if ($stmt->execute()) {
//            $item = new SessionItem();
//            $item->userId = $userId;
//            $item->sessionId = $sessionId;
//            $item->salt = $salt;
//        } else $item = null;
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }
//
//    public static function get($sessionId): ?SessionItem
//    {
//
//        Log::writeLn("\tModelSession.get({$sessionId})");
//
//        $Koneksi = new Koneksi();
//
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $sessionId);
//        $stmt->execute();
//
//        /* instead of bind_result: */
//        $result = $stmt->get_result();
//
//        $item = null;
//
//        if ($row = $result->fetch_assoc()) {
//            $item = self::saveToItem($row);
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }
//
//    /***
//     * ambil sessionId dari userId
//     * sessionId yg di ambil bisa saja sudah expired, agar tdk terjadi penumpukkan session
//     */
//    public function getFromUserId($userId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GETFROMUSERID);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $userId);
//        $stmt->execute();
//
//        $item = null;
//        $result = $stmt->get_result();
//        if ($row = $result->fetch_assoc()) {
//            $item = self::saveToItem($row);
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }
//
//    /***
//     * Update expDate from sessionId
//     * @param String $sessionId
//     * @return boolean
//     */
//    public function cekExpSession($sessionId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_VALIDATE_EXP_SESSION);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $sessionId);
//        $stmt->execute();
//
//        $item = null;
//        $result = $stmt->get_result();
//        if ($row = $result->fetch_assoc()) {
//            $item = self::saveToItem($row);
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }
//
//    public function cekExpSess($sessionId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_VALIDATE_EXP_SESS);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $sessionId);
//        $stmt->execute();
//
//        $item = null;
//        $result = $stmt->get_result();
//        if ($row = $result->fetch_assoc()) {
//            $item = self::saveToItem($row);
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }
//
//    public function updateExpDate($sessionId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATEEXPDATE);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return 0;
//        }
//        $stmt->bind_param('s', $sessionId);
//        $result = $stmt->execute();
//        $stmt->close();
//        $conn->close();
//
//        return $result;
//    }
//
//    public function updateDeviceType($token, $type, $sessionId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::UPDATE_DEVICE_TYPE);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return 0;
//        }
//        $stmt->bind_param('sss', $token, $type, $sessionId);
//        $result = $stmt->execute();
//        $stmt->close();
//        $conn->close();
//
//        return $result;
//    }
//
//    public function getSalt($sessionId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GETSALT);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return 0;
//        }
//        $stmt->bind_param('s', $sessionId);
//
//        $salt = null;
//        if ($stmt->execute()) {
//            $stmt->bind_result($salt);
//            $stmt->fetch();
//
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $salt;
//    }
//
//    /** Ambil device token dari sessionId
//     * @param $sessionId
//     * @return string|null => deviceToken
//     */
//    public function getDeviceToken($sessionId): ?string
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_DEVICETOKEN);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $sessionId);
//
//        $deviceToken = null;
//        if ($stmt->execute()) {
//            $stmt->bind_result($deviceToken);
//            $stmt->fetch();
//        } else {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $deviceToken;
//    }
//
//    public function getDeviceTokensFromUserId(string $userId): ?array
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_GETDEVICETOKENS_FROM_USERID);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $userId);
//
//        $list = array();
//        if ($stmt->execute()) {
//            $stmt->bind_result($deviceToken);
//            while ($stmt->fetch()) {
//                array_push($list, $deviceToken);
//            }
//        } else {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $list;
//    }
//
//    /***
//     * validate sessionId
//     * @param String $sessionId
//     * @return NULL or userId
//     */
//
//    public function validate($sessionId)
//    {
//        $item = self::get($sessionId);
//
//        if (isset($item) == false) return null;
//
//        $now = date('Y-m-d H:i:s');
//        //return null apabila sudah expired
//        if ($item->expDate < $now) return null;
//
//        return $item->userId;
//    }
//
//    public function validateSignature($sessionId, $inputString, $sig)
//    {
//
//        if (isset($inputString) == false) return 0;
//        if (isset($sig) == false) return 0;
//
//        $salt = self::getSalt($sessionId);
//        if (isset($salt) == false) return 0;
//
//        $genSig = Security::genHash($inputString, $salt);
//
//        if (strcasecmp($genSig, $sig) != 0) {
//            Log::writeErrorLn("Invalid signature " . $genSig . " <> " . $sig);
//            return 0;
//        }
//        Log::writeLn("Signature valid");
//
//        return 1;
//    }
//
//    private static function saveToItem($row)
//    {
//        $item = new SessionItem();
//        $item->sessionId = $row['sessionId'];
//        $item->userId = $row['userId'];
//        $item->salt = $row['salt'];
//        $item->createdDate = $row['createdDate'];
//        $item->expDate = $row['expDate'];
//        return $item;
//    }
//}

