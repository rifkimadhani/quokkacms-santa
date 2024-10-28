<?php

/**
 * Created by PageBuilder
 * Date: 2023-03-30 15:24:23
 */

namespace App\Controllers;

use App\Models\FacilityMediaModel;
use App\Models\FacilityModel;
use App\Models\FacilityForm;

class Facility extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'facility/index';
        $primaryKey = 'facility_id';
        $pageTitle = 'Facility';

        $model = new FacilityModel();
        $fieldList = $model->getFieldList();

        $form = new FacilityForm();

        return view('layout/template', compact('mainview', 'primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new FacilityModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

//        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new FacilityModel();

        $this->normalizeData($_POST, true);

        $r = $model->add($_POST);

        if (is_string($r)){
            $this->setErrorMessage($r);
        } else {
            $this->setSuccessMessage('Insert success');
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * function ini di call saat user click row pada table
     *
     * @return mixed html dialog
     */
    public function edit($facilityId)
    {
        $model = new FacilityModel();
        $data = $model->get($facilityId);

        //ambil data gambar dari FacilityMedia
        $media = new FacilityMediaModel();
        $medias = $media->getAll($facilityId);

        //merge semua media dalam 1 string, di split dgn ,
        $url_media = '';
        foreach ($medias as $item){
            //ambil url dari image atau video
            $url = ($item['url_image']!=null) ? $item['url_image'] : $item['url_video'];

            if (strlen($url_media)==0){
                $url_media = $url;
            } else {
                $url_media .= ',' . $url;
            }
        }

        //assign hasil merge ke dalam url_image, shg bisa di tampilkan pada form
        $baseHost = $this->getBaseHost();
        $data['url_image'] = str_replace('{BASE-HOST}', $baseHost, $url_media);

        $form = new FacilityForm();

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new FacilityModel();

        $this->normalizeData($_POST);

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update failed ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($facilityId){
        $model = new FacilityModel();
        $r = $model->remove($facilityId);

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
        //convert http://localhost/assets --> {BASE-HOST}/assets
        $baseHost = $this->getBaseHost();
        $data['url_image'] = str_replace($baseHost, '{BASE-HOST}', $data['url_image']);
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
