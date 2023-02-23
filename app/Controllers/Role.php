<?php

/**
 * Created by PageBuilder
 * Date: 2023-02-23 13:07:39
 */

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\RoleForm;

class Role extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'role/index';
        $pageTitle = 'Role';

        $model = new RoleModel();
        $fieldList = $model->getFieldList();

        $form = new RoleForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new RoleModel();

        header('Content-Type: application/json');
        echo json_encode($model->getSsp());
    }

    public function insert(){
        $model = new RoleModel();
        $r = $model->add($_POST);

        return redirect()->to($this->baseUrl);
    }

    public function edit($id)
    {
        $model = new RoleModel();
        $data = $model->get($id);

        $form = new RoleForm();

        $urlAction = $this->baseUrl . '/update';//base_url('/subscribergroup/update');
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $id = $_POST['role_id'];
        $roleName = $_POST['role_name'];


        $model = new RoleModel();
        $r = $model->modify($id, $roleName);

        if ($r>0){
            $this->setSuccessMessage('UPDATE success');
        } else {
            $this->setErrorMessage('UPDATE fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($id){
        $model = new RoleModel();
        $r = $model->remove($id);

        return redirect()->to($this->baseUrl);
    }
}
