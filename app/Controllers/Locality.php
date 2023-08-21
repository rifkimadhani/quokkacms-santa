<?php

/**
 * Created by PageBuilder
 * Date: 2023-03-28 12:20:30
 */

namespace App\Controllers;

use App\Models\LocalityMediaModel;
use App\Models\LocalityModel;
use App\Models\LocalityForm;

class Locality extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'locality/index';
        $primaryKey = 'locality_id';
        $pageTitle = 'Locality';

        $model = new LocalityModel();
        $fieldList = $model->getFieldList();

        $form = new LocalityForm();

        return view('layout/template', compact('mainview', 'primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new LocalityModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new LocalityModel();

        $this->normalizeData($_POST, true);

        $r = $model->add($_POST);

        if ($r instanceof \PDOException) {
            $this->setErrorMessage($r->getMessage());
        } else {
            $this->setSuccessMessage('Insert success');

            $media = new LocalityMediaModel();
            $media->modify($r, $_POST['url_media']);
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * function ini di call saat user click row pada table
     *
     * @return mixed html dialog
     */
    public function edit($localityId)
    {
        $media = new LocalityMediaModel();
        $list = $media->get($localityId);

        $url_media = '';
        foreach ($list as &$item){
            $image = $item['url_image'];
            $video = $item['url_video'];

            //pilih salah satu, image / video
            $media = (strlen($video)>0) ? $video : $image;

            //merge dalam $url_media
            if (strlen($url_media)==0){
                $url_media = $media;
            } else {
                $url_media .= ',' . $media;
            }
        }

        $model = new LocalityModel();
        $data = $model->get($localityId);
        $data['url_media'] = str_replace('{BASE-HOST}', $this->baseHost, $url_media);

        $form = new LocalityForm();

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new LocalityModel();

        $this->normalizeData($_POST);

        //simpan data locality
        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');

            //simpan image / video
            $localityId = $_POST['locality_id'];
            $media = new LocalityMediaModel();
            $media->modify($localityId, $_POST['url_media']);

        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($localityId){
        $model = new LocalityModel();
        $r = $model->remove($localityId);

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

        //conversikan url --> {BASE-HOST}
        $data['url_media'] = str_replace($this->baseHost, '{BASE-HOST}', $data['url_media']);
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
