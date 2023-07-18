<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/9/2023
 * Time: 1:59 PM
 */

namespace App\Models;

class AdminModel extends BaseModel
{
    const SQL_GET_ALL = 'SELECT admin_id, username, trole.role_name, tadmin.create_date, tadmin.update_date FROM tadmin INNER JOIN trole ON tadmin.role_id = trole.role_id';
    const SQL_MODIFY = 'UPDATE tadmin SET username=?, role_id=? WHERE admin_id=?';
//    const SQL_MODIFY_PASSWORD = 'UPDATE tadmin SET hash_password=? WHERE admin_id=?';

    protected $table      = 'tadmin';
    protected $primaryKey = 'admin_id';
    protected $allowedFields = ['role_id', 'username', 'hash_password'];

    public function getFieldList(){
        return ['admin_id', 'Username', 'Role', 'Create', 'Update'];
    }

    /**
     * get 1 user
     *
     * @param $username
     * @return array|null|object
     */
    public function get($username){
        $r = $this->select('tadmin.*, trole.role_name')
            ->join('trole', 'tadmin.role_id = trole.role_id')
            ->where('username', $username)
            ->find();

        if ($r != null) {
            return $r[0];
        }

        return null;
    }

    public function getById($adminId){
        return $this->find($adminId);
    }

    public function getAll(){
        $db = db_connect();
        return $db->query(self::SQL_GET_ALL)->getResult('array');
    }

    public function add($value)  {
        //xss
        $value['username'] = htmlentities($value['username'], ENT_QUOTES, 'UTF-8');

        $this->insert($value);
        return $this->getInsertID();
    }

    public function modify($id, $data){
        $this->errCode = '';
        $this->errMessage = '';

        $role_id = $data['role_id'];
        $username = $data['username'];

        //xss
        $username = htmlentities($data['username'], ENT_QUOTES, 'UTF-8');//$data['message'];

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$username, $role_id, $id] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }

    public function modifyPassword($id, $newPassword){
        return $this->update($id, ['hash_password'=>$newPassword]);
    }

    public function remove($id){
        return $this->delete($id);
    }
}