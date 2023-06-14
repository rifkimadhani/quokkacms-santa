<?php

namespace App\Controllers;

use App\Models\EmergencyHistoryModel;
use App\Models\EmergencyCategoryForm;

class EmergencyHistory extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'emergencyhistory/index';
        $pageTitle = 'Emergency History';
        $primaryKey = 'emergency_history_id';

        $model = new EmergencyHistoryModel();
        $fieldList = $model->getFieldList();

        $form = new EmergencyCategoryForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form', 'primaryKey'));
    }

    public function ssp()
    {
        $model = new EmergencyHistoryModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function delete($emergencyHistoryId){
        $model = new EmergencyHistoryModel();
        $r = $model->remove($emergencyHistoryId);

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
