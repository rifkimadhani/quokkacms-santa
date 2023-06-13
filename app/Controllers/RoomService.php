<?php

/**
 * Created by PageBuilder
 * Date: 2023-06-12 12:48:42
 */

namespace App\Controllers;

use App\Models\RoomServiceItemModel;
use App\Models\RoomServiceModel;
use App\Models\RoomServiceForm;

class RoomService extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'roomservice/index';
        $pageTitle = 'Room Service';

        $model = new RoomServiceModel();
        $fieldList = $model->getFieldList();

        $form = new RoomServiceForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function history()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'roomservice/history';
        $pageTitle = 'Room Service - history';

        $model = new RoomServiceModel();
        $fieldList = $model->getFieldList();

        $form = new RoomServiceForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new RoomServiceModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function sspHistory()
    {
        $model = new RoomServiceModel();

        header('Content-Type: application/json');

        $data = $model->getSspHistory();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new RoomServiceModel();

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
    public function edit($orderCode)
    {
        $item = new RoomServiceItemModel();

        $model = new RoomServiceModel();
        $data = $model->get($orderCode);
        $detail = $item->get($orderCode);

        $urlPost = $this->baseUrl . '/update';
        return view('roomservice/dialog-order', compact('data', 'urlPost', 'detail'));
    }

    public function update(){
        $model = new RoomServiceModel();

        $orderCode = $_POST['order_code'];
        $data = $model->get($orderCode);
        $status = $data['status'];

        switch (strtoupper($status)){
            case 'NEW':
                $newStatus = 'PROCESS';
                break;

            case 'PROCESS':
                $newStatus = 'ENROUTE';
                break;

            case 'ENROUTE':
                $newStatus = 'DELIVERED';
                break;

            default:
                $newStatus = '';
                break;
        }

        $r = $model->modifyStatus($orderCode, $newStatus);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($orderCode){
        $model = new RoomServiceModel();
        $r = $model->remove($orderCode);

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
