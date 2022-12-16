<?php

/**
 * Created by PhpStorm.
 * User: echri
 * Date: 26/10/2022
 * Time: 13:45
 */
require_once __DIR__ . '/../../config/Koneksi.php';

///////////////////////////////////////////////////////
/// class ini hanya di pakai utk old design ModelProfile
///
class ProfileItem
{
    var $userId;
    var $name;
    var $birthdate;
    var $gender;
    var $aboutMe;
    var $hobby;
    var $location;
    var $from;
//    var $urlPhotoprofile;
    var $urlPP;
    var $countViewer;
    var $education;
    var $ageInYears;
    var $countGallery;
    var $canAdd;
    var $sumOfFriends;

}

/**
 * Class ModelProfile
 * class ini gabungan dari 2 implementasi
 * 1. class dgn teknik baru
 * 2. class teknik lama, yg asli dari project metra/homeconnect, teknik lama ini perlahan2 di hapus dan di replace dgn yg baru
 */
class ModelProfile
{
    const SQL_CREATE_SIMPLE = "INSERT INTO tprofile (user_id) VALUES (?)";
    const SQL_GET = "SELECT * FROM tprofile WHERE user_id=?";

    static function create(int $userId) {

        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_CREATE_SIMPLE);
            $stmt->execute( [$userId] );

            return $stmt->rowCount();

        }catch (PDOException $e){
            if ($e->getCode()=='23000') return 1;
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static function get(int $userId){
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


    static function update(int $userId, $data){
        require_once __DIR__ . '/../../library/pdo.php';

        try{
            $pdo = Koneksi::create();

            $r = PdoUtil::update($pdo, 'tprofile', ['user_id'=>$userId], $data);

            return $r;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    //////////////////////////////////////////////////////////////////////////
    //di bawah ini berisikan code dari project metra/homeconnect
    //

    const TAG = 'ModelProfile';

//    const SQL_CREATE = "INSERT INTO tprofile (userId, `name`, birthdate, gender, aboutMe, hobby, location, `from`, urlPP, education) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
//    const SQL_UPDATE = "UPDATE tprofile SET updateDate=now(), `name`=?, birthdate=?, gender=?, aboutMe=?, hobby=?, location=?, `from`=?, education=? WHERE userId=?";
    const SQL_UPDATE_URLPP = "UPDATE tprofile SET urlPP=? WHERE userId=?";
//    const SQL_GET = "SELECT `name`, birthdate, gender, aboutMe, hobby, location, `from`, urlPP, countViewer, education FROM tprofile WHERE user_id=?";
    const SQL_UPDATE_REGISTER = "UPDATE tprofile SET name = ? WHERE userId = ?";
    const SQL_UPDATE_PHOTO = "UPDATE tprofile SET urlPP = ? WHERE userId = ?";
    const SQL_GET_PHOTO = "SELECT urlPP FROM tprofile WHERE userId = ?";
    const SQL_UPDATE_COUNTVIEWER = "UPDATE tprofile SET countViewer=countViewer+1 WHERE userId=?";
    const SQL_SEARCHBYEMAIL = "SELECT a.userId, a.`name`, a.urlPP, IFNULL(truncate(DATEDIFF(CURRENT_DATE, STR_TO_DATE(a.birthdate, '%Y-%m-%d'))/365,0),0) AS ageInYears,
                              a.location,a.from, (case when a.urlPP is null then count(b.imageId) else count(b.imageId)+1 end) as countGallery,
                              (case when c.userId is null and d.userId is null THEN 1 ELSE 0 END) AS canAdd,
                              a.aboutMe,count(e.userId) as sumOfFriends
                              FROM vprofile a 
                              left join tusergallery b on a.userId = b.userId
                              left join tfriend c on c.userId = a.userId and c.friendId=?
                              left join tfriend_request d on d.userId =? and d.friendId=a.userId  and (d.status='PENDING' or d.status='CONFIRM')
                              left join tfriend e on e.userId =a.userid
                              group by a.userId, b.userId WHERE a.username=? and a.userId<>? ORDER BY a.`name` LIMIT ? OFFSET ?";
    const SQL_SEARCHBYNAME = "SELECT a.userId, a.`name`, a.urlPP, IFNULL(truncate(DATEDIFF(CURRENT_DATE, STR_TO_DATE(a.birthdate, '%Y-%m-%d'))/365,0),0) AS ageInYears,
                              a.location,a.from, (case when a.urlPP is null then count(b.imageId) else count(b.imageId)+1 end) as countGallery,
                              (case when c.userId is null and d.userId is null THEN 1 ELSE 0 END) AS canAdd,
                              a.aboutMe,count(e.userId) as sumOfFriends
                              FROM vprofile a 
                              left join tusergallery b on a.userId = b.userId
                              left join tfriend c on c.userId = a.userId and c.friendId=?
                              left join tfriend_request d on d.userId =? and d.friendId=a.userId  and (d.status='PENDING' or d.status='CONFIRM')
                              left join tfriend e on e.userId =a.userid
                              [EXTRA_CRITERIA] group by a.userId, b.userId ORDER BY a.`name` LIMIT ? OFFSET ?";

    const SQL_SEARCHBYEMAIL_NOLIMIT = "SELECT a.userId, a.`name`, a.urlPP, IFNULL(truncate(DATEDIFF(CURRENT_DATE, STR_TO_DATE(a.birthdate, '%Y-%m-%d'))/365,0),0) AS ageInYears,
                              a.location,a.from, (case when a.urlPP is null then count(b.imageId) else count(b.imageId)+1 end) as countGallery,
                              (case when c.userId is null and d.userId is null THEN 1 ELSE 0 END) AS canAdd,
                              a.aboutMe,count(e.userId) as sumOfFriends
                              FROM vprofile a 
                              left join tusergallery b on a.userId = b.userId
                              left join tfriend c on c.userId = a.userId and c.friendId=?
                              left join tfriend_request d on d.userId =? and d.friendId=a.userId  and (d.status='PENDING' or d.status='CONFIRM')
                              left join tfriend e on e.userId =a.userid
                              group by a.userId, b.userId WHERE a.username=? and a.userId<>? ORDER BY a.`name`";
    const SQL_SEARCHBYNAME_NOLIMIT = "SELECT a.userId, a.`name`, a.urlPP, IFNULL(truncate(DATEDIFF(CURRENT_DATE, STR_TO_DATE(a.birthdate, '%Y-%m-%d'))/365,0),0) AS ageInYears,
                              a.location,a.from, (case when a.urlPP is null then count(b.imageId) else count(b.imageId)+1 end) as countGallery,
                              (case when c.userId is null and d.userId is null THEN 1 ELSE 0 END) AS canAdd,
                              a.aboutMe,count(e.userId) as sumOfFriends
                              FROM vprofile a 
                              left join tusergallery b on a.userId = b.userId
                              left join tfriend c on c.userId = a.userId and c.friendId=?
                              left join tfriend_request d on d.userId =? and d.friendId=a.userId  and (d.status='PENDING' or d.status='CONFIRM')
                              left join tfriend e on e.userId =a.userid
                              [EXTRA_CRITERIA] group by a.userId, b.userId ORDER BY a.`name`";


    function __construct()
    {
        require_once '../config/Koneksi.php';
    }

//    public function update(string $userId, $name, $birthdate, $gender, $aboutMe, $hobby, $location, $from, $education, $urlPP = NULL): bool
//    {
//
//        $r = $this->updateA($userId, $name, $birthdate, $gender, $aboutMe, $hobby, $location, $from, $education);
//        if ($r) return true;
//
//        $r = $this->create($userId, $name, $birthdate, $gender, $aboutMe, $hobby, $location, $from, $urlPP, $education);
//        if ($r) return true;
//
//        return false;
//    }
//
//    public function create($userId, $name, $birthdate, $gender, $aboutMe, $hobby, $location, $from, $urlPP, $education): bool
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_CREATE);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//
//        $r = false;
//        $stmt->bind_param("ssssssssss", $userId, $name, $birthdate, $gender, $aboutMe, $hobby, $location, $from, $urlPP, $education);
//        if ($stmt->execute()) {
//            $r = true;
//        } else {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $r;
//    }

    /** This function is called from update
     * @param $userId
     * @param $name
     * @param $birthdate
     * @param $gender
     * @return bool
     */
    public function updateA($userId, $name, $birthdate, $gender, $aboutMe, $hobby, $location, $from, $education): bool
    {
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $stmt = $conn->prepare(self::SQL_UPDATE);
        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return null;
        }

        $r = false;
        $stmt->bind_param("sssssssss", $name, $birthdate, $gender, $aboutMe, $hobby, $location, $from, $education, $userId);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) $r = true;
        } else {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
        }
        $stmt->close();
        $conn->close();

        return $r;
    }

    public function updateUrlPP($userId, $urlPP): bool
    {
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $stmt = $conn->prepare(self::SQL_UPDATE_URLPP);
        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return null;
        }

        $r = false;
        $stmt->bind_param("ss", $urlPP, $userId);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) $r = true;
        } else {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
        }
        $stmt->close();
        $conn->close();

        return $r;
    }

    public function updateCountViewer($userId): int
    {
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $stmt = $conn->prepare(self::SQL_UPDATE_COUNTVIEWER);
        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return 0;
        }

        $r = 0;
        $stmt->bind_param("s", $userId);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) $r = 1;
        } else {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
        }
        $stmt->close();
        $conn->close();

        return $r;
    }


//    public function get($userId): ?ProfileItem
//    {
//        $Koneksi = new Koneksi();
//        $conn = $Koneksi->connect();
//        $stmt = $conn->prepare(self::SQL_GET);
//        if ($stmt == false) {
//            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
//            return null;
//        }
//        $item = null;
//        $stmt->bind_param("s", $userId);
//        if ($stmt->execute()) {
//            $stmt->bind_result($name, $birthdate, $gender, $aboutMe, $hobby, $location, $from, $urlPP, $countViewer, $education);
//            if ($stmt->fetch()) {
//                $item = new ProfileItem();
//                $item->userId = $userId;
//                $item->name = $name;
//                $item->birthdate = $birthdate;
//                $item->gender = $gender;
//                $item->aboutMe = $aboutMe;
//                $item->hobby = $hobby;
//                $item->location = $location;
//                $item->from = $from;
//                $item->urlPP = $urlPP;
//                $item->countViewer = $countViewer;
//                $item->education = $education;
//            }
//        }
//        $stmt->close();
//        $conn->close();
//
//        return $item;
//    }

    public function registerasi($userId, $name)
    {
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $stmt = $conn->prepare(self::SQL_UPDATE_REGISTER);
        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return null;
        }
        $stmt->bind_param("ss", $name, $userId);
        if ($stmt->execute()) {
            return "Success";
        } else {
            return "Failed";
        }
        $stmt->close();
        $conn->close();
    }

    public function updatePhotoProfile($userId, $file_path_db)
    {
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $stmt = $conn->prepare(self::SQL_UPDATE_PHOTO);
        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return null;
        }
        $stmt->bind_param("ss", $file_path_db, $userId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
        $stmt->close();
        $conn->close();
    }

    public function getUrlImage($userId)
    {
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $stmt = $conn->prepare(self::SQL_GET_PHOTO);
        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return null;
        }
        $stmt->bind_param("s", $userId);
        if ($stmt->execute()) {
            $stmt->bind_result($urlPhotoprofile);
            if ($stmt->fetch()) {
                $item = new ProfileItem();
                $item->urlPP = $urlPhotoprofile;
            }
        }

        $stmt->close();
        $conn->close();
        return $item;
    }

    /**
     * @param $name bisa nama user atau email dari user. apabila email maka semua criteria akan di abaikan
     * @param $gender
     * @param int $ageFrom
     * @param int $ageTo
     * @param $hobby
     * @param $from
     * @param int $offset
     * @param int $limit
     * @return array|null
     */
    public function searchLite($userId, $name, $gender, int $ageFrom, int $ageTo, $hobby, $from, int $offset, int $limit)
    {
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $isSearchByEmail = false;

        if (strpos($name, '@')) {
            $isSearchByEmail = true;
            $sql = self::SQL_SEARCHBYEMAIL;
        } else {
            $sql = self::SQL_SEARCHBYNAME;
        }

        if ($isSearchByEmail == false) { //criteria filter
            $extraCriteria = '';
            $arCritetia = array();
            if ($name != null) {
                $name = str_replace("'", "''", $name); //replace ' --> '' to prevent sql injection
                array_push($arCritetia, "a.name LIKE '%$name%'");
            }
            if ($gender != null) {
                $gender = str_replace("'", "''", $gender); //replace ' --> '' to prevent sql injection
                array_push($arCritetia, "a.gender='$gender'");
            }
            if ($ageFrom > 0 && $ageTo > 0) {
                array_push($arCritetia, "(age>=$ageFrom AND age<=$ageTo)");
            }
            if ($hobby != null) {
                $hobby = str_replace("'", "''", $hobby); //replace ' --> '' to prevent sql injection
                array_push($arCritetia, "a.hobby LIKE '%$hobby%'");
            }
            if ($from != null) {
                $from = str_replace("'", "''", $from); //replace ' --> '' to prevent sql injection
                array_push($arCritetia, "a.`from`='$from'");
            }

            //join semua criteria jadi 1 string, dan di tambahkan AND
            $idx = 0;
            while (count($arCritetia) > $idx) {
                $criteria = $arCritetia[$idx];
                if (strlen($extraCriteria) == 0) $extraCriteria = $criteria; else $extraCriteria = $extraCriteria . ' AND ' . $criteria;
                $idx++;
            }

            //tambahkan keyword WHERE apabila criteria tdk kosong
            if (strlen($extraCriteria) > 0) $extraCriteria = 'WHERE ' . $extraCriteria . " and a.userId<>'$userId'";

//		    echo $extraCriteria . '<BR>';
            $sql = str_replace('[EXTRA_CRITERIA]', $extraCriteria, $sql);
//		    echo $sql . '<BR>';
        }

        $stmt = $conn->prepare($sql);

        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return null;
        }

        if ($isSearchByEmail) {
            $stmt->bind_param("ssssii", $userId, $userId, $name, $userId, $limit, $offset);
        } else {
            $stmt->bind_param("ssii", $userId, $userId, $limit, $offset);
        }

        $list = array();
        if ($stmt->execute()) {

            $stmt->bind_result($userId, $name, $urlPP, $ageInYears, $location, $from, $countGallery, $canAdd, $aboutMe, $sumOfFriends);
            while ($stmt->fetch()) {
                $item = new ProfileItem();
                $item->userId = $userId;
                $item->name = $name;
                $item->urlPP = $urlPP;
                $item->ageInYears = $ageInYears;
                $item->location = $location;
                $item->from = $from;
                $item->countGallery = $countGallery;
                $item->canAdd = $canAdd;
                $item->aboutMe = $aboutMe;
                $item->sumOfFriends = $sumOfFriends;
                array_push($list, $item);
            }
        }

        $stmt->close();
        $conn->close();
        return $list;
    }

    public function searchLiteNoLimit($userId, $name, $gender, int $ageFrom, int $ageTo, $hobby, $from, $withPhoto)
    {
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $isSearchByEmail = false;

        if (strpos($name, '@')) {
            $isSearchByEmail = true;
            $sql = self::SQL_SEARCHBYEMAIL_NOLIMIT;
        } else {
            $sql = self::SQL_SEARCHBYNAME_NOLIMIT;
        }

        if ($isSearchByEmail == false) { //criteria filter
            $extraCriteria = '';
            $arCritetia = array();
            if ($name != null) {
                $name = str_replace("'", "''", $name); //replace ' --> '' to prevent sql injection
                array_push($arCritetia, "a.name LIKE '%$name%'");
            }
            if ($gender != null) {
                $gender = str_replace("'", "''", $gender); //replace ' --> '' to prevent sql injection
                array_push($arCritetia, "a.gender='$gender'");
            }
            if ($ageFrom > 0 && $ageTo > 0) {
                array_push($arCritetia, "(age>=$ageFrom AND age<=$ageTo)");
            }
            if ($hobby != null) {
                $hobby = str_replace("'", "''", $hobby); //replace ' --> '' to prevent sql injection
                array_push($arCritetia, "a.hobby LIKE '%$hobby%'");
            }
            if ($from != null) {
                $from = str_replace("'", "''", $from); //replace ' --> '' to prevent sql injection
                array_push($arCritetia, "a.`from`='$from'");
            }
            if ($withPhoto != null) {
                $withPhoto = str_replace("'", "''", $withPhoto); //replace ' --> '' to prevent sql injection
                if ($withPhoto == "1")
                    array_push($arCritetia, "a.urlPP is not null");
            }

            //join semua criteria jadi 1 string, dan di tambahkan AND
            $idx = 0;
            while (count($arCritetia) > $idx) {
                $criteria = $arCritetia[$idx];
                if (strlen($extraCriteria) == 0) $extraCriteria = $criteria; else $extraCriteria = $extraCriteria . ' AND ' . $criteria;
                $idx++;
            }

            //tambahkan keyword WHERE apabila criteria tdk kosong
            if (strlen($extraCriteria) > 0) $extraCriteria = 'WHERE ' . $extraCriteria . " and a.userId<>'$userId'";

//		    echo $extraCriteria . '<BR>';
            $sql = str_replace('[EXTRA_CRITERIA]', $extraCriteria, $sql);
//		    echo $sql . '<BR>';
        }

        $stmt = $conn->prepare($sql);

        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return null;
        }

        if ($isSearchByEmail) {
            $stmt->bind_param("ssssii", $userId, $userId, $name, $userId, $limit, $offset);
        } else {
            $stmt->bind_param("ss", $userId, $userId);
        }

        $list = array();
        if ($stmt->execute()) {

            $stmt->bind_result($userId, $name, $urlPP, $ageInYears, $location, $from, $countGallery, $canAdd, $aboutMe, $sumOfFriends);
            while ($stmt->fetch()) {
                $item = new ProfileItem();
                $item->userId = $userId;
                $item->name = $name;
                $item->urlPP = $urlPP;
                $item->ageInYears = $ageInYears;
                $item->location = $location;
                $item->from = $from;
                $item->countGallery = $countGallery;
                $item->canAdd = $canAdd;
                $item->aboutMe = $aboutMe;
                $item->sumOfFriends = $sumOfFriends;
                array_push($list, $item);
            }
        }

        $stmt->close();
        $conn->close();
        return $list;
    }


}