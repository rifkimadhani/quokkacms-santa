<?php

/**
 * Created by PageBuilder
 * Date: 2023-04-06 09:47:23
 */

namespace App\Controllers;

use App\Models\Livetv_EPGModel;
use App\Models\Livetv_EPGForm;

class Livetv_EPG extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'livetv_epg/index';
        $primaryKey = 'epg_id';
        $pageTitle = 'Livetv EPG';

        $model = new Livetv_EPGModel();
        $fieldList = $model->getFieldList();
        $livetvData = $model->getChannelForSelect();

        $form = new Livetv_EPGForm($livetvData);

        return view('layout/template', compact('mainview', 'primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new Livetv_EPGModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new Livetv_EPGModel();

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
    public function edit($epgId)
    {
        $model = new Livetv_EPGModel();
        $data = $model->get($epgId);
        $livetvData = $model->getChannelForSelect();


        $form = new Livetv_EPGForm($livetvData);

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new Livetv_EPGModel();

        $this->normalizeData($_POST);

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($epgId){
        $model = new Livetv_EPGModel();
        $r = $model->remove($epgId);

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


    /**
     * Exports EPG data as an XML file for a specified date range and offset.
     */
    public function export()
    {
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $offset = intval($this->request->getPost('offset'));

        // for Filename
        $startDateTime = new \DateTime($startDate);
        $startDate = $startDateTime->format('Ymd');

        // $endDateTime = new \DateTime($endDate);
        // $endDate = $endDateTime->format('Ymd');
    
        $model = new Livetv_EPGModel();
        $data = $model->getAll($startDate, $endDate);
    
        $epgXml = $model->generateEpgXml($data, $offset);
        
        // Set the filename for the exported file
        $filename = 'epg-'.$startDate.'.xml';

        // Set the headers for the XML file download
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($epgXml));

        // Output the XML file
        echo $epgXml;
    }
        
    
}
