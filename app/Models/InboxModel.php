<?php
/**
 * Created by PageBuilder
 * Date: 2023-03-06 10:56:19
 */
namespace App\Models;

use App\Libraries\SSP;

class InboxModel extends BaseModel
{
    const VIEW = 'vinbox_user';

    const SQL_GET_ALL = 'SELECT tinbox.inbox_id,
                        tinbox.user_id,
                        tinbox.title,
                        tinbox.content,
                        tinbox.url_image,
                        tinbox.path_image,
                        tinbox.status,
                        tinbox.exp_date,
                        tinbox.create_date,
                        tinbox.update_date,
                        tprofile.name,
                        tuser.username,
                        tuser.email,
                        tuser.msisdn 
                        FROM ((tinbox LEFT JOIN tuser ON (tuser.user_id = tinbox.user_id)) 
                        LEFT JOIN tprofile ON (tinbox.user_id = tprofile.user_id))
                        ORDER BY tinbox.inbox_id DESC
                        ';

    const SQL_GET = 'SELECT * FROM tinbox WHERE (inbox_id=?)';
    const SQL_MODIFY = 'UPDATE tinbox SET user_id=?, title=?, content=?, url_image=?, status=?, exp_date=? WHERE (inbox_id=?)';

    protected $table      = 'tinbox';
    protected $primaryKey = 'inbox_id';
    // protected $allowedFields = ['user_id', 'title', 'content', 'url_image', 'path_image', 'status', 'exp_date', 'create_date', 'update_date'];
    protected $allowedFields = ['user_id', 'title', 'content', 'url_image', 'status', 'exp_date'];

    public $errCode;
    public $errMessage;

//    public function get2($adminId, $username){
//        $r = $this
//            ->where('admin_id', $adminId)
//            ->where('username', $username)
//            ->find();
//        if ($r!=null) return $r[0];
//        return null;
//    }

    public function get($inboxId)
    {
        // $r = $this
        //     ->where('inbox_id', $inboxId)
        //     ->find();
        // if ($r!=null) return $r[0];

        // return null;
        return $this->find($inboxId);
    }

    public function getAll(){
        // return $this->findAll();
        $db = db_connect();
        return $db->query(self::SQL_GET_ALL)->getResult();
    }

    public function getFieldList(){
        return ['inbox_id', 'user_id', 'title', 'status', 'exp_date', 'name', 'username', 'email', 'msisdn'];
//        return ['inbox_id', 'user_id', 'title', 'content', 'url_image', 'status', 'exp_date', 'create_date', 'update_date', 'name', 'username', 'email', 'msisdn'];
    }

    public function add($value)  {

        try
        {
            $value['title'] = htmlentities($value['title'], ENT_QUOTES, 'UTF-8');
            $value['content'] = htmlentities($value['content'], ENT_QUOTES, 'UTF-8');
            $value['url_image'] = htmlentities($value['url_image'], ENT_QUOTES, 'UTF-8');
            // $value['path_image'] = htmlentities($value['path_image'], ENT_QUOTES, 'UTF-8');
            $value['status'] = htmlentities($value['status'], ENT_QUOTES, 'UTF-8');

            parent::insert($value);

        }
        catch (\Exception $e){
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();

            return 0;
        }

        return $this->db->affectedRows();
    }

    /**
     * update dgn cara PDO, karena dgn cara ci4 tdk ada rowCount, shg tdk tahu apakah update berhasil atau tdk
     *
     * @param $id
     * @param $name
     * @param $status
     * @return \PDOException|\Exception|int => 0/1 = count update, -1 = pdo exception
     */
    public function modify($value){

        $this->errCode = '';
        $this->errMessage = '';

        $inboxId = $value['inbox_id'];

        $userId = $value['user_id'];
        $title = htmlentities($value['title'], ENT_QUOTES, 'UTF-8');
        $content = htmlentities($value['content'], ENT_QUOTES, 'UTF-8');
        $urlImage = htmlentities($value['url_image'], ENT_QUOTES, 'UTF-8');
        // $pathImage = htmlentities($value['path_image'], ENT_QUOTES, 'UTF-8');
        $status = htmlentities($value['status'], ENT_QUOTES, 'UTF-8');
        $expDate = $value['exp_date'];
        // $createDate = $value['create_date'];
        // $updateDate = $value['update_date'];

        if (strlen($expDate)==0) $expDate = null;
        // if (strlen($createDate)==0) $createDate = null;
        // if (strlen($updateDate)==0) $updateDate = null;

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            // $stmt->execute( [$userId, $title, $content, $urlImage, $pathImage, $status, $expDate, $createDate, $updateDate, $inboxId] );
            $stmt->execute( [$userId, $title, $content, $urlImage, $status, $expDate, $inboxId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($inboxId){
        $r = $this
            ->where('inbox_id', $inboxId)
            ->delete();

        return $this->db->affectedRows();
    }

    /**
     * di pakai utk datatables.js
     *
     * @return mixed
     */
    public function getSsp()
    {
        // return $this->_getSsp($this->table, $this->primaryKey, $this->getFieldList());
        return $this->_getSsp(self::VIEW, $this->primaryKey, $this->getFieldList());
    }
}
