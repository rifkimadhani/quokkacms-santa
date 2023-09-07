<?php

/**
 * Created by PageBuilder
 * Date: 2023-06-06 12:33:41
 */

namespace App\Controllers;

use App\Models\SettingModel;
use App\Models\SettingForm;
use App\Models\SettingSimpleForm;

class Setting extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'setting/index';
        $pageTitle = 'Settings';

        $model = new SettingModel();
        $fieldList = $model->getFieldList();

        $form = new SettingForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    // for Simple Settings
    public function simple()
    {
        $baseUrl = $this->baseUrl;

        $mainview = 'setting/simple';
        $pageTitle = 'Simple Settings';

        $model = new SettingModel();
        $fieldList = $model->getSimpleFieldList();

        // var_dump($data);

        $form = new SettingSimpleForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new SettingModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    // for Simple Settings
    public function sspSimple()
    {
        $model = new SettingModel();

        header('Content-Type: application/json');

        $data = $model->getSspSimple();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new SettingModel();

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
    public function edit($settingId)
    {
        $model = new SettingModel();
        $data = $model->get($settingId);


        $form = new SettingForm();

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    // for Simple Settings
    public function editSetting($settingId)
    {
        $model = new SettingModel();
        $data = $model->get($settingId);


        $form = new SettingSimpleForm();

        $urlAction = $this->baseUrl . '/updateSetting';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new SettingModel();

        $this->normalizeData($_POST);

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    // for Simple Settings
    public function updateSetting()
    {
        $model = new SettingModel();

        $this->normalizeData($_POST);

        $r = $model->modify($_POST);

        if ($r > 0) {
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl . '/simple');
    }

    //delete tdk di pakai pada setting, krn tdk boleh di hapus
//    public function delete($settingId){
//        $model = new SettingModel();
//        $r = $model->remove($settingId);
//
//        if ($r>0){
//            $this->setSuccessMessage('Delete success');
//        } else {
//            $this->setErrorMessage('Delete fail');
//        }
//
//        return redirect()->to($this->baseUrl);
//    }

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
