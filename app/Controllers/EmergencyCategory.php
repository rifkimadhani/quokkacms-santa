<?php

/**
 * Created by PageBuilder
 * Date: 2023-06-07 15:53:32
 */

namespace App\Controllers;

use App\Models\EmergencyCategoryModel;
use App\Models\EmergencyCategoryForm;

class EmergencyCategory extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'emergencycategory/index';
        $pageTitle = 'Emergency Category';
        $primaryKey = 'emergency_code';

        $model = new EmergencyCategoryModel();
        $fieldList = $model->getFieldList();

        $form = new EmergencyCategoryForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form', 'primaryKey'));
    }

    public function ssp()
    {
        $model = new EmergencyCategoryModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new EmergencyCategoryModel();

        $this->normalizeData($_POST, true);

        // convert url -> {BASE-HOST}
        $_POST['url_image'] = str_replace($this->baseHost, '{BASE-HOST}', $_POST['url_image']);

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
    public function edit($emergencyCode)
    {
        $model = new EmergencyCategoryModel();
        $data = $model->get($emergencyCode);

        // convert {BASE-HOST} --> URL
        $data['url_image'] = str_replace('{BASE-HOST}', $this->baseHost, $data['url_image']);

        $form = new EmergencyCategoryForm();

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new EmergencyCategoryModel();

        $this->normalizeData($_POST);

        // convert url -> {BASE-HOST}
        $_POST['url_image'] = str_replace($this->baseHost, '{BASE-HOST}', $_POST['url_image']);

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($emergencyCode){
        $model = new EmergencyCategoryModel();
        $r = $model->remove($emergencyCode);

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

    }

    /**
     * melakukan conversi data ke asalnya, misalnya utk url balik dari BASE-HOST -> http://
     * @param $data
     */
    protected function sspDataConversion(&$data){
        return;

        foreach($data['data'] as &$row){

        }
    }
}
