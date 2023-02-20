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

        $model = new AdminModel();
        $messageId = $model->add($_POST);

        $this->setSuccessMessage('New admin user create success');

        return redirect()->to($this->baseUrl);
    }

    public function update(){
        $adminId = $_POST['admin_id'];

        $model = new AdminModel();
        $r = $model->update($adminId, $_POST);

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