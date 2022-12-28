<?php

/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/20/2022
 * Time: 9:55 AM
 */

namespace App\Controllers;

use App\Models\SubscriberGroupForm;
use App\Models\SubscriberGroupModel;

class SubscriberGroup extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = "subscriber_group/index";
        $primary = 'group_id';
        $pageTitle = 'Guest group';

        $group = new SubscriberGroupModel();
        $fieldList = $group->getFieldList();

        return view('template', compact('mainview','primary', 'fieldList', 'pageTitle', 'baseUrl'));
    }

    public function ssp()
    {
        $group = new SubscriberGroupModel();

        header('Content-Type: application/json');
        echo json_encode($group->getSsp());
    }

//    public function detail(int $message_id)
//    {
//        $mainview   = "messages/detail";
//        $messages   = $this->maindb->getMessagesByIDMessages($message_id);
//        $this->load->view('template', compact('mainview','messages'));
//    }

    public function insert(){
        $name = $_POST['name'];
        $status = $_POST['status'];

        $model = new SubscriberGroupModel();
        $r = $model->add(['name'=>$name, 'status'=>$status]);

//        return redirect()->to('subscribergroup');
        return redirect()->to($this->baseUrl);
    }

    public function edit($groupId)
    {
        $model = new SubscriberGroupModel();
        $data = $model->get($groupId);

        $form = new SubscriberGroupForm();

        $urlAction = base_url('/subscribergroup/update');
        return $form->renderForm('Edit group', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $groupId = $_POST['group_id'];
        $name = $_POST['name'];
        $status = $_POST['status'];

        $model = new SubscriberGroupModel();
        $r = $model->modify($groupId, $name, $status);

        if ($r>0){
            $this->setSuccessMessage('UPDATE success');
        } else {
            $this->setErrorMessage('UPDATE fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($groupId){
        $model = new SubscriberGroupModel();
        $r = $model->remove($groupId);

        return redirect()->to($this->baseUrl);
    }
}
