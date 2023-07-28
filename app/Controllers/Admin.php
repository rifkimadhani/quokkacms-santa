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

        $roles = $model->getRolesForSelect();
        $jsonData = [];
        
        $form = new AdminForm($roles, $jsonData);

        return view('layout/template', compact('mainview','data', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function detail($adminId){
        $model = new AdminModel();
        $data = $model->getById($adminId);

        $roles = $model->getRolesForSelect();
        $jsonData = json_decode($data['json'], true)['roles'] ?? [];

        // Set the array value as the id
        $jsonData = array_combine($jsonData, $jsonData);

        $form = new AdminForm($roles, $jsonData);

        // dd($jsonData);
        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function insert(){

        $username = $_POST['username'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        // check if the username already exists
        $adminModel = new AdminModel();
        $existingAdmin = $adminModel->get($username);
        if ($existingAdmin) {
            $this->setErrorMessage('Username already exists. Please choose a different username.');
            return redirect()->to($this->baseUrl);
        }

        if (empty($password)){
            $this->setErrorMessage('Password must not empty');
            return redirect()->to($this->baseUrl);
        }

        if ($password!=$password2){
            $this->setErrorMessage('Password not matched');
            return redirect()->to($this->baseUrl);
        }

        // check if the 'json' field is not empty and build an array from it
        $jsonData = [];
        if (!empty($_POST['json'])) {
            foreach ($_POST['json'] as $role) {
                $jsonData['roles'][] = $role;
            }
            $_POST['json'] = json_encode($jsonData);
        } else {
            $_POST['json'] = null;
        }
        

        require_once __DIR__ . '/../../library/Security.php';
        $hash = \Security::genHash($username, $password);

        $model = new AdminModel();
        $messageId = $model->add(['username'=>$username, 'hash_password'=>$hash, 'json' => $_POST['json']]);

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
        
        // check if the username already exists
        $adminModel = new AdminModel();
        $existingAdmin = $adminModel->get($username);
        if ($existingAdmin) {
            $this->setErrorMessage('Username already exists. Please choose a different username.');
            return redirect()->to($this->baseUrl);
        }

        // Check if the 'json' field is not empty and build an array from it
        $jsonData = [];
        if (!empty($_POST['json'])) {
            foreach ($_POST['json'] as $role) {
                $jsonData['roles'][] = $role;
            }
            $_POST['json'] = json_encode($jsonData);
        } else {
            $_POST['json'] = null;
        }

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
        $r = $model->modify($adminId, ['username' => $username, 'json' => $_POST['json']]);
        $r = $model->modifyPassword($adminId, $hash);

        // Update the 'json' field separately from the rest of the fields
        $jsonUpdateResult = $model->modifyJson($adminId, $_POST['json']);

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