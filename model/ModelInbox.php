<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 8/9/2017
 * Time: 12:49 PM
 */


class InboxItem
{
    var $inboxId;
    var $userId;
    var $fromUserId;
    var $fromName;
    var $fromGender;
    var $fromUrlPP;
    var $createdDate;
    var $status;
    var $type;
    var $message;
    var $image;

}

class ModelInbox
{
    const TAG = "ModelInbox";
//    const SQL_CREATE = 'INSERT INTO tinbox (userId, fromUserId, type, message) VALUE (?, ?, ?, ?)';
    const SQL_GET = 'SELECT inboxId, userId, otherUserId, otherName, otherGender, otherUrlPP, type, message, createdDate, `status`,image FROM vinboxprofile WHERE userId=? AND inboxId>? LIMIT ?';
    const SQL_REMOVE_AFTER_LASTINBOXID = 'DELETE FROM tinbox WHERE userId=? AND inboxId<=?';
    const SQL_CREATE = 'CALL spChatPrivate(?, ?, ?, ?,?, @messageId);';
    const SQL_DELETE_STATUS="update tinbox set status=3 where (userId=?  and otherUserId=?) or (userId=? and otherUserId=?) and inboxId=?";
    const SQL_SEND_STATUS="update tinbox set status=3 where userId=? and inboxId=? and otherUserId=?";

    const SQL_GET_ALL = 'SELECT * FROM tinbox WHERE status=\'A\' AND (user_id=:user_id or user_id=0) ORDER BY update_date DESC LIMIT :_limit OFFSET :_offset';
    const SQL_GET_BY_ID = 'SELECT * FROM tinbox WHERE status=\'A\' and inbox_id=:_id';

    /**
     * @param string $userId , user tujuan message
     * @param string $fromUserId pengirim message
     * @param int $type = 1=text,2=image
     * @param string $message
     * @return array
     */
    static public function create(string $userId, string $fromUserId, int $type, string $message, string $image): int
    {
        /** require once dipindahkan ke setiap function, karena tidak bisa di akses dari function getById, yang dipanggil dari cms */
        require_once '../config/Koneksi.php';
        require_once '../library/DateUtil.php';
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $stmt = $conn->prepare(self::SQL_CREATE);
        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return 0;
        }

        $inboxId = 0;
        $stmt->bind_param('sssss', $userId, $fromUserId, $type, $message,$image);
//        $stmt->bind_param('ssss', $fromUserId, $userId, $type, $message);
        if ($stmt->execute()) {
            $stmt->bind_result($inboxId);
            $stmt->fetch();
        }
        $stmt->close();
        $conn->close();

        return $inboxId;
    }

    static public function get(string $userId, int $inboxId, int $limit): array
    {
        /** require once dipindahkan ke setiap function, karena tidak bisa di akses dari function getById, yang dipanggil dari cms */
        require_once '../config/Koneksi.php';
        require_once '../library/DateUtil.php';
        $list = array();
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $stmt = $conn->prepare(self::SQL_GET);
        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return $list;
        }

        $stmt->bind_param("sii", $userId, $inboxId, $limit);
        if ($stmt->execute()) {
            $stmt->bind_result($inboxId, $userId, $fromUserId, $fromName, $fromGender, $fromUrlPP, $type, $message, $createdDate, $status,$image);
            while ($stmt->fetch()) {
                $item = new InboxItem();
                $item->inboxId = $inboxId;
                $item->userId = $userId;
                $item->fromUserId = $fromUserId;
                $item->fromName = $fromName;
                $item->fromGender = $fromGender;
                $item->fromUrlPP = $fromUrlPP;
                $item->type = $type;
                $item->message = $message;
                $item->status = $status;
                $item->createdDate = DateUtil::formatDate($createdDate); //int
                $item->image = $image;
                array_push($list, $item);
            }
        } else {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return $list;
        }
        $stmt->close();
        $conn->close();

        return $list;
    }

    /** Hapus semua inbox dari user, sampai lastInboxId
     * @param string $userId
     * @param int $lastInboxId
     * @return int
     */
    static public function removeAfterLastInboxId(string $userId, int $lastInboxId): int
    {
        /** require once dipindahkan ke setiap function, karena tidak bisa di akses dari function getById, yang dipanggil dari cms */
        require_once '../config/Koneksi.php';
        require_once '../library/DateUtil.php';
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $stmt = $conn->prepare(self::SQL_REMOVE_AFTER_LASTINBOXID);
        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return 0;
        }

        $result = 0;
        $stmt->bind_param("si", $userId, $lastInboxId);
        if ($stmt->execute()) {
            $result = 1;
        } else {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return 0;
        }
        $stmt->close();
        $conn->close();

        return $result;
    }

    static public function deleteStatus(string $userId, int $inboxId, string $otherUserId): int
    {
        /** require once dipindahkan ke setiap function, karena tidak bisa di akses dari function getById, yang dipanggil dari cms */

        require_once '../config/Koneksi.php';
        require_once '../library/DateUtil.php';
        $Koneksi = new Koneksi();
        $conn = $Koneksi->connect();
        $stmt = $conn->prepare(self::SQL_DELETE_STATUS);
        if ($stmt == false) {
            Log::writeErrorLn(self::TAG . "." . __FUNCTION__ . " " . $conn->errno . " " . $conn->error);
            return 0;
        }

        $result = 0;
        $stmt->bind_param('sssss', $userId, $otherUserId,$otherUserId,$userId, $inboxId);
        if ($stmt->execute()) {
            $result=1;
        }
        $stmt->close();
        $conn->close();

        return $result;
    }

    /**
     * Ambil semua record yg berstatus A
     *
     * @param int $offset
     * @param int $limit
     * @return array|Exception|PDOException
     */
    static public function getAll(int $userId, int $offset, int $limit){
        /** require once dipindahkan ke setiap function, karena tidak bisa di akses dari function getById, yang dipanggil dari cms */
//        require_once '../config/Koneksi.php';
//        require_once '../library/DateUtil.php';
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelInbox::SQL_GET_ALL);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':_limit', $limit, PDO::PARAM_INT);

            $stmt->execute( );

            $rows = $stmt->fetchAll();
            return $rows;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }


    static public function getById(int $inboxId){
        try{
            $pdo = Koneksi::open();
            $stmt = $pdo->prepare(ModelInbox::SQL_GET_BY_ID);
            $stmt->bindValue(':_id', $inboxId, PDO::PARAM_INT);

            $stmt->execute( );

            $rows = $stmt->fetchAll();
            return $rows;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

}