<?php

/**
 * Created by PageBuilder
 * Date: 2023-06-14 09:28:36
 */

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserForm;

class User extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'user/index';
        $pageTitle = 'User';

        $model = new UserModel();
        $fieldList = $model->getFieldList();

        $form = new UserForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new UserModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
//        $model = new UserModel();

//        $this->normalizeData($_POST, true);

//        $r = $model->add($_POST);

//        if ($r>0){
//            $this->setSuccessMessage('Insert success');
//        } else {
//            $this->setErrorMessage('Insert fail ' . $model->errMessage);
//        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * function ini di call saat user click row pada table
     *
     * @return mixed html dialog
     */
    public function edit($userId)
    {
        $model = new UserModel();
        $data = $model->get($userId);


        $form = new UserForm();

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new UserModel();

        $this->normalizeData($_POST);

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($userId){
        $model = new UserModel();
        $r = $model->remove($userId);

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
