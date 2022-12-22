<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

namespace App\Models;
use CodeIgniter\Model;

class SubscriberModel extends Model
{
    const SQL_GET_ALL = "SELECT (tsubscriber.subscriber_id)'Subscriber ID',CONCAT(IFNULL(tsubscriber.salutation,''),' ',tsubscriber.name,' ',IFNULL(tsubscriber.last_name,''))'Full Name',(ttheme.name)Theme,(tpackage.name)Package,GROUP_CONCAT(troom.name)Room,(tsubscriber.status)'Status',(tsubscriber.checkin_date)'Checkin Date',(tsubscriber.checkout_date)'Checkout Date',(tsubscriber.create_date)'Create Date',(tsubscriber.update_date)'Update Date' ".
    " FROM tsubscriber LEFT JOIN ttheme ON tsubscriber.theme_id = ttheme.theme_id".
    " LEFT JOIN tpackage ON tsubscriber.package_id = tpackage.package_id".
    " RIGHT JOIN troom ON tsubscriber.subscriber_id = troom.subscriber_id  WHERE troom.subscriber_id IS NOT NULL GROUP BY troom.subscriber_id";

    const SQL_GET_HISTORY = "SELECT (A.subscriber_id)'Subscriber ID',CONCAT(IFNULL(A.salutation,''),' ',A.name,' ',IFNULL(A.last_name,''))'Full Name',A.Theme,A.Package,(B.Room)Room,(A.status)'Status',(A.checkin_date)'Checkin Date',(A.checkout_date)'Checkout Date',(A.create_date)'Create Date',(A.update_date)'Update Date' FROM
    (
        SELECT tsubscriber.*,(ttheme.name)Theme,(tpackage.name)Package
        FROM tsubscriber LEFT JOIN ttheme ON tsubscriber.theme_id = ttheme.theme_id
        LEFT JOIN tpackage ON tsubscriber.package_id = tpackage.package_id WHERE tsubscriber.status = 'CHECKOUT'
    )A
    LEFT JOIN
    (
        SELECT tsubscriber_room.subscriber_id,GROUP_CONCAT(troom.name)Room FROM tsubscriber_room 
        LEFT JOIN troom ON tsubscriber_room.room_id = troom.room_id GROUP BY tsubscriber_room.subscriber_id
    )B ON A.subscriber_id = B.subscriber_id";

    protected $db;
    protected $table = 'tsubscriber';
    protected $primaryKey = 'subscriber_id';

    public function __construct()
	{
//        parent::__construct();
//        $this->load->model('BillingModel', 'bilingmodel');
//        $this->load->model('NotificationModel', 'notificationmodel');

        $this->db = db_connect();
    }

    public function getFieldList()
    {
        return $this->db->getFieldNames($this->table);
    }
    
    public function getJoinAll()
    {
        return  $this->db->query(SubscriberModel::SQL_GET_ALL)->getResult();
    }

    public function getHistory()
    {
        return  $this->db->query(SubscriberModel::SQL_GET_HISTORY,array())->result();
    }

    public function findOneBy($namaColumn,$valueColumn)
    {
        $result = $this->db->get_where($this->table, array($namaColumn => $valueColumn), 1, 0)->result_array();
        if($result AND count($result) > 0)
        {
            return $result[0]; 
        }
        return false;
    }

    public function getOneBy($subscriber_id)
    {
        $query 		= "SELECT tsubscriber.*,tsubscriber_room.room_id FROM tsubscriber LEFT JOIN tsubscriber_room ON tsubscriber.subscriber_id = tsubscriber_room.subscriber_id WHERE tsubscriber.subscriber_id = ?";
        $result     = $this->db->query($query,array($subscriber_id))->getResult();
        if($result AND count($result) > 0)
        {
            $querystb 		= "SELECT tsubscriber_room.room_id FROM tsubscriber_room WHERE tsubscriber_room.subscriber_id = ?";
            $resultstb      = $this->db->query($querystb,array($subscriber_id))->getResult();
            $room_id = [];
            if($resultstb AND count($resultstb) > 0)
            {
                foreach($resultstb as $value)
                {
                    $room_id[] = $value['room_id'];
                } 
            }
            $result[0]['room_id'] = $room_id;
            return $result[0];
        }
        return false;
    }
    
    public function insertComplex(array $datatosave)
    {
        $room_id                   = $datatosave['room_id'];
        unset($datatosave['room_id'],$datatosave['country'],$datatosave['age_bracket'],$datatosave['diet']);
        $datatosave['checkin_date'] = date('Y-m-d H:i:s');
        $datatosave['create_date']  = date('Y-m-d H:i:s');
        $datatosave['status']       = 'CHECKIN';
        $this->db->db_debug = true;
        $statusinsert = $this->db->insert($this->table, $datatosave);
        $this->db->db_debug = true;
        if ($statusinsert) 
        {
            $subscriber_id = $this->db->insert_id();
            if($room_id)
            {
                foreach($room_id as $room_id)
                {
                    $datatosaveroomsubsriber = ['room_id'=>$room_id,'subscriber_id'=>$subscriber_id,'name_guest'=>null,'create_date'=>date('Y-m-d H:i:s')];
                    $this->db->insert('tsubscriber_room', $datatosaveroomsubsriber);

                    $datatoupdateroom = [];
                    $datatoupdateroom['subscriber_id'] = $subscriber_id;
                    $datatoupdateroom['theme_id']      = $datatosave['theme_id'];
                    $datatoupdateroom['package_id']    = $datatosave['package_id'];
                    $datatoupdateroom['status']        = 'OCCUPIED';    
                    $this->db->where('room_id', $room_id);
                    $this->db->update('troom',$datatoupdateroom);
                }
            }
            $msg =  "Data Guest Dengan Nama <b>{$datatosave['name']}</b> Berhasil Disimpan.";
            $this->session->set_flashdata('success_msg',$msg);
            $this->notificationmodel->sendCheckinToSubscriber($subscriber_id);
            return $subscriber_id;
        }
        else
        {
            $msg =  "Data Guest Dengan Nama <b>{$datatosave['name']}</b> Gagal Disimpan.Database Processing Failed";
            $this->session->set_flashdata('error_msg',$msg);
            return false;  
        }
    }

    public function updateComplex($datatoupdate)
    {
        $room_id = $datatoupdate['room_id'];
        unset($datatoupdate['room_id'],$datatoupdate['country'],$datatoupdate['age_bracket'],$datatoupdate['diet']);
        $datatoupdate['update_date'] = date('Y-m-d H:i:s');
        $this->db->db_debug = true;    
        $columnName  = $this->session->userdata('columnName');
        $columnValue = $this->session->userdata('columnValue');     
        $this->db->where($columnName, $columnValue);
        $status = $this->db->update($this->table,$datatoupdate);
        $this->db->db_debug = true;
        if($status)
        {
            // $this->db->delete('tsubscriber_room',array('subscriber_id'=>$columnValue));
            // if($room_id)
            // {
            //     foreach($room_id as $room_id)
            //     {
            //         $datatosaveroomsubsriber = ['room_id'=>$room_id,'subscriber_id'=>$columnValue,'name_guest'=>$datatosave['name'],'create_date'=>date('Y-m-d H:i:s')];
            //         $this->db->insert('tsubscriber_room', $datatosaveroomsubsriber);
            //     }
            // }
            
            $msg =  "Data Guest Dengan Nama <b>{$datatoupdate['name']}</b> Berhasil Diubah.";
            $this->session->set_flashdata('success_msg',$msg);
            $subscriber_entity = $this->session->userdata('subscriber_entity');
            if($subscriber_entity['theme_id'] != $datatoupdate['theme_id'])
            {
                $this->notificationmodel->sendThemeUpdateToSubscriber($subscriber_entity['subscriber_id']);
            }
            if($subscriber_entity['package_id'] != $datatoupdate['package_id'])
            {
                $this->notificationmodel->sendLivetvUpdateToSubscriber($subscriber_entity['subscriber_id']);
            }
            return $columnValue;   
        }
        else
        {
            $msg =  "Data Guest Dengan Nama <b>{$datatoupdate['name']}</b> Gagal Diubah.Database Processing Failed";
            $this->session->set_flashdata('error_msg',$msg);
        }
    }

    public function updateroominfo($datatoupdate)
    {
        $security_pin = $datatoupdate['security_pin'];
        unset($datatoupdate['security_pin']);
        $datatoupdate['update_date'] = date('Y-m-d H:i:s');
        $this->db->db_debug = true;    
        $this->db->where('subscriber_id', $datatoupdate['subscriber_id']);
        $this->db->where('room_id', $datatoupdate['room_id']);
        $status = $this->db->update('tsubscriber_room',$datatoupdate);
        $this->db->db_debug = true;
        if($status)
        {
            $this->db->where('room_id', $datatoupdate['room_id']);
            $this->db->update('troom',['security_pin'=>$security_pin]);

            $msg =  "Data Guest Berhasil Diubah.";
            $this->session->set_flashdata('success_msg',$msg);
            return $columnValue;   
        }
        else
        {
            $msg =  "Data Guest Gagal Diubah.Database Processing Failed";
            $this->session->set_flashdata('error_msg',$msg);
        }
    }

    public function checkoutsubscriber($subscriber_id)
    {
        $datatoupdatesubscriber                  = [];
        $datatoupdatesubscriber['update_date']   = date('Y-m-d H:i:s');
        $datatoupdatesubscriber['checkout_date'] = date('Y-m-d H:i:s');
        $datatoupdatesubscriber['status']        = 'CHECKOUT';

        $this->db->db_debug = true;    
        $this->db->where('subscriber_id', $subscriber_id);
        $status = $this->db->update('tsubscriber',$datatoupdatesubscriber);
        $this->db->db_debug = true;
        if($status)
        {
            $datatoupdateroom = [];
            $datatoupdateroom['update_date']   = date('Y-m-d H:i:s');
            $datatoupdateroom['subscriber_id'] = null;
            $datatoupdateroom['theme_id']      = null;
            $datatoupdateroom['package_id']    = null;
            $datatoupdateroom['status']        = 'VACANT';    
            $this->db->where('subscriber_id', $subscriber_id);
            $this->db->update('troom',$datatoupdateroom);
            $msg =  "Guest Berhasil Checkout.";
            $this->session->set_flashdata('success_msg',$msg);
            $this->notificationmodel->sendCheckoutToSubscriber($datatoupdateroom['subscriber_id']);
            return $columnValue;   
        }
        else
        {
            $msg =  "Guest Gagal Checkout.Database Processing Failed";
            $this->session->set_flashdata('error_msg',$msg);
        }
    }

    public function checkoutsubscriberbyroom($subscriber_id,$room_id)
    {
        $datatoupdate = [];
        $datatoupdate['update_date']   = date('Y-m-d H:i:s');
        $datatoupdate['subscriber_id'] = null;
        $datatoupdate['theme_id']      = null;
        $datatoupdate['package_id']    = null;
        $datatoupdate['status']        = 'VACANT';
        $this->db->db_debug = true;    
        $this->db->where('room_id', $room_id);
        $status = $this->db->update('troom',$datatoupdate);
        $this->db->db_debug = true;
        if($status)
        {
            
            $this->db->where('subscriber_id',$subscriber_id);
            $this->db->from("troom");
            $countresult = $this->db->count_all_results();
            if($countresult == 0)
            {
                $datatoupdatesubscriber                  = [];
                $datatoupdatesubscriber['update_date']   = date('Y-m-d H:i:s');
                $datatoupdatesubscriber['checkout_date'] = date('Y-m-d H:i:s');
                $datatoupdatesubscriber['status']        = 'CHECKOUT';

                $this->db->db_debug = true;    
                $this->db->where('subscriber_id', $subscriber_id);
                $status = $this->db->update('tsubscriber',$datatoupdatesubscriber);
            }
            $msg =  "Guest Berhasil Checkout.";
            $this->session->set_flashdata('success_msg',$msg);
            $this->notificationmodel->sendCheckoutToSubscriber($subscriber_id);
            return $countresult;
        }
        else
        {
            $msg =  "Guest Gagal Checkout.Database Processing Failed";
            $this->session->set_flashdata('error_msg',$msg);
        }
    }

    public function deleteComplex($columnName,$columnValue)
    {
        $isExist = $this->findOneBy($columnName,$columnValue);
        if($isExist)
        {
            $this->db->delete('tsubscriber_room',array('subscriber_id' => $columnValue));
            $result = $this->db->delete($this->table,array($columnName => $columnValue));
            if($result)
            {
                $msg =  "Data Subscriber Success Berhasil Dihapus.";
                $this->session->set_flashdata('success_msg',$msg);
            }
            else
            {
                $msg =  "Data Subscriber Gagal Dihapus.Database Processing Failed";
                $this->session->set_flashdata('error_msg',$msg);
            }       
        }
        else
        {
            $msg =  'Data Data Subscriber Yang Akan Dihapus Tidak Ditemukan.';
            $this->session->set_flashdata('error_msg',$msg);
        } 
    }

    public function getRoom()
    {
        $query 		= "SELECT troom.room_id AS id ,troom.name AS value FROM troom WHERE troom.subscriber_id IS NULL";
		$result     = $this->db->query($query)->getResult();
    	if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }

    public function getRoomBySubscriberID($subscriber_id)
    {
        $query 		= "SELECT troom.room_id AS room_id ,troom.name AS room_name,(CASE WHEN troom.subscriber_id IS NOT NULL THEN 'checkin' ELSE 'checkout' END)status_checkin,tsubscriber_room.subscriber_id,tsubscriber_room.name_guest FROM troom".
                     " LEFT JOIN tsubscriber_room ON troom.room_id = tsubscriber_room.room_id WHERE tsubscriber_room.subscriber_id=?";
		$result     = $this->db->query($query,array('subscriber_id'=>$subscriber_id))->getResult();
    	if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }

    public function getRoomBySubscriberIDAndRoomId($subscriber_id,$room_id)
    {
        $query 		= "SELECT troom.room_id AS room_id ,troom.name AS room_name,(CASE WHEN troom.subscriber_id IS NOT NULL THEN 'checkin' ELSE 'checkout' END)status_checkin,tsubscriber_room.subscriber_id,tsubscriber_room.name_guest,troom.security_pin FROM troom".
                     " LEFT JOIN tsubscriber_room ON troom.room_id = tsubscriber_room.room_id WHERE tsubscriber_room.subscriber_id=? AND tsubscriber_room.room_id=?";
		$result     = $this->db->query($query,array($subscriber_id,$room_id))->getResult();
    	if(sizeof($result) > 0)
        {
            return $result[0];
        }
        return [];
    }

    public function getFullRoomInformation($subscriber_id)
    {

    }

    public function getRoomForEdit()
    {
        $query 		= "SELECT troom.room_id AS id ,troom.name AS value,(CASE WHEN tsubscriber_room.subscriber_id IS NOT NULL THEN 'disabled' ELSE 'enabled' END)AS status_select FROM troom LEFT JOIN tsubscriber_room ON troom.room_id = tsubscriber_room.room_id";
        $result     = $this->db->query($query)->getResult();
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    	
    }

    public function getPackage()
    {
        $query 		= "SELECT package_id AS id ,name AS value FROM tpackage";
		$result     = $this->db->query($query)->getResult();
    	return $result;
    }

    public function getTheme()
    {
        $query 		= "SELECT theme_id AS id ,name AS value FROM ttheme";
		$result     = $this->db->query($query)->getResult();
    	return $result;
    }

    public function getJsTreeSubscriber()
    {
        $subscriber_id  = $this->session->userdata('subscriber_id');
        $datasubscriber = $this->getOneBy($subscriber_id);
        $rootmenu[]     = ['id'=>0,'parent'=>'#','text'=>'GUEST INFORMATION','data'=>$datasubscriber];
        $datarooms      = $this->getRoomBySubscriberID($subscriber_id);
        if(sizeof($datarooms) > 0)
        {
            foreach($datarooms as $dataroom)
            {
                $dataroom['subscriber_id'] = $subscriber_id;
                $rootmenu[] = ['id'=>$dataroom['room_id'],'parent'=>0,'text'=>$dataroom['room_name'],'data_room'=>$dataroom];
            }
        }
        return  $rootmenu;
    }

    public function getBillingBySubscriberRoom($subscriber_id,$room_id)
    {
        $biling  = $this->bilingmodel->roomServiceBillingBySubscriberAndRoom($subscriber_id,$room_id);
        $karaoke = $this->bilingmodel->karaokeBillingBySubscriberAndRoom($subscriber_id,$room_id);
        $vod     = $this->bilingmodel->vodBillingBySubscriberAndRoom($subscriber_id,$room_id);
        $livetv  = $this->bilingmodel->liveTVBillingBySubscriberAndRoom($subscriber_id,$room_id);
        return array_merge($biling,$karaoke,$vod,$livetv);
    }

    public function getBilling($subscriber_id)
    {
        $dataroom = $this->getRoomBySubscriberID($subscriber_id);
        $result   = [];
        foreach($dataroom as $room)
        {
            $room['room_service'] = $this->getBillingBySubscriberRoom($subscriber_id,$room['room_id']);
            $result[] = $room; 
        }
        return $result;
    }

    public function getBillingSummary($subscriber_id)
    {
        $dataroom = $this->getRoomBySubscriberID($subscriber_id);
        $result   = [];
        foreach($dataroom as $room)
        {
            $room_service = $this->bilingmodel->roomServiceBillingGrouping($subscriber_id,$room['room_id']);
            $karaoke      = $this->bilingmodel->karaokeBillingGrouping($subscriber_id,$room['room_id']);
            $vod          = $this->bilingmodel->vodBillingGrouping($subscriber_id,$room['room_id']);
            $livetv       = $this->bilingmodel->liveTVBillingGrouping($subscriber_id,$room['room_id']);
            $billings     = ['room_service'=>$room_service,'karaoke'=>$karaoke,'vod'=>$vod,'livetv'=>$livetv];
            $room['billings'] = $billings;
            $result[] = $room; 
        }
        return $result;
    }

    
}