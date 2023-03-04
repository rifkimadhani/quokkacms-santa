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

        $this->normalizeData($_POST, true);

        $r = $model->add($_POST);

        if ($r>0){
            $this->setSuccessMessage('Insert success');
        } else {
            $this->setErrorMessage('Insert fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * function ini di call saat user click row pada table
     *
     * @return mixed html dialog
     */
    public function edit(__pk_param__)
    {
        $model = new __Model__();
        $data = $model->get(__pk_param__);

//__convert_baseurl__
        $form = new __Form__();

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new __Model__();

        $this->normalizeData($_POST);

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete(__pk_param__){
        $model = new __Model__();
        $r = $model->remove(__pk_param__);

        if ($r>0){
            $this->setSuccessMessage('Delete success');
        } else {
            $this->setErrorMessage('Delete fail');
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * melaukan proses normalisasi data apabila di butuhkan
     *
     * @param $data array, sbg in dan out
     * @param bool $isInsert
     */
    protected function normalizeData(&$data, $isInsert=false){
//__convert_basehost__
    }
}
