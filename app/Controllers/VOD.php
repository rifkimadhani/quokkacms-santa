<?php

/**
 * Created by PageBuilder
 * Date: 2023-05-10 11:42:27
 */

namespace App\Controllers;

use App\Models\VODModel;
use App\Models\VODForm;
use App\Models\VODGenreModel;

class VOD extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'vod/index';
        $pageTitle = 'VOD';
        $primaryKey = 'vod_id';

        $model = new VODModel();
        $fieldList = $model->getFieldList();

        $genre = new VODGenreModel();
        $genreData = $genre->getForSelect();

        $form = new VODForm($genreData);

        return view('layout/template', compact('mainview', 'fieldList', 'primaryKey', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new VODModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

//        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new VODModel();

        $this->normalizeData($_POST, true);

        // convert url -> {BASE-HOST}
        $_POST['url_poster'] = str_replace($this->baseHost, '{BASE-HOST}', $_POST['url_poster']);

        // convert url -> {BASE-HOST}
        $_POST['url_stream1'] = str_replace($this->baseHost, '{BASE-HOST}', $_POST['url_stream1']);

        // convert url -> {BASE-HOST}
        $_POST['url_trailer'] = str_replace($this->baseHost, '{BASE-HOST}', $_POST['url_trailer']);

        

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
    public function edit($vodId)
    {
        $model = new VODModel();
        $data = $model->get($vodId);
        // dd($data);
        $genre = new VODGenreModel();
        $genreData = $genre->getForSelect();
        
        // Fetch the selected genres for the VOD
        $selectedGenres = $genre->getGenresForVOD($vodId);
        // dd($selectedGenres);

        // convert {BASE-HOST} --> URL
        $data['url_poster'] = str_replace('{BASE-HOST}', $this->baseHost, $data['url_poster']);

        // convert url -> {HOST-VOD}
        $data['url_stream1'] = str_replace('{BASE-HOST}', $this->baseHost, $data['url_stream1']);

        // convert url -> {HOST-VOD}
        $data['url_trailer'] = str_replace('{BASE-HOST}', $this->baseHost, $data['url_trailer']);


        $form = new VODForm($genreData, $selectedGenres);

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new VODModel();

        $this->normalizeData($_POST);

        // convert url -> {BASE-HOST}
        $_POST['url_poster'] = str_replace($this->baseHost, '{BASE-HOST}', $_POST['url_poster']);

        // convert url -> {BASE-HOST}
        $_POST['url_stream1'] = str_replace($this->baseHost, '{BASE-HOST}', $_POST['url_stream1']);

        // convert url -> {BASE-HOST}
        $_POST['url_trailer'] = str_replace($this->baseHost, '{BASE-HOST}', $_POST['url_trailer']);

        $vodId = $_POST['vod_id']; // Get the vod_id from the $_POST data
        $value = $_POST; // Pass the entire $_POST data as the $value
    
        $r = $model->modify($vodId, $value); // Pass both arguments to the modify() method    

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($vodId){
        $model = new VODModel();
        $r = $model->remove($vodId);

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
        foreach($data['data'] as &$row){

        }
    }
}
