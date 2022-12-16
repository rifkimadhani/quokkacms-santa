<?php

/**
 * implementasi model disini ada 2 type, type lama yg masih mempergunakan cara2 kuno
 * dan cara baru yg mempergunakan Connection.php
 */
require_once __DIR__ . '/../../config/Koneksi.php';

class ModelUser
{
    const TAG = "ModelUser";
    const USERID_LENGTH = 10;

    const SQL_CREATE = "INSERT INTO tuser (username) VALUES (null)";

    const SQL_GET = "SELECT * FROM tuser WHERE user_id=?";
    const SQL_GET_EMAIL = "SELECT * FROM tuser WHERE email = ?";
    const SQL_GET_FACEBOOK_ID = "SELECT * FROM tuser WHERE facebookId = ?";
    const SQL_GET_GOOGLE_ID = "SELECT * FROM tuser WHERE googleId = ?";
    const SQL_GET_INSTAGRAM_ID = "SELECT * FROM tuser WHERE instagram_user_id=?";

    const SQL_GET_BY_MSISDN = 'SELECT * FROM tuser WHERE msisdn=?';
    const SQL_UPDATE_FACEBOOKID = "UPDATE tuser SET facebookId=? WHERE user_id = ?";
    const SQL_UPDATE_GOOGLEID = "UPDATE tuser SET googleId=? WHERE user_id = ?";

    const SQL_UPDATE_HASH = 'UPDATE tuser SET hash=?, salt=? WHERE user_id=?';
    const SQL_UPDATE_MSISDN = 'UPDATE tuser SET msisdn=?, msisdn_state=1 WHERE user_id=?';
    const SQL_UPDATE_EMAIL = 'UPDATE tuser SET email=?, email_state=1 WHERE user_id=?';
    const SQL_UPDATE_MSISDN_CODE = 'UPDATE tuser SET msisdn_code=?, msisdn_code_exp=?, msisdn_code_resend=?, msisdn_code_count=msisdn_code_count+1 WHERE user_id=?';
    const SQL_UPDATE_EMAIL_CODE = 'UPDATE tuser SET email_code=?, email_code_exp=?, email_code_resend=?, email_code_count=email_code_count+1 WHERE user_id=?';
    const SQL_UPDATE_MSISDN_STATE = 'UPDATE tuser SET msisdn_state=? WHERE user_id=?';
    const SQL_UPDATE_EMAIL_STATE = 'UPDATE tuser SET email_state=? WHERE user_id=?';

//    const SQL_GET_FROM_USERNAME = "SELECT userId, username, hash, createdDate, lastSeen, regState, isBlock, facebookId,googleId FROM tuser WHERE username=?";
//    const SQL_VALIDATE = "SELECT userId, regState, isBlock FROM tuser WHERE userId=? AND hash=?";
//    const SQL_VALIDATES = "SELECT userId, regState, isBlock FROM tuser WHERE username=? AND hash=?";
//    const SQL_VALIDATE_DATA_FACEBOOK = "SELECT * FROM tuser WHERE userId = ? AND username = ? AND facebookId = ? ";
//    const SQL_GET_ALL_WITH_USERNAME = "SELECT * FROM tuser WHERE username = ?";
//    const SQL_GET_ALL_WITH_USERNAME_VERIFIED = "SELECT * FROM tuser WHERE username = ? and regState=2";
//    const SQL_GET_ALL_WITH_FACEBOOK_ID = "SELECT * FROM tuser WHERE facebookId = ?";
//    const SQL_SET_USER = "INSERT INTO tuser (userId,username,facebookId,regState) VALUES (?,?,?,?)";
//    const SQL_SET_USER_WITH_IDUSER = "UPDATE tuser SET username = ?, facebookId = ?, regState = ? WHERE userId = ?";
//    const SQL_UPDATE_USER_WITH_FACEBOOKID = "UPDATE tuser SET username = ? WHERE facebookId = ? AND userId = ?";
//    const SQL_UPDATE_USER_WITH_EMAIL = "UPDATE tuser SET facebookId = ? WHERE username = ? AND userId = ?";
//    const SQL_GET_USER_WITH_EMAIL = "SELECT * FROM tuser WHERE username = ? ";
//    const SQL_GET_USER_ID = "SELECT userId FROM tuser WHERE username = ? AND facebookId = ?";
//    const SQL_SET_REGISTER = "UPDATE tuser SET username = ?, hash = ?, regState = ? WHERE userId = ?";
//    const SQL_GET_ALL_NICKNAME = "SELECT nickName FROM tuser WHERE nickName = ?";
//    const SQL_UPDATE_STATE = "UPDATE tuser SET regState = 2 WHERE userId = ?";
//    const SQL_UPDATE_WITH_USERID = "UPDATE tuser SET facebookId = ?, username = ?, regState = ? WHERE userId = ?";
//    const SQL_VALID_DATA_FB = "SELECT * FROM tuser WHERE userId = ? AND username = ?";
//    const SQL_UPDATE_USER_REGISTER = "UPDATE tuser SET username = ?, hash = ?, regState = ? WHERE userId = ?";
//    const SQL_GET_ALL_WITH_FBID_USERID = "SELECT * FROM tuser WHERE userId = ? AND facebookId = ?";
//    const SQL_UPDATE_REG_STATE = "UPDATE tuser SET regState=? WHERE userId = ?";
//    //msisdnstate=1 untuk handle yang user pada saat form aplikasi cancel verification
//    const SQL_UPDATE_PASSWORD = "UPDATE tuser SET hash=?, msisdnState='1' WHERE userId = ?";
//
//    const SQL_UPDATE_GOOGLEID = "UPDATE tuser SET googleId=? WHERE userId = ?";
//    const SQL_UPDATE = "UPDATE tuser SET username=?, regState=? WHERE userId = ?";
//
//    const SQL_GET_BY_MSISDN_VERIFIED = 'SELECT userId, msisdnState FROM tuser WHERE msisdn=? and msisdnState=1';
//    const SQL_UPDATE_MSISDN = "UPDATE tuser SET msisdn=?,hash=?,msisdnState=? WHERE userId = ?";
//    const SQL_UPDATE_MSISDN_STATE = "UPDATE tuser SET msisdnState = 1 WHERE userId = ?";
//
//    //login by msisdn
//    const SQL_VALIDATES_MSISDN = "SELECT userId, msisdnState, isBlock FROM tuser WHERE msisdn=?";
//    const SQL_VALIDATES_WITH_PASSWORD_MSISDN = "SELECT userId, msisdnState, isBlock FROM tuser WHERE msisdn=? and hash=?";
//
//    const SQL_GET_BY_USERID = "SELECT * FROM tuser WHERE userId=?";
//
//    //delete user stb
//    const SQL_DELETE_USERID = "DELETE FROM tuser WHERE userId=?";

    static function create() {

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_CREATE);
            $stmt->execute( [] );

            return $pdo->lastInsertId();

        }catch (PDOException $e){
            if ($e->getCode()=='23000') return 0;
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static function update(int $userId, $data){
        require_once __DIR__ . '/../../library/pdo.php';

        try{
            $pdo = Koneksi::create();

            $r = PdoUtil::update($pdo, 'tuser', ['user_id'=>$userId], $data);

            return $r;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function get($userId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET);
            $stmt->execute( [$userId] );

            $rows = $stmt->fetchAll();
            if (count($rows)==0) return null;
            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    /**
     * Function ini sama dgn getAllWithFacebookId
     *
     * yg berbeda func ini mempergunakan cara yg baru
     *
     * @param $facebookId
     * @return Exception|null|PDOException
     */
    static public function getByFacebook($facebookId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_FACEBOOK_ID);
            $stmt->execute( [$facebookId] );

            $rows = $stmt->fetchAll();
            if (count($rows)==0) return null;
            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function getByGoogle($googleId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_GOOGLE_ID);
            $stmt->execute( [$googleId] );

            $rows = $stmt->fetchAll();
            if (count($rows)==0) return null;
            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    public static function getByInstagram(string $instagramUserId) {
        try {
            $pdo = Koneksi::create();

            $stmt = $pdo->prepare(self::SQL_GET_INSTAGRAM_ID);

            $stmt->execute([$instagramUserId]);

            if ($stmt->rowCount()==0) return null;

            return $stmt->fetchAll()[0];

        } catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function getByEmail($email){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_EMAIL);
            $stmt->execute( [$email] );

            $rows = $stmt->fetchAll();
            if (count($rows)==0) return null;
            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function getByMsisdn($msisdn){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_BY_MSISDN);
            $stmt->execute( [$msisdn] );

            $rows = $stmt->fetchAll();
            if (count($rows)==0) return null;
            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function updateFacebook($userId, $facebookId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE_FACEBOOKID);
            $stmt->execute( [$facebookId, $userId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function updateGoogle($userId, $googleId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE_GOOGLEID);
            $stmt->execute( [$googleId, $userId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function updateHash($userId, $hash, $salt){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE_HASH);
            $stmt->execute( [$hash, $salt, $userId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function updateMsisdn($userId, $msisdn){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE_MSISDN);
            $stmt->execute( [$msisdn, $userId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function updateEmail($userId, $email){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE_EMAIL);
            $stmt->execute( [$email, $userId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function updateMsisdnCode($userId, $code, $exp, $resend){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE_MSISDN_CODE);
            $stmt->execute( [$code, $exp, $resend, $userId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function updateEmailCode($userId, $code, $exp, $resend){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE_EMAIL_CODE);
            $stmt->execute( [$code, $exp, $resend, $userId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function updateMsisdnState($userId, $state){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE_MSISDN_STATE);
            $stmt->execute( [$state, $userId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function updateEmailState($userId, $state){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_UPDATE_EMAIL_STATE);
            $stmt->execute( [$state, $userId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }


//    function __construct()
//    {
//        require_once '../config/Koneksi.php';
//        require_once '../model/ModelUser.php';
//        require_once '../library/Security.php';
//    }
//
//    public function get($userId): ?UserItem
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $userId);
//        $item = null;
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId, $username, $hash, $createdDate, $lastSeen, $regState, $isBlock);
//            if ($stmt->fetch()) {
//                $item = new UserItem();
//                $item->userId = $userId;
//                $item->username = $username;
//                $item->hash = $hash;
//                $item->createdDate = $createdDate;
//                $item->lastSeen = $lastSeen;
//                $item->regState = $regState;
//                $item->isBlock = $isBlock;
//            }
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }
//
//    public function getFromUsername($username)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_FROM_USERNAME);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $username);
//        $item = null;
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId, $username, $hash, $createdDate, $lastSeen, $regState, $isBlock, $facebookId, $googleId);
//            if ($stmt->fetch()) {
//                $item = new UserItem();
//                $item->userId = $userId;
//                $item->username = $username;
//                $item->hash = $hash;
//                $item->createdDate = $createdDate;
//                $item->lastSeen = $lastSeen;
//                $item->regState = $regState;
//                $item->isBlock = $isBlock;
//                $item->facebookId = $facebookId;
//                $item->googleId = $googleId;
//
//            }
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }

    /***
     * Create userId
     * @param String $userId
     * @return number: 0 = fail, 1= success
     */
//    public function createA($userId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_CREATE);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return 0;
//        }
//
//        $stmt->bind_param("s", $userId);
//        if ($stmt->execute()) {
//            Log::writeLn("\t" . self::TAG . ".createA " . $userId . " success");
//            $result = 1;
//        } else $result = 0;
//        $stmt->close();
//        $conn->close();
//
//        return $result;
//    }
//
//    /***
//     * @param string $email
//     * @param string $hash
//     * @return null|UserItem (only userId, regState and isBlock filled)
//     */
//    public function validate(string $userId, string $hash)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_VALIDATE);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('ss', $userId, $hash);
//        $item = null;
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId, $regState, $isBlock);
//            if ($stmt->fetch()) {
//                $item = new UserItem();
//                $item->userId = $userId;
//                $item->regState = $regState;
//                $item->isBlock = $isBlock;
//            }
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }
//
//    public function validates(string $email, string $hash)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_VALIDATES);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('ss', $email, $hash);
//        $item = null;
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId, $regState, $isBlock);
//            if ($stmt->fetch()) {
//                $item = new UserItem();
//                $item->userId = $userId;
//                $item->regState = $regState;
//                $item->isBlock = $isBlock;
//                Log::writeLn(self::TAG . "." . __FUNCTION__ . " = sucess");
//            } else {
//                Log::writeLn(self::TAG . "." . __FUNCTION__ . " = fail | hash='{$hash}'");
//            }
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }
//
//
//    // Bagian Registrai dengan facebook//
//
//
//    //validasi email sudah terdaftar atau belum
//    public function getUsername(string $username)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_EMAIL);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $username);
//        if ($stmt->execute()) {
//            $stmt->bind_result($username);
//            if ($stmt->fetch()) {
//                $username = $username;
//            }
//        }
//        $stmt->close();
//        $conn->close();
//
//
//        return $username;
//    }
//
//    // validasi facebooId udah ada atau belum
//    public function getFacebookId($facebookid)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_FACEBOOK_ID);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $facebookid);
//        if ($stmt->execute()) {
//            $stmt->bind_result($facebookid);
//            if ($stmt->fetch()) {
//                $facebookid = $facebookid;
//            }
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $facebookid;
//    }
//
//    //validai user id berdaarkan username
//    public function validateDataFB($userId, $username)
//    {
//
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_VALID_DATA_FB);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('ss', $userId, $username);
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId, $username, $hash, $createdDate, $lastSeen, $regState, $isBlock, $nickName, $facebookId);
//            if ($stmt->fetch()) {
//                $items = new UserItem();
//                $items->userId = $userId;
//                $items->username = $username;
//                $items->hash = $hash;
//                $items->createdDate = $createdDate;
//                $items->lastSeen = $lastSeen;
//                $items->regState = $regState;
//                $items->isBlock = $isBlock;
//                $items->nickName = $nickName;
//                $items->facebookId = $facebookId;
//            } else {
//                $items = NULL;
//            }
//        }
//        $stmt->close();
//        $conn->close();
//        return $items;
//    }
//
//    // validasi data faebook berdarkan uerId username dan facebook id
//    public function validateDataFacebook($userId, $username, $facebookId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_VALIDATE_DATA_FACEBOOK);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('sss', $userId, $username, $facebookId);
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId, $username, $hash, $createdDate, $lastSeen, $regState, $isBlock, $nickName, $facebookId);
//            if ($stmt->fetch()) {
//                $items = new UserItem();
//                $items->userId = $userId;
//                $items->username = $username;
//                $items->hash = $hash;
//                $items->createdDate = $createdDate;
//                $items->lastSeen = $lastSeen;
//                $items->regState = $regState;
//                $items->isBlock = $isBlock;
//                $items->nickName = $nickName;
//                $items->facebookId = $facebookId;
//            } else {
//                $items = NULL;
//            }
//        }
//        $stmt->close();
//        $conn->close();
//        return $items;
//    }
//
//    //set username ketika data sama sekali tidak ada atau baru login dengan facebook tanpa guest
//    public function setUser($username, $facebookId, $userId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $regState = 2;
//        if (empty($userId)) {
//            $userId = Security::random(10);
//            $stmt = $conn->prepare(self::SQL_SET_USER);
//            if ($stmt == false) {
//                Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//                return null;
//            }
//            $stmt->bind_param('sssi', $userId, $username, $facebookId, $regState);
//            $stmt->execute();
//            $stmt->close();
//            $conn->close();
//            return $userId;
//        } else {
//            $stmt = $conn->prepare(self::SQL_SET_USER_WITH_IDUSER);
//            if ($stmt == false) {
//                Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//                return null;
//            }
//            $stmt->bind_param('ssis', $username, $facebookId, $regState, $userId);
//            if ($stmt->execute()) {
//                return $userId;
//            } else {
//                return "Failed";
//            }
//            $stmt->close();
//            $conn->close();
//
//        }
//    }
//
//    // !!!!!! dapatkan data berdasarkan username atau email facebook untuk registrasi juga
//    public function getAllWithUsername($username)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_ALL_WITH_USERNAME);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $username);
//        if ($stmt->execute()) {
////            $stmt->bind_result($userId, $username, $hash, $createdDate, $lastSeen, $regState, $isBlock, $nickName, $facebookId);
////            if ($stmt->fetch()){
//            $items = null;
//            $result = $stmt->get_result();
//            while ($row = $result->fetch_assoc()) {
//                $items = new UserItem();
//                $items->userId = $row['userId'];
//                $items->username = $row['username'];
//                $items->hash = $row['hash'];
//                $items->createdDate = $row['createdDate'];
//                $items->lastSeen = $row['lastSeen'];
//                $items->regState = $row['regState'];
//                $items->isBlock = $row['isBlock'];
//                $items->nickName = $row['nickName'];
//                $items->facebookId = $row['facebookId'];
//            }
//
//        }
//
//        $stmt->close();
//        $conn->close();
//        return $items;
//
//    }
//
//    public function getAllWithUsernameVerified($username)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_ALL_WITH_USERNAME_VERIFIED);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $username);
//        if ($stmt->execute()) {
////            $stmt->bind_result($userId, $username, $hash, $createdDate, $lastSeen, $regState, $isBlock, $nickName, $facebookId);
////            if ($stmt->fetch()){
//            $items = null;
//            $result = $stmt->get_result();
//            while ($row = $result->fetch_assoc()) {
//                $items = new UserItem();
//                $items->userId = $row['userId'];
//                $items->username = $row['username'];
//                $items->hash = $row['hash'];
//                $items->createdDate = $row['createdDate'];
//                $items->lastSeen = $row['lastSeen'];
//                $items->regState = $row['regState'];
//                $items->isBlock = $row['isBlock'];
//                $items->nickName = $row['nickName'];
//                $items->facebookId = $row['facebookId'];
//            }
//
//        }
//
//        $stmt->close();
//        $conn->close();
//        return $items;
//
//    }
//
//    //cari semua data berdasarkan facebook id
////    public function getAllWithFacebookId($facebookId)
////    {
////        $Koneksi = new Koneksi();
////        $conn = $Koneksi->connect();
////        $stmt = $conn->prepare(self::SQL_GET_ALL_WITH_FACEBOOK_ID);
////        if ($stmt == false) {
////            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
////            return null;
////        }
////        $stmt->bind_param('s', $facebookId);
////        if ($stmt->execute()) {
////            $stmt->bind_result($userId, $username, $hash, $createdDate, $lastSeen, $regState, $isBlock, $nickName, $facebookId);
////            if ($stmt->fetch()) {
////                $items = new UserItem();
////                $items->userId = $userId;
////                $items->username = $username;
////                $items->hash = $hash;
////                $items->createdDate = $createdDate;
////                $items->lastSeen = $lastSeen;
////                $items->regState = $regState;
////                $items->isBlock = $isBlock;
////                $items->nickName = $nickName;
////                $items->facebookId = $facebookId;
////            } else {
////                $items = NULL;
////            }
////        }
////        $stmt->close();
////        $conn->close();
////        return $items;
////    }
//
//    //cari data facebook $userId != NULL AND $username == NULL AND $facebookId != NULL
//    public function getWithUserIdFbId($userId, $facebookId)
//    {
//
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_ALL_WITH_FBID_USERID);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('ss', $userId, $facebookId);
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId, $username, $hash, $createdDate, $lastSeen, $regState, $isBlock, $nickName, $facebookId);
//            if ($stmt->fetch()) {
//                $items = new UserItem();
//                $items->userId = $userId;
//                $items->username = $username;
//                $items->hash = $hash;
//                $items->createdDate = $createdDate;
//                $items->lastSeen = $lastSeen;
//                $items->regState = $regState;
//                $items->isBlock = $isBlock;
//                $items->nickName = $nickName;
//                $items->facebookId = $facebookId;
//            } else {
//                $items = NULL;
//            }
//        }
//        $stmt->close();
//        $conn->close();
//        return $items;
//    }
//
//
//    // cari data untuk userid untuk validate userid yang tidak null di parameter
//    public function getUserId($username, $facebookId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_USER_ID);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('ss', $username, $facebookId);
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId);
//            if ($stmt->fetch()) {
//                $userId = $userId;
//            }
//        }
//        $stmt->close();
//        $conn->close();
//        return $userId;
//    }
//
//    // cari semua data berdasarkan nickname
//    public function getAllWithNickName($name)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_ALL_NICKNAME);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $name);
//        if ($stmt->execute()) {
//            $stmt->bind_result($name);
//            if ($stmt->fetch()) {
//                $name = $name;
//            }
//        }
//        $stmt->close();
//        $conn->close();
//        return $name;
//    }
//
//    // update data berdasarkan user ID
//    public function updateWithUserId($userId, $id, $email)
//    {
//        $regState = 2;
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATE_WITH_USERID);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('ssis', $id, $email, $regState, $userId);
//        if ($stmt->execute()) {
//            return true;
//        } else {
//            return false;
//        }
//        $stmt->close();
//        $conn->close();
//    }
//
//
//
//    //update user berdasarkan user id ketika fb terdaftar dengan no hp dan ingin daftar dengan email
////    public function updateUserWithFB($username,$facebookId,$userId){
////        $Koneksi = new Koneksi();
////        $conn = $Koneksi->connect();
////        $stmt = $conn->prepare(self::SQL_UPDATE_USER_WITH_FACEBOOKID);
////        if($stmt==false){
////            Log::writeErrorLn(self::TAG.".".__FUNCTION__." ". $conn->errno . " " . $conn->error);
////            return null;
////        }
////        $stmt->bind_param('sss',$username,$facebookId,$userId);
////        if($stmt->execute()){
////            return $userId;
////        }else{
////            return "Failed";
////        }
////        $stmt->close();
////    }
//
//
//    // update data berdasarkan email atau uername
//    public function updateUserWithEmail($username, $facebookId, $userId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATE_USER_WITH_EMAIL);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('sss', $facebookId, $username, $userId);
//        if ($stmt->execute()) {
//            return $userId;
//        } else {
//            return "Failed";
//        }
//        $stmt->close();
//        $conn->close();
//    }
//
////update paksa data state yang maih bernilai 1 atau 0
//
//    public function updateState($userId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATE_STATE);
//
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param("s", $userId);
//        if ($stmt->execute()) {
//            return TRUE;
//        } else {
//            return "Failed";
//        }
//        $stmt->close();
//        $conn->close();
//    }
//
//    public function updateFacebookId(string $userId, string $facebookId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATE_FACEBOOKID);
//
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return false;
//        }
//        $stmt->bind_param("ss", $facebookId, $userId);
//        if ($stmt->execute() == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return false;
//        }
//        $stmt->close();
//        $conn->close();
//
//        return true;
//    }
//
//    public function updateGoogleId(string $userId, string $googleId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATE_GOOGLEID);
//
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return false;
//        }
//        $stmt->bind_param("ss", $googleId, $userId);
//        if ($stmt->execute() == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return false;
//        }
//        $stmt->close();
//        $conn->close();
//
//        return true;
//    }
//
//    public function update(string $userId, string $username, $regState)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATE);
//
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return false;
//        }
//        $stmt->bind_param("sis", $username, $regState, $userId);
//        if ($stmt->execute() == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return false;
//        }
//        $stmt->close();
//        $conn->close();
//
//        return true;
//    }
//
////akhir dari do Facebook
//
//    //Do Register
//
//
//    public function setRegister($username, $hash, $userId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $regState = 1;
//        $stmt = $conn->prepare(self::SQL_SET_REGISTER);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('ssis', $username, $hash, $regState, $userId);
//
//        if ($stmt->execute()) {
//            $response['success'] = array('username' => $username, 'hash' => $hash, 'userId' => $userId);
//            return $response;
//        }
//        $stmt->close();
//        $conn->close();
//    }
//
//    public function updateUserRegister($userId, $mail, $hash, $regState)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATE_USER_REGISTER);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('ssis', $mail, $hash, $regState, $userId);
//        if ($stmt->execute()) {
//            return TRUE;
//        } else {
//            return FALSE;
//        }
//        $stmt->close();
//        $conn->close();
//
//    }
//
//    public function updateRegState(string $userId, int $regState): int
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATE_REG_STATE);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return 0;
//        }
//        $stmt->bind_param('is', $regState, $userId);
//        if ($stmt->execute()) {
//            $result = 1;
//        } else {
//            $result = 0;
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $result;
//    }
//
//    public function updatePassword(string $userId, string $hash): int
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATE_PASSWORD);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return 0;
//        }
//        $stmt->bind_param('ss', $hash, $userId);
//        if ($stmt->execute()) {
//            $result = 1;
//        } else {
//            $result = 0;
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $result;
//    }
//
//
//    //register by msisdn
//    public function getUserIdFromMsisdnVerified(string $msisdn)
//    {
//        require_once '../config/Koneksi.php';
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_USERID_FROM_MSISDN_VERIFIED);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $msisdn);
//
//        $result = null;
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId, $msisdnState);
//            if ($stmt->fetch()) {
//                $result = array('userId' => $userId, 'msisdnState' => $msisdnState);
//            }
//        }
//        $stmt->close();
//        $conn->close();
//        return $result;
//    }
//
//    public function getUserIdFromMsisdn(string $msisdn)
//    {
//        require_once '../config/Koneksi.php';
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_USERID_FROM_MSISDN);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $msisdn);
//
//        $result = null;
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId, $msisdnState);
//            if ($stmt->fetch()) {
//                $result = array('userId' => $userId, 'msisdnState' => $msisdnState);
//            }
//        }
//        $stmt->close();
//        $conn->close();
//        return $result;
//    }
//
//
//    public function updateMsisdn(string $userId, string $msisdn, string $password, string $regState): int
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATE_MSISDN);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return 0;
//        }
//        $stmt->bind_param('ssss', $msisdn, $password, $regState, $userId);
//        if ($stmt->execute()) {
//            $result = 1;
//        } else {
//            $result = 0;
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $result;
//    }
//
//    public function updateMsisdnState(string $userId)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_UPDATE_MSISDN_STATE);
//
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param("s", $userId);
//        if ($stmt->execute()) {
//            return TRUE;
//        } else {
//            return "Failed";
//        }
//        $stmt->close();
//        $conn->close();
//    }
//
//    //login by msisdn
//    public function validatesWitshPasswordMsisdn(string $phoneNumber, string $password)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_VALIDATES_WITH_PASSWORD_MSISDN);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('ss', $phoneNumber, $password);
//        $item = null;
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId, $regState, $isBlock);
//            if ($stmt->fetch()) {
//                $item = new UserItem();
//                $item->userId = $userId;
//                $item->regState = $regState;
//                $item->isBlock = $isBlock;
//                Log::writeLn(self::TAG . "." . __FUNCTION__ . " = sucess");
//            } else {
//                Log::writeLn(self::TAG . "." . __FUNCTION__ . " = fail | phone='{$phoneNumber}'");
//            }
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }
//
//
//    public function validatesMsisdn(string $phoneNumber)
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_VALIDATES_MSISDN);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $stmt->bind_param('s', $phoneNumber);
//        $item = null;
//        if ($stmt->execute()) {
//            $stmt->bind_result($userId, $regState, $isBlock);
//            if ($stmt->fetch()) {
//                $item = new UserItem();
//                $item->userId = $userId;
//                $item->regState = $regState;
//                $item->isBlock = $isBlock;
//                Log::writeLn(self::TAG . "." . __FUNCTION__ . " = sucess");
//            } else {
//                Log::writeLn(self::TAG . "." . __FUNCTION__ . " = fail | phone='{$phoneNumber}'");
//            }
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }
//
//    public function isMSISDNUser(string $userId): int
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET_BY_USERID);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return 0;
//        }
////        var_dump(self::SQL_GET_BY_USERID);
////        var_dump($userId);
//        $stmt->bind_param('s', $userId);
//        if ($stmt->execute()) {
//            $rquery = $stmt->get_result();
////            var_dump($rquery->num_rows);
//            while ($row = $rquery->fetch_assoc()) {
//                $username = $row['username'];
//                $facebookId = $row['facebookId'];
//                if ($username == null && $facebookId == null)
//                    $result = 1;
//                else
//                    $result = 0;
//            }
////            $result = 1;
//
//        } else {
//            $result = 0;
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $result;
//    }
//
//    public function deleteUser(string $userId): int
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_DELETE_USERID);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return 0;
//        }
//        $stmt->bind_param('s', $userId);
//
//        $stmt->bind_param("s", $userId);
//        if ($stmt->execute()) {
//            $result = 1;
//        } else
//            $result = 0;
//
//        $stmt->close();
//        $conn->close();
//
//        return $result;
//    }


}


