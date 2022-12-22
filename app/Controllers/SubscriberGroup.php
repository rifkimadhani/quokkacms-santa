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

class SubscriberGroup extends Controller
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

        $mainview   = "subscriber_group/index";
        $primary 	= 'group_id';
        $title = 'Guest group';

//        $group = new SubscriberGroupModel();
//        $field_list = $group->getFieldList();
//
//        $data = $group->get(1);
//        $metadata = new SubscriberGroupForm();
//        $builder = new FormBuilder();
//        return $builder->renderDialog('newForm', $metadata, '', $data);

        return view('template', compact('mainview'));
//        return view('template', compact('mainview','primary', 'metadata', 'field_list', 'title'));
    }

    public function ssp()
    {
        header('Content-Type: application/json');
        echo json_encode($this->maindb->getSsp());
    }

    public function detail(int $message_id)
    {
        $mainview   = "messages/detail";
        $messages   = $this->maindb->getMessagesByIDMessages($message_id);
        $this->load->view('template', compact('mainview','messages'));
    }
    public function ssprole()
    {
        header('Content-Type: application/json');
        echo json_encode($this->sspmodel->getAll());
    }

    public function edit($groupId)
    {
        $this->load->model('SubscriberGroupForm');

//        $this->session->set_userdata(['columnName'=>urldecode($columnName),'columnValue'=>urldecode($columnValue)]);
        $entity 	= $this->maindb->get($groupId);
//        $entity['url_image']  =  $this->maindb->getImagesByIDMessages($columnValue);
        $metadata 	= new SubscriberGroupForm();
//        unset($metadata->room_id);

        $mainview   = "util/editformmodal";
        $this->load->view($mainview, compact('entity','metadata'));
    }

    public function delete()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
//            $columnName 	= $this->input->post('columnName');
            $groupId = $this->input->post('columnValue');

            $err = $this->maindb->delete($groupId);

            $this->logVarDump($err);

            //apabila ada error maka tampilkan ke user
            if ($err['code']>0){
                $this->session->set_flashdata('error_msg', 'DELETE FAIL, GROUP IN USE');
            } else {
                $this->session->set_flashdata('success_msg', 'DELETE SUCCESS');
            }

            redirect($this->agent->referrer());
        }
        else
        {
            redirect('errorpage/methodnotallowed');
        }
    }

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

    protected function procesToDatabase($typeaction)
    {
        $this->load->model('SubscriberGroupForm');

        $groupId = 0;
        $metadata 	= new SubscriberGroupForm();

        $datatosave = [];
        foreach ($metadata as $key => $value)
        {
            $datatosave[$key] = $this->input->post($key);

            //simpan value groupId
            if ($key=='group_id') $groupId = $this->input->post($key);
        }

        if($typeaction == 'create')
        {
            $this->maindb->insert($datatosave);
        }
        else
        {
            $this->maindb->update($groupId, $datatosave);
        }
    }

}
