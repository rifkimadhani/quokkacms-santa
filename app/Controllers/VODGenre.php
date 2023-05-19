<?php

/**
 * Created by PageBuilder
 * Date: 2023-05-11 13:48:16
 */

namespace App\Controllers;

use App\Models\VODGenreModel;
use App\Models\VODGenreForm;

class VODGenre extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'vodgenre/index';
        $pageTitle = 'VODGenre';
        $primaryKey = 'genre_id';

        $model = new VODGenreModel();
        $fieldList = $model->getFieldList();

        $form = new VODGenreForm();

        return view('layout/template', compact('mainview', 'fieldList', 'primaryKey', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new VODGenreModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new VODGenreModel();

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
    public function edit($genreId)
    {
        $model = new VODGenreModel();
        $data = $model->get($genreId);


        $form = new VODGenreForm();

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new VODGenreModel();

        $this->normalizeData($_POST);

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($genreId){
        $model = new VODGenreModel();
        $r = $model->remove($genreId);

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
