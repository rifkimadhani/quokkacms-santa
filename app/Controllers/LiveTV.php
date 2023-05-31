<?php

/**
 * Created by PageBuilder
 * Date: 2023-05-30 16:17:57
 */

namespace App\Controllers;

use App\Models\LiveTVModel;
use App\Models\LiveTVForm;

class LiveTV extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'livetv/index';
        $primaryKey = 'livetv_id';
        $pageTitle = 'LiveTV';

        $model = new LiveTVModel();
        $fieldList = $model->getFieldList();
        $languageData = $model->getLangForSelect();
        $categoryData = $model->getCategoryForSelect();

        $form = new LiveTVForm($languageData, $categoryData);

        return view('layout/template', compact('mainview', 'primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new LiveTVModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new LiveTVModel();

        $this->normalizeData($_POST, true);

        // convert url -> {BASE-HOST}
        $_POST['url_station_logo'] = str_replace($this->baseHost, '{BASE-HOST}', $_POST['url_station_logo']);

        $r = $model->add($_POST);

        if ($r>0){
            $this->setSuccessMessage('Insert success');
        } else {
            $this->setErrorMessage('Insert failed: ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * function ini di call saat user click row pada table
     *
     * @return mixed html dialog
     */
    public function edit($livetvId)
    {
        $model = new LiveTVModel();
        $data = $model->get($livetvId);
        $languageData = $model->getLangForSelect();
        $categoryData = $model->getCategoryForSelect();

        // convert {BASE-HOST} --> URL
        $data['url_station_logo'] = str_replace('{BASE-HOST}', $this->baseHost, $data['url_station_logo']);

        $form = new LiveTVForm($languageData, $categoryData);

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new LiveTVModel();

        $this->normalizeData($_POST);

        // convert url -> {BASE-HOST}
        $_POST['url_station_logo'] = str_replace($this->baseHost, '{BASE-HOST}', $_POST['url_station_logo']);

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($livetvId){
        $model = new LiveTVModel();
        $r = $model->remove($livetvId);

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
