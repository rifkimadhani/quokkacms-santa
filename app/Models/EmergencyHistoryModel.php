<?php

namespace App\Models;

class EmergencyHistoryModel extends BaseModel
{
    const SQL_GET = 'SELECT emergency_history_id AS ID, emergency_code, active_date, inactive_date, create_date, update_date FROM temergency_history';
    const SQL_GET_EMERGENCY_ACTIVE = 'SELECT * FROM temergency_history WHERE inactive_date IS NULL ORDER BY emergency_history_id DESC LIMIT 1';
    const SQL_GET_EMERGENCYCODE ='SELECT * FROM temergency_history WHERE emergency_code = ?';
    const SQL_GET_EMERGENCYCATEGORY ='SELECT emergency_code AS id ,name AS value FROM temergency';

    protected $table      = 'temergency_history';
    protected $primaryKey = 'emergency_history_id';
    protected $allowedFields = ['emergency_code', 'active_date', 'inactive_date', 'create_date', 'update_date'];

    public $errCode;
    public $errMessage;

    public function get($emergencyHistoryId)
    {
        $r = $this
            ->where('emergency_history_id', $emergencyHistoryId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['emergency_history_id', 'emergency_code', 'active_date', 'inactive_date', 'create_date', 'update_date'];
    }

    public function getOneEmergencyActive()
    {
        $result = $this->db->query(self::SQL_GET_EMERGENCY_ACTIVE)->getRow(); //retrieve a single row as an object
        if ($result !== null) {
            return $result;
        }
    
        return (object) [
            'emergency_code' => 'GENERIC',
            'emergency_history_id' => 0
        ];
    }

    public function getOneByEmergencyCode($versioncode)
	{
        $result = $this->db->query(self::SQL_GET_EMERGENCYCODE, array($versioncode))->getResult();
        if($result && count($result) > 0)
        {
            return $result[0];
        }

        return false;
    }


    public function getEmergencyCategory()
    {
		$result     = $this->db->query(self::SQL_GET_EMERGENCYCATEGORY)->getResult();

    	return $result;
    }

    public function setEmergencyActive($emergency_code)
    {
        $datatosave = [
            'emergency_code' => $emergency_code,
            'active_date' => date('Y-m-d H:i:s'),
            'create_date' => date('Y-m-d H:i:s')
        ];
    
        $this->db->db_debug = true;
        $statusinsert = $this->db->table($this->table)->insert($datatosave);
        if ($statusinsert)
        {
            $this->updateEmergency(1, $emergency_code);
        }
        $this->db->db_debug = true;
        // $this->notificationmodel->sendEmergencyWarningToAll(1);
    }

    public function setEmergencyNonActive($emergency_history_id)
    {
        $datatoupdate = [
            'inactive_date' => date('Y-m-d H:i:s'),
            'update_date' => date('Y-m-d H:i:s')
        ];

        $this->db->db_debug = true;
        $status= $this->builder()->where('emergency_history_id',  $emergency_history_id)->update($datatoupdate);

        if ($status)
        {
            $this->updateEmergency(0, '');
        }
        // $this->notificationmodel->sendEmergencyWarningToAll(0);
    }

    public function updateEmergency($value_int,$value_string)
    {
        $this->db->db_debug = true;
        $status= $this->db->table('tsetting')->where('setting_id', 2)->update(['update_date' => date('Y-m-d H:i:s'), 'value_int' => $value_int, 'value_string' => $value_string]);
    }

    public function remove($emergencyHistoryId){
        $r = $this
            ->where('emergency_history_id', $emergencyHistoryId)
            ->delete();

        return $this->db->affectedRows();
    }

    /**
     * di pakai utk datatables.js
     *
     * @return mixed
     */
    public function getSsp()
    {
        return $this->_getSsp($this->table, $this->primaryKey, $this->getFieldList());
    }
}
