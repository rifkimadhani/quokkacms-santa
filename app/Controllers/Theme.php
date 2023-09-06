<?php
/**
 * Created by PageBuilder
 * Date: 2023-04-04 14:18:55
 */

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\SettingModel;
use App\Models\ThemeForm;
use App\Models\ThemeModel;
use App\Models\ThemeElementForm;
use App\Models\ElementModel;

class Theme extends BaseController
{
    public function index(){
        $baseUrl = $this->getBaseUrl();

        $mainview = 'theme/index';
        $primaryKey = 'theme_id';
        $pageTitle = 'Theme';

        $model = new ThemeModel();
        $clone = $model->getThemeForSelect();

        $form = new ThemeForm($clone);
        $form->removeSetAsDefault();

        $fieldList = $model->getThemeFieldList();

        return view('layout/template', compact('mainview', 'primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function detail($themeId)
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'theme/detail';
        $primaryKey = 'theme_id';
        $pageTitle = 'Theme';

        $model = new ThemeModel();

        $theme = $model->get($themeId);

        if (is_null($theme)){
            $pageTitle = 'Theme not found';
        } else {
            $pageTitle = 'Theme detail ' . $theme['name'];
        }

        $themeElementData = $model->getAll();
        $fieldList = $model->getFieldList();
        $elementData = $model->getElementForSelect();
        $themeData = $model->getThemeForSelect();

        $form = new ThemeElementForm($elementData, $themeData);

        return view('layout/template', compact('mainview', 'primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form', 'themeElementData', 'themeId'));
    }

    /**
     * notify ke semua stb utk update theme
     *
     */
    public function notify($themeId){
        NotificationModel::sendThemeUpdateToAll();
        $this->setSuccessMessage('Notification sent to all stb');
        return redirect()->to($this->baseUrl . "/detail/$themeId");
    }

    public function ssp($themeId)
    {
        $model = new ThemeModel();

        header('Content-Type: application/json');

        $data = $model->getSsp($themeId);

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function sspTheme()
    {
        $model = new ThemeModel();

        header('Content-Type: application/json');

        $data = $model->getThemeSsp();

        echo json_encode($data);
    }

    /**
     * Check if the uploaded file is an image and has the required dimensions
     * @param array $file The uploaded file data from $_FILES
     * @param int $width The required width of the image
     * @param int $height The required height of the image
     * @return bool|string Returns true if the image has the required dimensions, otherwise returns an error message
     */
    private function checkImageDimensions($urlimage, $width, $height) {
        // check if file is an image
        if (!getimagesize($urlimage)) {
            return 'ERROR: File is not an image';
        }

        // check image dimensions
        list($uploadedWidth, $uploadedHeight) = getimagesize($urlimage);

        if ($uploadedWidth > $width || $uploadedHeight > $height) {
            return "Insert failed: Maximum Image dimensions should be {$width} x {$height} Pixels!";
        }
    
        return true;
    }

    public function cloneTheme(){
        $model = new ThemeModel();

        //ambil clone theme id
        $cloneId = $_POST['clone_theme_id'];
        unset($_POST['clone_theme_id']);

        $r = $model->clone($cloneId, $_POST);

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
    public function editTheme($themeId){

        $model = new ThemeModel();
        $data = $model->get($themeId);

        $form = new ThemeForm();
        $form->removeCloneTheme();

        $setting = new SettingModel();
        $defaultThemeId = $setting->getThemeDefault();
        if ($defaultThemeId==$themeId){
            $form->setDefault(1);
        }

        $urlAction = $this->baseUrl . '/update_theme';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function edit($themeId, $elementId)
    {
        $model = new ThemeModel();
        $data = $model->getElement($themeId, $elementId);

        // convert {BASE-HOST} --> URL
        $data['url_image'] = str_replace('{BASE-HOST}', $this->baseHost, $data['url_image']);

        $form = new ThemeElementForm();

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function updateTheme(){
        $model = new ThemeModel();

        $default = (isset($_POST['set_as_default'])) ? 1 : 0;
        if ($default>0){
            $themeId = $_POST['theme_id'];
            $setting = new SettingModel();
            $setting->setThemeDefault($themeId);
        }

        unset($_POST['default']); //hapus dari post, shg tdk menganggu update
        $r = $model->modifyTheme($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail: ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function update(){
        $model = new ThemeModel();
        $urlimage = $_POST['url_image'];
        $themeId = $_POST['theme_id'];

        $this->normalizeData($_POST);
        
        // get image dimensions
        $element = new ElementModel();
        $elementId = $_POST['element_id'];
        $telement = $element->find($elementId);

        // check if element with the submitted ID exists
        if (!$telement) {
            $this->session->setFlashdata('error_msg', 'Element with ID ' . $elementId . ' not found');
            return redirect()->back();
        }

        // get width and height from telement
        $width = intval($telement['width']);
        $height = intval($telement['height']);
    
        // check if file is uploaded
        if (!empty($urlimage)) {  
            // check image dimensions
            $r = $this->checkImageDimensions($urlimage, $width, $height);

            if ($r !== true) {
                $this->setErrorMessage('Update failed: Maximum image dimensions is '.$width.' x '. $height . $model->errMessage);
                return redirect()->back();
            }
        }

        // convert url -> {BASE-HOST}
        $_POST['url_image'] = str_replace($this->baseHost, '{BASE-HOST}', $_POST['url_image']);

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl . "/detail/$themeId");
    }

    public function deleteTheme($themeId){
        $model = new ThemeModel();
        $r = $model->removeTheme($themeId);

        if ($r>0){
            $this->setSuccessMessage('Delete success');
        } else {
            $this->setErrorMessage('Delete fail');
        }

        return redirect()->to($this->baseUrl);
    }

//    public function delete($themeId, $elementId){
//        $model = new ThemeModel();
//        $r = $model->remove($themeId, $elementId);
//
//        if ($r>0){
//            $this->setSuccessMessage('Delete success');
//        } else {
//            $this->setErrorMessage('Delete fail');
//        }
//
//        return redirect()->to($this->baseUrl);
//    }

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
     * @param string $filename
     * @param int $maxWidth
     * @param int $maxHeight
     * @return int -1 = file not image, -2 = image oversize
     */
    function checkImage(string $filename, int $maxWidth, int $maxHeight) : int {

        //1. check file type dulu
        $finfo = new finfo(FILEINFO_MIME);
        $type = $finfo->file($filename);

        //check apakah type==image ???
        if (strpos($type, 'image')===false) {
            return -1;
        }

        //2. check size
        $size = getimagesize($filename);
        $width = $size[0];
        $height = $size[1];

        if ($width>$maxWidth) return -2;
        if ($height>$maxHeight) return -2;

        return 0;
    }
}
