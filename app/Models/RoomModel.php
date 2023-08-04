<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 1:29 PM
 */

namespace App\Models;

use App\Libraries\SSP;

class RoomModel extends BaseModel
{
    const VIEW = 'vroom';
    const PK = 'room_id'; //primary key yg di pergunakan pada SSP

    const SQL_GET_BY_SUBSCRIBER = 'SELECT room_id AS id, name AS value FROM troom WHERE subscriber_id=?';
    const SQL_GET_FOR_SELECT = 'SELECT room_id AS id, name AS value FROM troom ORDER BY name';
    const SQL_GET_VACANT_FOR_SELECT = 'SELECT room_id AS id, name AS value FROM troom WHERE status=\'VACANT\' ORDER BY name';
    const SQL_GET_TYPE_FOR_SELECT = 'SELECT `room_type_id` AS id, `type` AS `value` FROM troom_type ORDER BY room_type_id';
    const SQL_GET = "SELECT troom.room_id, troom.name, troom.location, troom.room_type_id, troom_type.type AS `type`, troom.theme_id, ttheme.name AS `theme`, troom.package_id, tpackage.name AS `package`, troom.security_pin, troom.status, troom.subscriber_id, CONCAT(tsubscriber.salutation, tsubscriber.name, tsubscriber.last_name) AS `guest`, troom.create_date, troom.update_date FROM troom LEFT JOIN troom_type ON troom.room_type_id = troom_type.room_type_id LEFT JOIN ttheme ON troom.theme_id = ttheme.theme_id LEFT JOIN tpackage ON troom.package_id = tpackage.package_id LEFT JOIN tsubscriber ON troom.subscriber_id = tsubscriber.subscriber_id";

    const SQL_GET_STB_FOR_SELECT = "SELECT tstb.stb_id AS id, tstb.`name` AS value, (CASE WHEN A.room_id IS NOT NULL THEN 'disabled' ELSE 'enabled' END) AS status_select FROM tstb LEFT JOIN (SELECT troom_stb.room_id, troom_stb.stb_id, troom.name FROM troom_stb INNER JOIN troom ON troom_stb.room_id = troom.room_id) A ON tstb.stb_id = A.stb_id WHERE A.room_id IS NULL ORDER BY tstb.stb_id ASC";
    const SQL_GET_STB_FOR_EDIT   = "SELECT tstb.stb_id AS id, tstb.`name` AS value, (CASE WHEN A.room_id IS NOT NULL THEN 'disabled' ELSE 'enabled' END) AS status_select FROM tstb LEFT JOIN (SELECT troom_stb.room_id, troom_stb.stb_id, troom.name FROM troom_stb INNER JOIN troom ON troom_stb.room_id = troom.room_id) A ON tstb.stb_id = A.stb_id WHERE A.room_id IS NULL OR tstb.stb_id IN (:selectedStbs) ORDER BY tstb.stb_id ASC";
    
    const SQL_MODIFY = 'UPDATE troom SET name=?, location=?, room_type_id=?, theme_id=?, status=?, security_pin=? WHERE (room_id=?)';

    protected $table      = 'troom';
    protected $primaryKey = 'room_id';
    protected $allowedFields = ['name', 'location', 'room_type_id', 'theme_id', 'package_id', 'subscriber_id', 'create_date', 'update_date', 'status', 'security_pin'];

    public function get($id){
        return $this->find($id);
    }

    public function getBySubscriber($subscriberId){
        return $this->where('subscriber_id', $subscriberId)->findAll();
    }

    public function getForSelect(){
        $db = db_connect();
        return $db->query(self::SQL_GET_FOR_SELECT)->getResult('array');
    }

    public function getVacantForSelect(){
        $db = db_connect();
        return $db->query(self::SQL_GET_VACANT_FOR_SELECT)->getResult('array');
    }

    public function getTypeForSelect(){
        $result= $this->db->query(self::SQL_GET_TYPE_FOR_SELECT)->getResult('array');
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }

    public function getStbForSelect(){
        $result = $this->db->query(self::SQL_GET_STB_FOR_SELECT)->getResult('array');
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
        
    }

    /**
     * retrieves STB data for editing based on the selected STBs.
     * 
     * @param selectedStbs The parameter `` is an array that contains the selected STBs
     * It is used to filter the query results and retrieve the STBs for editing.
     * 
     * @return an array of results from the database query. If there are no results, an empty array is
     * returned.
     */
    public function getStbForEdit($selectedStbs = []) {
        $selectedStbs = implode(',', $selectedStbs);
        $query = str_replace(':selectedStbs', $selectedStbs, self::SQL_GET_STB_FOR_EDIT);
        
        $result = $this->db->query($query)->getResult('array');
        if (sizeof($result) > 0) {
            return $result;
        }
        return [];
    }

    // To retrieve stb_ids based on room_id
    public function getStbRoom($roomId)
    {
        $stbIds = $this->db->table('troom_stb')
            ->select('stb_id')
            ->where('room_id', $roomId)
            ->get()
            ->getResultArray();
        
        $stbIds = array_column($stbIds, 'stb_id');

        // Convert stb IDs to integers
        $stbIds = array_map('intval', $stbIds);
    
        return $stbIds;
    }

    public function getFieldList(){
        return ['room_id', 'name', 'location', 'type', 'theme', 'package', 'security_pin', 'status', 'Guest', 'STB', 'create_date', 'update_date'];
    }

    /**
     * recursively sanitizes an array by applying htmlentities to its elements.
     * 
     * @param array The input array that needs to be sanitized.
     * 
     * @return a sanitized array where all the values have been sanitized using htmlentities function.
     */
    private function sanitizeArray($array)
    {
        $sanitizedArray = [];
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                // If the value is an array, recursively sanitize its elements
                $sanitizedArray[$key] = $this->sanitizeArray($val);
            } else {
                // Sanitize the value using htmlentities
                $sanitizedArray[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8');
            }
        }
        return $sanitizedArray;
    }

    public function add($value)
    {
        try {
            $sanitizedValue = [];
            foreach ($value as $key => $val) {
                if (is_array($val)) {
                    $sanitizedValue[$key] = $this->sanitizeArray($val);
                } else {
                    $sanitizedValue[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8');
                }
            }

            // Insert into troom table
            $roomData = [
                'name' => $sanitizedValue['name'],
                'location' => $sanitizedValue['location'],
                'status' => $sanitizedValue['status'],
                'security_pin' => $sanitizedValue['security_pin'],
                'room_type_id' => $sanitizedValue['room_type_id'],
                'theme_id' => $sanitizedValue['theme_id']
            ];
            parent::insert($roomData);

            // Get the inserted room_id
            $roomId = $this->db->insertID();

            $stbIds = [];

            // Check if the 'stb' data is provided and is an array
            if (!empty($sanitizedValue['stb']) && is_array($sanitizedValue['stb'])) {

                // Iterate through each selected STB and cast to integer
                foreach ($sanitizedValue['stb'] as $stb) {
                    $stbIds[] = (int) $stb;
                }

            }

            // Insert into troom_stb table for each stb_id
            foreach ($stbIds as $stbId) {
                $roomStbData = [
                    'room_id' => $roomId,
                    'stb_id' => $stbId
                ];
                $this->db->table('troom_stb')->insert($roomStbData);
            }
        } catch (\Exception $e) {
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return 0;
        }

        return $this->db->affectedRows();
    }

    /**
     * PHP sanitizes and updates the data
     * 
     * @param roomId The roomId parameter is the ID of the room that needs to be modified.
     * @param value The "value" parameter is an array that contains the data to be modified for a specific room.
     * 
     * @return the number of affected rows in the database after the update and insert operations.
     */
    public function modify($roomId, $value)
    {
        try {
            $sanitizedValue = [];
            foreach ($value as $key => $val) {
                if (is_array($val)) {
                    $sanitizedValue[$key] = $this->sanitizeArray($val);
                } else {
                    $sanitizedValue[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8');
                }
            }

            // Update troom table
            $roomData = [
                'name' => $sanitizedValue['name'],
                'location' => $sanitizedValue['location'],
                'status' => $sanitizedValue['status'],
                'security_pin' => $sanitizedValue['security_pin'],
                'room_type_id' => $sanitizedValue['room_type_id'],
                'theme_id' => $sanitizedValue['theme_id']
            ];
            parent::update($roomId, $roomData);

            $stbIds = [];

            // Check if the 'stb' data is provided and is an array
            if (!empty($sanitizedValue['stb']) && is_array($sanitizedValue['stb'])) {
                
                // Delete existing STB associations for the room
                $this->db->table('troom_stb')->where('room_id', $roomId)->delete();

                // Iterate through each selected STB and cast to integer
                foreach ($sanitizedValue['stb'] as $stb) {
                    $stbIds[] = (int) $stb;
                }
            }

            // Insert into troom_stb table for each stb_id
            foreach ($stbIds as $stbId) {
                $roomGenreData = [
                    'room_id' => $roomId,
                    'stb_id' => $stbId
                ];
                $this->db->table('troom_stb')->insert($roomGenreData);
            }
        } catch (\Exception $e) {
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return 0;
        }

        return $this->db->affectedRows();
    }

    public function remove($roomId){
        try {
            // Delete from troom_stb table
            $this->db->table('troom_stb')->where('room_id', $roomId)->delete();
    
            // Delete from troom table
            $r = $this->where('room_id', $roomId)->delete();
    
            return $this->db->affectedRows();
        } catch (\Exception $e) {
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return 0;
        }
    }

    /**
     * di pakai utk datatables.js
     *
     * @return mixed
     */
    public function getSsp()
    {
        return $this->_getSsp(self::VIEW, self::PK, $this->getFieldList());
    }
}