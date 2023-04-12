<?php
/**
 * Created by PageBuilder
 * Date: 2023-04-06 09:47:23
 */
namespace App\Models;

use App\Libraries\SSP;

class Livetv_EPGModel extends BaseModel
{
    const VIEW = 'vlivetv_epg';

    // const SQL_GET = 'SELECT * FROM tlivetv_epg WHERE (epg_id=?)';
    const SQL_GET = 'SELECT `tlivetv_epg`.`epg_id` AS `epg_id`,`tlivetv_epg`.`livetv_id` AS `livetv_id`,`tlivetv_epg`.`start_date` AS `start_date`,`tlivetv_epg`.`end_date` AS `end_date`,`tlivetv_epg`.`duration` AS `duration`,`tlivetv_epg`.`name` AS `name`,`tlivetv_epg`.`sinopsis` AS `sinopsis`,`tlivetv_epg`.`create_date` AS `create_date`,`tlivetv_epg`.`update_date` AS `update_date`,`tlivetv`.`name` AS `channel`,`tlivetv`.`url_station_logo` AS `url_station_logo` FROM (`tlivetv` JOIN `tlivetv_epg` ON (`tlivetv`.`livetv_id` = `tlivetv_epg`.`livetv_id`))';
    const SQL_MODIFY = 'UPDATE tlivetv_epg SET livetv_id=?, start_date=?, end_date=?, name=?, sinopsis=? WHERE (epg_id=?)';
    const SQL_GET_CHANNEL_FOR_SELECT = 'SELECT livetv_id as id, `name` as value FROM tlivetv ORDER BY livetv_id';

    protected $table      = 'tlivetv_epg';
    protected $primaryKey = 'epg_id';
    protected $allowedFields = ['livetv_id', 'start_date', 'end_date', 'duration', 'name', 'sinopsis', 'create_date', 'update_date'];

    public $errCode;
    public $errMessage;


    public function get($epgId)
    {
        $r = $this
            ->where('epg_id', $epgId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll($livetvId, $startDate = null, $endDate = null){
        // $db = db_connect();
        // return $db->query(self::SQL_GET)->getResult();
        $db = db_connect();
        $builder = $db->table(self::VIEW);

        $livetvId = $builder->where('channel =', $livetvId);

        if ($startDate != null) {
            $builder->where('start_date >=', $startDate);
        }

        if ($endDate != null) {
            $builder->where('end_date <=', $endDate);
        }

        return $builder->get()->getResult();
    }

    public function getFieldList(){
        return ['epg_id', 'livetv_id', 'channel', 'start_date', 'end_date', 'duration', 'name', 'sinopsis', 'create_date', 'update_date', 'url_station_logo'];
    }

    public function getChannelForSelect(){
        $result = $this->db->query(self::SQL_GET_CHANNEL_FOR_SELECT)->getResult('array');
        if(sizeof($result) > 0)
        {
            return $result;
        }
        return [];
    }

    public function add($value)  {

        try
        {
            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            $value['sinopsis'] = htmlentities($value['sinopsis'], ENT_QUOTES, 'UTF-8');

            parent::insert($value);

        }
        catch (\Exception $e){
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();

            return 0;
        }

        return $this->db->affectedRows();
    }

    /**
     * update dgn cara PDO, karena dgn cara ci4 tdk ada rowCount, shg tdk tahu apakah update berhasil atau tdk
     *
     * @param $id
     * @param $name
     * @param $status
     * @return \PDOException|\Exception|int => 0/1 = count update, -1 = pdo exception
     */
    public function modify($value){

        $this->errCode = '';
        $this->errMessage = '';

        $epgId = $value['epg_id'];

        $livetvId = $value['livetv_id'];
        $startDate = $value['start_date'];
        $endDate = $value['end_date'];
        // $duration = $value['duration'];
        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        $sinopsis = htmlentities($value['sinopsis'], ENT_QUOTES, 'UTF-8');

        if (strlen($startDate)==0) $startDate = null;
        if (strlen($endDate)==0) $endDate = null;

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$livetvId, $startDate, $endDate, $name, $sinopsis, $epgId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }


    public function remove($epgId){
        $r = $this
            ->where('epg_id', $epgId)
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
        return $this->_getSsp(self::VIEW, $this->primaryKey, $this->getFieldList());
    }


    /**
     * This function generates an XML file for an EPG.
     * 
     * @param $data (start_date and end_dates, livetv_id, name, and synopsis.)
     * @param $offset
     * The function loops through each row of data and generates an XML output in the EPG format.
     * 
     * @return an EPG XML generated from the datatable.
     */
    public function generateEpgXml($data, $offset) {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><!DOCTYPE tv SYSTEM "xmltv.dtd"><tv generator-info-name="madeiraentertainz.com"></tv>');
        
        $existChannels = array();
    
        // Loop through each row of data and generate the XML
        foreach ($data as $row) {
            $channelId = $row->livetv_id;
            if (!in_array($channelId, $existChannels)) {
                // Channel has not been written before, so write channel information
                $channel = $xml-> addChild('channel');
                $channel->addAttribute('id', $channelId);
    
                $display = $channel->addChild('display-name', $row->channel);
                // $icon    = $channel->addChild('icon');
                // $icon->addAttribute('src', $row->url_station_logo);
    
                // Add channel to existChannels array
                $existChannels[] = $channelId;
            }

            $programme = $xml-> addChild('programme');
            $programme -> addAttribute('channel', $row->channel);

            // Apply offset
            $startDateTime = new \DateTime($row->start_date);
            $startDateTime->modify($offset . ' hours');
            $start = $startDateTime->format('YmdHis');

            $endDateTime = new \DateTime($row->end_date);
            $endDateTime->modify($offset . ' hours');
            $end = $endDateTime->format('YmdHis');

            $programme -> addAttribute('start', $start);
            $programme -> addAttribute('stop', $end);
    
            $title  = $programme->addChild('title', $row->name);
            $title  -> addAttribute('lang', 'eng');
            $sub   = $programme->addChild('sub-title', $row->sinopsis);
            $sub   -> addAttribute('lang', 'eng');
        }
    
        // Format the XML output
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        $epgXml = $dom->saveXML();
    
        return $epgXml;
    }
    
}
