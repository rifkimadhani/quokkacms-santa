<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

namespace App\Models;
use CodeIgniter\Model;

class CountingModel extends Model
{
    public $table = 'tstb';
    const STB_COUNTING       = "SELECT SUM(CASE WHEN `status` = 1 THEN 1 ELSE 0 END)online,SUM(CASE WHEN `status` = 0 THEN 1 ELSE 0 END)offline FROM tstb";
    
    const ROOM_COUNTING      = "SELECT SUM(CASE WHEN `status` = 'OCCUPIED' THEN 1 ELSE 0 END)occupied,SUM(CASE WHEN `status` = 'VACANT' THEN 1 ELSE 0 END)vacant FROM troom";
    const GUEST_COUNTING     = "SELECT SUM(CASE WHEN DATE(checkin_date) = CURDATE() THEN 1 ELSE 0 END)checkin,SUM(CASE WHEN DATE(checkout_date) = CURDATE() THEN 1 ELSE 0 END)checkout FROM tsubscriber";
    const EMERGENCY_COUNTING = "SELECT SUM(CASE WHEN DATE(active_date) = CURDATE() THEN 1 ELSE 0 END)emergency_count FROM temergency_history";
    
    public function getStbCounting()
    {
        $db = db_connect();
        $result = $db->query(CountingModel::STB_COUNTING)->getResultArray();

       if($result AND count($result) > 0)
        {
            return $result[0]; 
        }
        return ['online'=>0,'offline'=>0];
    }
//    public function getStbCounting()
//    {
//       $result = $this->db->query(CountingModel::STB_COUNTING)->result_array();
//       if($result AND count($result) > 0)
//        {
//            return $result[0];
//        }
//        return ['online'=>0,'offline'=>0];
//    }

    public function getRoomCounting()
    {
//       $result = $this->db->query(CountingModel::ROOM_COUNTING)->result_array();
        $db = db_connect();
        $result = $db->query(CountingModel::ROOM_COUNTING)->getResultArray();

        if($result AND count($result) > 0)
        {
            return $result[0]; 
        }
        return ['occupied'=>0,'vacant'=>0];
    }

    public function getGuestCounting()
    {
        $db = db_connect();
        $result = $db->query(CountingModel::GUEST_COUNTING)->getResultArray();

//        $result = $this->db->query(CountingModel::GUEST_COUNTING)->result_array();
       if($result AND count($result) > 0)
        {
            return $result[0]; 
        }
        return ['checkin'=>0,'checkout'=>0];
    }

    public function getEmergencyCounting()
    {
        $db = db_connect();
        $result = $db->query(CountingModel::EMERGENCY_COUNTING)->getResultArray();

//        $result = $this->db->query(CountingModel::EMERGENCY_COUNTING)->result_array();
       if($result AND count($result) > 0)
        {
            return $result[0]; 
        }
        return ['emergency_count'=>0];
    }
    
}