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
    const SQL_GET_ALL = 'SELECT admin_id, username, json AS roles, create_date, update_date FROM tadmin';
    const SQL_MODIFY = 'UPDATE tadmin SET username=? WHERE admin_id=?';
    // const SQL_MODIFY_PASSWORD = 'UPDATE tadmin SET hash_password=? WHERE admin_id=?';

    const SQL_MODIFY_JSON = 'UPDATE tadmin SET json=? WHERE admin_id=?';
    const SQL_GET_JSON = 'SELECT json AS id, json AS value FROM tadmin';
    
    // only for reference
    const SQL_GET_ROLES_FOR_SELECT = 'SELECT role_name AS id, role_name AS value FROM trole';

    protected $table      = 'tadmin';
    protected $primaryKey = 'admin_id';
    protected $allowedFields = ['role_id', 'username', 'json', 'hash_password'];

    public function getFieldList(){
        return ['admin_id', 'Username', 'roles', 'Create', 'Update'];
    }

    /**
     * get 1 user
     *
     * @param $username
     * @return array|null|object
     */
    public function get($username){
        $r = $this->select('tadmin.*')
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

    public function getRolesForSelect(){
        $result = $this->db->query(self::SQL_GET_ROLES_FOR_SELECT)->getResult('array');
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }

    public function getJson(){
        $result = $this->db->query(self::SQL_GET_JSON)->getResult('array');
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }

    public function add($value)  {
        //xss
        $value['username'] = htmlentities($value['username'], ENT_QUOTES, 'UTF-8');

        // Make sure $json is a JSON string
        if (is_array($value['json'])) {
            $value['json'] = json_encode($value['json']);
        }

        $this->insert($value);
        return $this->getInsertID();
    }

    public function modify($id, $data){
        $this->errCode = '';
        $this->errMessage = '';

        // $role_id = $data['role_id'];
        $username = $data['username'];

        //xss
        $username = htmlentities($data['username'], ENT_QUOTES, 'UTF-8');//$data['message'];

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$username, $id] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }

    // update roles on field json
    public function modifyJson($id, $json){
        $this->errCode = '';
        $this->errMessage = '';
    
        try {
            // Make sure $json is a JSON string
            if (is_array($json)) {
                $json = json_encode($json);
            }
    
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY_JSON);
            $stmt->execute([$json, $id]);
    
            return $stmt->rowCount();
        } catch (\PDOException $e) {
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