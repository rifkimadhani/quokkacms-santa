<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

namespace App\Models;
use CodeIgniter\Model;

class SubscriberSspModel extends Model
{
    protected $table      = 'tsubscriber';
    protected $primaryKey = 'subscriber_id';

//    private $tableview;
//    private $primaryKey;
    private $columns;
    private $sql_details;
    
    public function getAll()
    {
//        $this->tableview 	= 'tsubscriber';
//		$this->primaryKey   = 'subscriber_id';

		$field_list         = $this->db->list_fields($this->tableview);
        foreach($field_list as $key => $value)
        {
            $columns[] = ['db' =>$value,'dt'=>$key];
        }

		$this->columns      = $columns;
        $this->sql_details = $this->maindbaction->getFullConnection();
        return $this->datatables_ssp->simple( $_GET,$this->sql_details,$this->tableview,$this->primaryKey,$this->columns);
    }
}
