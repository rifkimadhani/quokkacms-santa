<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/17/2023
 * Time: 12:59 PM
 */

namespace App\Controllers;

use App\Models\AdminForm;
use App\Models\AdminModel;
use App\Models\RoleModel;

class Admin extends BaseController
{
    /**
     * list semua record pada tadmin
     *
     * @return string
     */
    public function index()
    {
        $model = new AdminModel();

        $baseUrl = $this->baseUrl;

        $mainview = "admin/index";
        $pageTitle = 'Admin';

        $fieldList = $model->getFieldList();
        $data = $model->getAll();

        $role = new RoleModel();
        $roles = $role->getAllForSelect();
        $form = new AdminForm($roles);

        return view('layout/template', compact('mainview','data', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function detail($adminId){
        $admin = new AdminModel();
        $data = $admin->getById($adminId);

        $role = new RoleModel();
        $roles = $role->getAllForSelect();

        $form = new AdminForm($roles);

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function insert(){

        $roleId = $_POST['role_id'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];


        if (empty($password)){
            $this->setErrorMessage('Password must not empty');
            return redirect()->to($this->baseUrl);
        }

        if ($password!=$password2){
            $this->setErrorMessage('Password not matched');
            return redirect()->to($this->baseUrl);
        }

        require_once __DIR__ . '/../../library/Security.php';
        $hash = \Security::genHash($username, $password);

        $model = new AdminModel();
        $messageId = $model->add(['role_id'=>$roleId, 'username'=>$username, 'hash_password'=>$hash]);

        $this->setSuccessMessage('New admin user create success');

        return redirect()->to($this->baseUrl);
    }

    /**
     * 1. Username bisa di rubah tetapi password juga harus di masukkan
     * 2. Password bisa di rubah
     *
     * @return $this
     */
    public function update(){
        require_once __DIR__ . '/../../library/Security.php';

        $adminId = $_POST['admin_id'];

        //username bisa di update, tapi passwrod juga harus di rubah
        $username = $_POST['username'];

        //password bisa di rubah tanpa merubah username
        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        if (empty($password)){
            $this->setSuccessMessage('Nothing change');
            return redirect()->to($this->baseUrl);
        }

        if ($password!=$password2){
            $this->setErrorMessage('Password not matched');
            return redirect()->to($this->baseUrl);
        }

        $hash = \Security::genHash($username, $password);

        $model = new AdminModel();
        $r = $model->modify($adminId, $_POST);
        $r = $model->modifyPassword($adminId, $hash);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail');
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($adminId){
        $model = new AdminModel();
        $r = $model->remove($adminId);

        if ($r){
            $this->setSuccessMessage('DELETE success');
        } else {
            $this->setErrorMessage('DELETE fail');
        }

        return redirect()->to($this->baseUrl);
    }

}