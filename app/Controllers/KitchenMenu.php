<?php

/**
 * Created by PageBuilder
 * Date: 2023-06-08 12:29:19
 */

namespace App\Controllers;

use App\Models\KitchenMenuGroupModel;
use App\Models\KitchenMenuModel;
use App\Models\KitchenMenuForm;

class KitchenMenu extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'kitchenmenu/index';
        $pageTitle = 'Menu';

        $model = new KitchenMenuModel();
        $fieldList = $model->getFieldList();

        $group = new KitchenMenuGroupModel();
        $data = $group->getAllForSelect();

        $form = new KitchenMenuForm($data);

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form', 'data'));
    }

    public function ssp()
    {
        $model = new KitchenMenuModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new KitchenMenuModel();

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
    public function edit($menuId)
    {
        $model = new KitchenMenuModel();
        $data = $model->get($menuId);

        $data['url_image'] = str_replace('{BASE-HOST}', $this->baseHost, $data['url_image']);

        $group = new KitchenMenuGroupModel();

        $form = new KitchenMenuForm($group->getAllForSelect());

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new KitchenMenuModel();

        $this->normalizeData($_POST);
        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($menuId){
        $model = new KitchenMenuModel();
        $r = $model->remove($menuId);

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
        $data['url_image'] = str_replace($this->baseHost, '{BASE-HOST}', $data['url_image']);

        //add kitchen_id ke data, yg di ambil dari group_id
        $groupId = $data['menu_group_id'];
        $group = new KitchenMenuGroupModel();
        $item = $group->get($groupId);

        $data['kitchen_id'] = $item['kitchen_id'];
    }

    /**
     * melakukan conversi data ke asalnya, misalnya utk url balik dari BASE-HOST -> http://
     * @param $data
     */
    protected function sspDataConversion(&$data){

        foreach($data['data'] as &$row){
            $row[9] = str_replace('{BASE-HOST}', $this->baseHost, $row[9]); //url_image

        }
    }
}
