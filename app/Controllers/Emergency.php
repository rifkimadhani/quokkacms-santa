<?php

namespace App\Controllers;

use App\Models\DipatcherModel;
use App\Models\EmergencyCategoryModel;
use App\Models\EmergencyHistoryModel;
use App\Models\NotificationModel;

class Emergency extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'emergency/index';
        $pageTitle = 'Emergency';

        $model = new EmergencyHistoryModel();
        // $fieldList = $model->getFieldList();
        $category   = $model->getEmergencyCategory();
        $oneactive  = $model->getOneEmergencyActive();

        // Check if emergency is active
        $isEmergency = ($oneactive->emergency_history_id != 0);

        // Notify Emergency alert to all the devices
//        NotificationModel::sendEmergencyWarningToAll($isEmergency);

        if ($isEmergency)
        {
            $json = json_encode( ['type'=>'emergency_warning_1'] );
        }
        else
        {
            $json = json_encode( ['type'=>'emergency_warning_0'] );
        }

        //sent event dgn dispatcher
        $disp = new DipatcherModel();
        $disp->sendToAll($json);

        // Pass the emergency status to the view
        $status['isEmergency'] = $isEmergency;

        return view('layout/template', compact('mainview', 'category', 'pageTitle', 'baseUrl', 'oneactive','status'));
    }

    public function turnemergency()
	{
        if ($this->request->getMethod() === 'post') {
            $model = new EmergencyHistoryModel();
            $emergencyId = $this->request->getPost('emergency_history_id');
            $emergencyCode = $this->request->getPost('emergency_code');

            if ($emergencyId) {
                $model->setEmergencyNonActive($emergencyId);
                $this->setSuccessMessage('EMERGENCY STATUS: OFF!');
            } else {
                $model->setEmergencyActive($emergencyCode);
                $this->setErrorMessage('EMERGENCY STATUS: ON!');
            }

            return redirect()->to($this->baseUrl);

        } else {
            $this->setErrorMessage('Error: Invalid method');

            return redirect()->to($this->baseUrl);
        }      
    }
}
