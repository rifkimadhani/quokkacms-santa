<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/9/2023
 * Time: 11:15 AM
 */

namespace App\Controllers;

use App\Models\AdminModel;

class Login extends BaseController
{

    public function index()
    {
        return view('login/index');
    }

    public function login()
    {
        require_once __DIR__ . '/../../library/Security.php';

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $username = $_POST['username'];
            $password = $_POST['password'];

            //ambil user dari db
            $model = new AdminModel();
            $user = $model->get($username);

            //user tdk ketemu
            if ($user==null){
                $this->setErrorMessage('Username/password not valid');
                return redirect()->to('/login');
            }

            $adminId = $user['admin_id'];
            $roleId = $user['role_id'];

            //create hash dari entry
            $hash = \Security::genHash($username, $password);

            //check apakah password yg di entry benar ?
            if ($hash==$user['hash_password']){
                //apabila benar maka masuk ke home
                session()->set('username', $username);
                session()->set('admin_id', $adminId);
                session()->set('role_id', $roleId);
                return redirect()->to('/home');
            }

            //show error kalo password salah
            $this->setErrorMessage('Username/password not valid');
            return redirect()->to('/login');
        }

        return redirect()->to('/login');
    }

    public function logout()
    {
        //hapus semua session
        session()->destroy();

        return redirect()->to('login');
    }


}