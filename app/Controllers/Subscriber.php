<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

namespace App\Controllers;

use App\Models\SubscriberFormModel;
use App\Models\SubscriberModel;

class Subscriber extends BaseController
{
//	public $headertitle = '';
	public $tabletitle  = 'LIST GUEST CHEKCIN';
//	public function __construct()
//	{
//        parent::__construct();
//		$this->load->model('SubscriberModel', 'maindbaction');
//		$this->load->model('BillingModel', 'bilingmodel');
//		$this->load->model('SubscriberFormModel');
//		$this->load->model('SubscriberSspModel','sspmodel');
//		$this->load->helper('url');
//	}

	public function index()
	{
	    $db = new SubscriberModel();

		$mainview   = "subscriber/bootstrap";
        $data   	= compact('mainview');
		$primary 	= 'subscriber_id';

		$room       = $db->getRoom();
		$package    = $db->getPackage();
		$theme      = $db->getTheme();
        $field_list = $db->getFieldList();
        $subscribers= $db->getJoinAll();

		$metadata 	= new SubscriberFormModel($room, $theme, $package);
		$usemodal   = true;//$this->usemodal;
        $headertitle = '';

		return view('template', compact('mainview','primary','metadata','usemodal','field_list','subscribers', 'headertitle'));
	}
	
	public function history()
	{
		$mainview   = "subscriber/history";
		$primary 	= 'subscriber_id';
		$field_list = $this->maindbaction->getFieldList();
		$subscribers= $this->maindbaction->getHistory();
		$this->load->view('template', compact('mainview','primary','field_list','subscribers'));
    }
    
    public function ssprole()
	{
		header('Content-Type: application/json');
		echo json_encode($this->sspmodel->getAll());
	}

	public function detail(int $subscriber_id)
	{
//		$subscriber_id = (int) $this->uri->segment('2');
		$this->session->set_userdata(['subscriber_id'=>urldecode($subscriber_id)]);
		$this->session->set_userdata(['columnName'=>'subscriber_id','columnValue'=>$subscriber_id]);
		$mainview   = "subscriber/detail";
		$entity 	= $this->maindbaction->getOneBy($subscriber_id);
		$this->session->set_userdata(['subscriber_entity'=>$entity]);
		unset($entity['room_id']);
		$room       = $this->maindbaction->getRoomForEdit();
		$package    = $this->maindbaction->getPackage();
		$theme      = $this->maindbaction->getTheme();
		$metadata 	= new SubscriberFormModel($room,$theme,$package);
		$summarys   = $this->maindbaction->getBillingSummary($subscriber_id); 
		$bilings    = $this->maindbaction->getBilling($subscriber_id); 
		// header('Content-Type: application/json');
		// echo json_encode($summarys);die;
		$this->load->view('template', compact('mainview','entity','metadata','summarys','bilings','subscriber_id'));
	}

	public function checkout(int $subscriber_id = null,int $room_id = null)
	{
		if( $subscriber_id && $room_id)
		{
			$countresult = $this->maindbaction->checkoutsubscriberbyroom($subscriber_id,$room_id);
			if($countresult == 0)
			{
				redirect('subscriber');
			}
			else
			{
				redirect($this->agent->referrer());
			}
		}
		else
		{
			$this->maindbaction->checkoutsubscriber($subscriber_id);
			redirect('subscriber');
		}
		
	}

	public function jstreesubscribers()
	{
		$data 		= $this->maindbaction->getJsTreeSubscriber();
		header('Content-Type: application/json');
		echo json_encode($data);die;
	}

	public function jstreeroominfo()
	{
		$subscriber_id = $this->input->post('subscriber_id');
		$room_id       = $this->input->post('room_id');
		$name_guest    = $this->input->post('name_guest');
		if($name_guest == null OR $name_guest == '')
		{
			$entity 	= $this->maindbaction->getOneBy($subscriber_id);
			$name_guest = $entity['salutation'].' '.$entity['name'].' '.$entity['last_name'];
		}
		
		$billings      = $this->maindbaction->getBillingBySubscriberRoom($subscriber_id,$room_id);
		$room_info     = $this->maindbaction->getRoomBySubscriberIDAndRoomId($subscriber_id,$room_id);
		$mainview      = "subscriber/roominfo";
		$this->load->view($mainview, compact('subscriber_id','room_id','name_guest','billings','room_info'));
	}

	public function updateroominfo()
	{
		$subscriber_id = $this->input->post('subscriber_id');
		$room_id       = $this->input->post('room_id');
		$name_guest    = $this->input->post('name_guest');
		$security_pin  = $this->input->post('security_pin');
		$datatoupdate  = ['subscriber_id'=>$subscriber_id,'room_id'=>$room_id,'name_guest'=>$name_guest,'security_pin'=>$security_pin];
		$this->maindbaction->updateroominfo($datatoupdate);
		redirect($this->agent->referrer());
	}

	public function edit($columnValue,$columnName)
	{
		$this->session->set_userdata(['columnName'=>urldecode($columnName),'columnValue'=>urldecode($columnValue)]);
		$entity 	= $this->maindbaction->getOneBy($columnValue);
		unset($entity['room_id']);
		$room       = $this->maindbaction->getRoomForEdit();
		$package    = $this->maindbaction->getPackage();
		$theme      = $this->maindbaction->getTheme();
		$metadata 	= new SubscriberFormModel($room,$theme,$package);
		$mainview   = "util/editformmodal";
		$this->load->view($mainview, compact('entity','metadata'));
	}
    
	public function delete()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST')
        {
        	$columnName 	= $this->input->post('columnName');
    		$columnValue 	= $this->input->post('columnValue');
            $this->maindbaction->delete($columnName,$columnValue);
            redirect($this->agent->referrer());		
        }
        else
		{
			redirect('errorpage/methodnotallowed');	
		}
	}

	protected function procesToDatabase($typeaction)
	{
		$base_url   = base_url();
		$metadata 	= new SubscriberFormModel();
        $datatosave = [];
        foreach ($metadata as $key => $value) 
        {
			if($this->input->post($key) === '')
			{
				$datatosave[$key] 		= null;
			}
			else
			{
				$datatosave[$key] 		= $this->input->post($key);
			}
			
		}

        if($typeaction == 'create')
        {
			$subscriber_id = $this->maindbaction->insert($datatosave);
			redirect('subscriber/detail/'.$subscriber_id);
        }
        else if ($typeaction == 'update')
        {
			$subscriber_id = (int) $this->maindbaction->update($datatosave);
			redirect('subscriber/detail/'.$subscriber_id);
		}   
	}

}
