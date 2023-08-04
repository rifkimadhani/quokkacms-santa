<?php

/**
 * Created by PageBuilder
 * Date: 2023-05-08 15:14:15
 */

namespace App\Controllers;

use App\Models\RoomModel;
use App\Models\RoomForm;
use App\Models\ThemeModel;

class Room extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'room/index';
        $primaryKey = 'room_id';
        $pageTitle = 'Room';

        $model = new RoomModel();
        $fieldList = $model->getFieldList();
        $roomtypeData = $model->getTypeForSelect();
        $stbData = $model->getStbForSelect();

        $themeModel= new ThemeModel();
        $themeData = $themeModel->getThemeForSelect();

        $form = new RoomForm($roomtypeData, $themeData, $stbData);

        return view('layout/template', compact('mainview', 'primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new RoomModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new RoomModel();
        $themeId = $_POST['theme_id'];

        $this->normalizeData($_POST, true);

        // check if theme is selected
        if (empty($themeId)) {  
            $this->setErrorMessage('Insert failed: Please select Theme! '. $model->errMessage);
            return redirect()->back();
        }

        $r = $model->add($_POST);

        if ($r>0){
            $this->setSuccessMessage('Insert success');
        } else {
            $this->setErrorMessage('Insert failed ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * function ini di call saat user click row pada table
     *
     * @return mixed html dialog
     */
    public function edit($roomId)
    {
        $model = new RoomModel();
        $data = $model->get($roomId);
        $roomtypeData = $model->getTypeForSelect();

        $selectedStb = $model->getStbRoom($roomId);

        $stbData = $model->getStbForSelect();
        if (!empty($selectedStb)) {
            $stbData = $model->getStbForEdit($selectedStb);
        }

        $themeModel= new ThemeModel();
        $themeData = $themeModel->getThemeForSelect();

        $form = new RoomForm($roomtypeData, $themeData, $stbData, $selectedStb);

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new RoomModel();
        $themeId = $_POST['theme_id'];

        $this->normalizeData($_POST);

        // check if theme is selected
        if (empty($themeId)) {  
            $this->setErrorMessage('Update failed: Please select Theme! '. $model->errMessage);
            return redirect()->back();
        }
        
        $roomId = $_POST['room_id']; // Get the room_id from the $_POST data
        $value = $_POST; // Pass the entire $_POST data as the $value
    
        $r = $model->modify($roomId, $value); // Pass both arguments to the modify() method    

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update failed ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($roomId){
        $model = new RoomModel();
        $r = $model->remove($roomId);

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
