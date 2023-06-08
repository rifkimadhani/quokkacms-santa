<?php

/**
 * Created by PageBuilder
 * Date: 2023-06-08 12:15:46
 */

namespace App\Controllers;

use App\Models\KitchenMenuGroupModel;
use App\Models\KitchenMenuGroupForm;

class KitchenMenuGroup extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'kitchenmenugroup/index';
        $pageTitle = 'Menu Group';

        $model = new KitchenMenuGroupModel();
        $fieldList = $model->getFieldList();

        $form = new KitchenMenuGroupForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new KitchenMenuGroupModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new KitchenMenuGroupModel();

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
    public function edit($menuGroupId)
    {
        $model = new KitchenMenuGroupModel();
        $data = $model->get($menuGroupId);


        $form = new KitchenMenuGroupForm();

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new KitchenMenuGroupModel();

        $this->normalizeData($_POST);

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($menuGroupId){
        $model = new KitchenMenuGroupModel();
        $r = $model->remove($menuGroupId);

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
