<?php

/**
 * Created by PageBuilder
 * Date: 2023-03-01 10:20:34
 */

namespace App\Controllers;

use App\Models\AppModel;
use App\Models\AppForm;

class App extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'app/index';
        $pageTitle = 'App';

        $model = new AppModel();
        // $entities   = $model->getOneLatesApkEachGroup();
        $fieldList = $model->getFieldList();

        $form = new AppForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new AppModel();

        header('Content-Type: application/json');
        echo json_encode($model->getSsp());
    }

    // From apk_helper.php
    function getApkInformation($full_path)
    {
        $engine      = 'aapt dump badging '.$full_path;
        exec($engine." 2>&1",$output,$return_var);
        if(count($output) > 0)
        {
            return ['status'=>'success','data'=>$output ];
            foreach($output as $value)
            {
                log_message('error',"$value\n");
            }   
        }
        else
        {
            return ['status'=>'error','data'=>'Failed To Identification. Engine Not Found'];
        }
    }

    function getPackageName($apkinformation,$keywords = 'package')
    {
        foreach($apkinformation as $value)
        {
            if(strpos($value,$keywords) !== false)
            {
                preg_match_all('/".*?"|\'.*?\'/', $value, $matches);
                return $matches[0];
            }
        }

    }

    function getMainActivity($apkinformation,$keywords = 'launchable-activity')
    {
        foreach($apkinformation as $value)
        {
            if(strpos($value,$keywords) !== false)
            {
                preg_match_all('/".*?"|\'.*?\'/', $value, $matches);
                return $matches[0];
            }
        }

    }
    // End

    public function insert(){
        $model = new AppModel();
        // $r = $model->add($_POST);
    
        // Check if the form was submitted
        if ($this->request->getMethod() === 'post') {
            // Get the uploaded file data
            $file = $this->request->getFile('apkfile');
    
            // Check if a file was uploaded
            if ($file->isValid() && ! $file->hasMoved()) {
                // Define the upload directory and allowed file types
                $upload_dir = FCPATH . 'assets/apk/';
                $allowed_types = ['apk'];
    
                // Check if the file type is allowed
                if (in_array($file->getClientExtension(), $allowed_types)) {
                    // Move the file to the upload directory
                    $file->move($upload_dir);
    
                    // Get the full path to the uploaded file
                    $full_path = $upload_dir . $file->getName();
    
                    // Get the APK information
                    $matches = $this->getApkInformation($full_path);
    
                    // Check if the APK information was obtained successfully
                    if ($matches['status'] === 'success') {
                        // Extract the package name, version code, version name, and main activity
                        $dataapk = $matches['data'];
                        $packagename = $this->getPackageName($dataapk);
                        $acktivity = $this->getMainActivity($dataapk);
    
                        // Save the APK data to the database
                        $data = [
                            'app_id' => str_replace("'", "", $packagename[0]),
                            'version_code' => str_replace("'", "", $packagename[1]),
                            'version_name' => str_replace("'", "", $packagename[2]),
                            'main_activity' => str_replace("'", "", $acktivity[0]),
                            'urlDownload' => '{BASE-HOST}/assets/apk/' . $file->getName(),
                            'path' => $full_path
                        ];
                        $r = $model->insert($data);
                        if ($r === -1) {
                            // Delete the uploaded file and display an error message
                            unlink($full_path);
                            $this->setErrorMessage('Add fail ' . $model->errMessage);
                        } else {
                            $this->setSuccessMessage('Add success');
                        }    
                    } else {
                        // Delete the uploaded file and display an error message
                        unlink($full_path);
                        $msg = $matches['data'];
                        $this->setErrorMessage('error_msg', $msg);
                    }
                } else {
                    // Display an error message if the file type is not allowed
                    $this->setErrorMessage('error_msg', 'Invalid file type. Only APK files are allowed.');
                }
            } else {
                // Display an error message if no file was uploaded
                $this->setErrorMessage('error_msg', 'No file was uploaded.');
            }
        }
    
        return redirect()->to($this->baseUrl);
    }


    // public function edit($id)
    // {
    //     $model = new AppModel();
    //     $data = $model->get($id);

    //     $form = new AppForm();

    //     $urlAction = $this->baseUrl . '/update';//base_url('/subscribergroup/update');
    //     return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    // }

    // public function update(){
    //     $id = $_POST['app_id'];
    //     $versionCode = $_POST['version_code'];


    //     $model = new AppModel();
    //     $r = $model->modify($id, $versionCode);

    //     if ($r>0){
    //         $this->setSuccessMessage('UPDATE success');
    //     } else {
    //         $this->setErrorMessage('UPDATE fail ' . $model->errMessage);
    //     }

    //     return redirect()->to($this->baseUrl);
    // }

    public function delete($id){
        $model = new AppModel();
        $r = $model->remove($id);

        return redirect()->to($this->baseUrl);
    }
}
