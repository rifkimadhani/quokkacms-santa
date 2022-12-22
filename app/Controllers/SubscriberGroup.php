<?php

/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/20/2022
 * Time: 9:55 AM
 */

namespace App\Controllers;

use App\Libraries\FormBuilder;
use CodeIgniter\Controller;
use App\Models\SubscriberGroupForm;
use App\Models\SubscriberGroupModel;

class SubscriberGroup extends BaseController
{
//    public $headertitle = 'Guest group';

//    public function __construct()
//    {
//        parent::__construct();
//        $this->load->model('SubscriberGroupModel', 'maindb');
//        $this->load->model('MessagesFormModel');
//        $this->load->model('MessagesSspModel','sspmodel');
//    }

    public function index()
    {

        $mainview = "subscriber_group/index";
        $primary = 'group_id';
        $title = 'Guest group';

        $group = new SubscriberGroupModel();
        $fieldList = $group->getFieldList();

        return view('template', compact('mainview','primary', 'fieldList', 'title'));
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

        return redirect()->to('subscribergroup');
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

        $model = new SubscriberGroupModel();
        $r = $model->modify($groupId, $_POST);

        return redirect()->to('subscribergroup');
    }

    public function delete($groupId){
        $model = new SubscriberGroupModel();
        $r = $model->modify($groupId, $_POST);

    }

//    public function delete()
//    {
//        if($this->input->server('REQUEST_METHOD') == 'POST')
//        {
////            $columnName 	= $this->input->post('columnName');
//            $groupId = $this->input->post('columnValue');
//
//            $err = $this->maindb->delete($groupId);
//
//            $this->logVarDump($err);
//
//            //apabila ada error maka tampilkan ke user
//            if ($err['code']>0){
//                $this->session->set_flashdata('error_msg', 'DELETE FAIL, GROUP IN USE');
//            } else {
//                $this->session->set_flashdata('success_msg', 'DELETE SUCCESS');
//            }
//
//            redirect($this->agent->referrer());
//        }
//        else
//        {
//            redirect('errorpage/methodnotallowed');
//        }
//    }

//    public function bulkdelete()
//    {
//        if($this->input->server('REQUEST_METHOD') == 'POST')
//        {
//            $bulk_message_id 	= $this->input->post('bulk_message_id');
//            $arraymessage_id = explode(",",$bulk_message_id);
//            $this->maindb->bulkDelete($arraymessage_id);
//            redirect($this->agent->referrer());
//        }
//        else
//        {
//            redirect('errorpage/methodnotallowed');
//        }
//    }

//    protected function procesToDatabase($typeaction)
//    {
//        $this->load->model('SubscriberGroupForm');
//
//        $groupId = 0;
//        $metadata 	= new SubscriberGroupForm();
//
//        $datatosave = [];
//        foreach ($metadata as $key => $value)
//        {
//            $datatosave[$key] = $this->input->post($key);
//
//            //simpan value groupId
//            if ($key=='group_id') $groupId = $this->input->post($key);
//        }
//
//        if($typeaction == 'create')
//        {
//            $this->maindb->insert($datatosave);
//        }
//        else
//        {
//            $this->maindb->update($groupId, $datatosave);
//        }
//    }

}
