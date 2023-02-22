<?php

/**
 * Created by PageBuilder
 * Date: __TODAY__
 */

namespace App\Controllers;

use App\Models\__Model__;
use App\Models\__Form__;

class __Controller__ extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = '__view__';
        $pageTitle = '__title__';

        $model = new __Model__();
        $fieldList = $model->getFieldList();

        $form = new __Form__();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new __Model__();

        header('Content-Type: application/json');
        echo json_encode($model->getSsp());
    }

    public function insert(){
        $model = new __Model__();
        $r = $model->add($_POST);

        return redirect()->to($this->baseUrl);
    }

    public function edit($id)
    {
        $model = new __Model__();
        $data = $model->get($id);

        $form = new __Form__();

        $urlAction = $this->baseUrl . '/update';//base_url('/subscribergroup/update');
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $id = $_POST['__pk__'];
__update_field__;

        $model = new __Model__();
        $r = $model->modify($id, __modify_field__);

        if ($r>0){
            $this->setSuccessMessage('UPDATE success');
        } else {
            $this->setErrorMessage('UPDATE fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($id){
        $model = new __Model__();
        $r = $model->remove($id);

        return redirect()->to($this->baseUrl);
    }
}
