<?php

/**
 * Created by PageBuilder
 * Date: 2023-06-06 16:24:59
 */

namespace App\Controllers;

use App\Models\HotelServiceModel;
use App\Models\HotelServiceForm;

class HotelService extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'hotelservice/index';
        $pageTitle = 'HotelService';

        $model = new HotelServiceModel();
        $fieldList = $model->getFieldList();

        $form = new HotelServiceForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function history()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'hotelservice/history';
        $pageTitle = 'HotelService';

        $model = new HotelServiceModel();
        $fieldList = $model->getFieldList();

        $form = new HotelServiceForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new HotelServiceModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function sspOld()
    {
        $model = new HotelServiceModel();

        header('Content-Type: application/json');

        $data = $model->getSspOld();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new HotelServiceModel();

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
    public function edit($taskId)
    {
        $model = new HotelServiceModel();
        $data = $model->get($taskId);

        $urlPost = $this->baseUrl . '/update';

        return view('hotelservice/dialog-order', compact('data', 'urlPost'));

    }

    public function update(){
        $model = new HotelServiceModel();

        $taskId = $_POST['task_id'];

        $data = $model->get($taskId);
        $status = $data['status'];

        switch ($status){
            case HotelServiceModel::STATUS_NEW:
                $result= $model->modifyStatus($data, HotelServiceModel::STATUS_ACK);
                $msg =  "Task status updated to ACK";
                $this->setSuccessMessage($msg);
                break;
            case HotelServiceModel::STATUS_ACK:
                $result= $model->modifyStatus($data, HotelServiceModel::STATUS_FINISH);
                $msg =  "Task status updated to FINISH";
                $this->setSuccessMessage($msg);
                break;
            default:
                break;
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($taskId){
        $model = new HotelServiceModel();
        $r = $model->remove($taskId);

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
