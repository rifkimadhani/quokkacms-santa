<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-14 09:28:36
 */
namespace App\Models;

class UserModel extends BaseModel
{
    const SQL_GET = 'SELECT * FROM tuser WHERE (user_id=?)';
    const SQL_MODIFY = 'UPDATE tuser SET username=?, hash=?, salt=?, email=?, email_state=?, email_code_count=?, email_code=?, email_code_exp=?, email_code_resend=?, msisdn=?, msisdn_state=?, msisdn_code_count=?, msisdn_code=?, msisdn_code_exp=?, msisdn_code_resend=?, facebookId=?, googleId=?, instagram_user_id=?, instagram_id=?, is_block=?, create_date=?, update_date=? WHERE (user_id=?)';

    protected $view = 'vuser_profile';
    protected $table = 'tuser';
    protected $primaryKey = 'user_id';
    protected $allowedFields = ['username', 'hash', 'salt', 'email', 'email_state', 'email_code_count', 'email_code', 'email_code_exp', 'email_code_resend', 'msisdn', 'msisdn_state', 'msisdn_code_count', 'msisdn_code', 'msisdn_code_exp', 'msisdn_code_resend', 'facebookId', 'googleId', 'instagram_user_id', 'instagram_id', 'is_block', 'create_date', 'update_date'];

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

    public function get($userId)
    {
        $r = $this
            ->where('user_id', $userId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['user_id', 'name', 'gender', 'username', 'email', 'email_state', 'msisdn', 'msisdn_state', 'facebookId', 'googleId', 'is_block'];
//        return ['user_id', 'username', 'hash', 'salt', 'email', 'email_state', 'email_code_count', 'email_code', 'email_code_exp', 'email_code_resend', 'msisdn', 'msisdn_state', 'msisdn_code_count', 'msisdn_code', 'msisdn_code_exp', 'msisdn_code_resend', 'facebookId', 'googleId', 'instagram_user_id', 'instagram_id', 'is_block', 'create_date', 'update_date'];
    }

    public function add($value)  {

        try
        {
            $value['username'] = htmlentities($value['username'], ENT_QUOTES, 'UTF-8');
            $value['hash'] = htmlentities($value['hash'], ENT_QUOTES, 'UTF-8');
            $value['salt'] = htmlentities($value['salt'], ENT_QUOTES, 'UTF-8');
            $value['email'] = htmlentities($value['email'], ENT_QUOTES, 'UTF-8');
            $value['email_code'] = htmlentities($value['email_code'], ENT_QUOTES, 'UTF-8');
            $value['msisdn'] = htmlentities($value['msisdn'], ENT_QUOTES, 'UTF-8');
            $value['msisdn_code'] = htmlentities($value['msisdn_code'], ENT_QUOTES, 'UTF-8');
            $value['facebookId'] = htmlentities($value['facebookId'], ENT_QUOTES, 'UTF-8');
            $value['googleId'] = htmlentities($value['googleId'], ENT_QUOTES, 'UTF-8');
            $value['instagram_user_id'] = htmlentities($value['instagram_user_id'], ENT_QUOTES, 'UTF-8');
            $value['instagram_id'] = htmlentities($value['instagram_id'], ENT_QUOTES, 'UTF-8');

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

        $userId = $value['user_id'];

        $username = htmlentities($value['username'], ENT_QUOTES, 'UTF-8');
        $hash = htmlentities($value['hash'], ENT_QUOTES, 'UTF-8');
        $salt = htmlentities($value['salt'], ENT_QUOTES, 'UTF-8');
        $email = htmlentities($value['email'], ENT_QUOTES, 'UTF-8');
        $emailState = $value['email_state'];
        $emailCodeCount = $value['email_code_count'];
        $emailCode = htmlentities($value['email_code'], ENT_QUOTES, 'UTF-8');
        $emailCodeExp = $value['email_code_exp'];
        $emailCodeResend = $value['email_code_resend'];
        $msisdn = htmlentities($value['msisdn'], ENT_QUOTES, 'UTF-8');
        $msisdnState = $value['msisdn_state'];
        $msisdnCodeCount = $value['msisdn_code_count'];
        $msisdnCode = htmlentities($value['msisdn_code'], ENT_QUOTES, 'UTF-8');
        $msisdnCodeExp = $value['msisdn_code_exp'];
        $msisdnCodeResend = $value['msisdn_code_resend'];
        $facebookId = htmlentities($value['facebookId'], ENT_QUOTES, 'UTF-8');
        $googleId = htmlentities($value['googleId'], ENT_QUOTES, 'UTF-8');
        $instagramUserId = htmlentities($value['instagram_user_id'], ENT_QUOTES, 'UTF-8');
        $instagramId = htmlentities($value['instagram_id'], ENT_QUOTES, 'UTF-8');
        $isBlock = $value['is_block'];
        $createDate = $value['create_date'];
        $updateDate = $value['update_date'];

        if (strlen($emailCodeExp)==0) $emailCodeExp = null;
        if (strlen($emailCodeResend)==0) $emailCodeResend = null;
        if (strlen($msisdnCodeExp)==0) $msisdnCodeExp = null;
        if (strlen($msisdnCodeResend)==0) $msisdnCodeResend = null;
        if (strlen($createDate)==0) $createDate = null;
        if (strlen($updateDate)==0) $updateDate = null;

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$username, $hash, $salt, $email, $emailState, $emailCodeCount, $emailCode, $emailCodeExp, $emailCodeResend, $msisdn, $msisdnState, $msisdnCodeCount, $msisdnCode, $msisdnCodeExp, $msisdnCodeResend, $facebookId, $googleId, $instagramUserId, $instagramId, $isBlock, $createDate, $updateDate, $userId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($userId){
        $r = $this
            ->where('user_id', $userId)
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
        return $this->_getSsp($this->view, $this->primaryKey, $this->getFieldList());
    }
}
