<?php
/**
 * Created by PageBuilder
 * Date: 2023-06-06 09:43:25
 */
namespace App\Models;


use DateTime;

class STBDevicesModel extends BaseModel
{
    const SQL_GET     = 'SELECT * FROM tstb WHERE (stb_id=?)';
    // const SQL_GET_ALL = <<<'SQL'
    //                         SELECT
    //                             tstb.stb_id, tstb.name, tstb.ip_address,
    //                             tstb.mac_address,
    //                             tstb.location, 
    //                             IF (TIMESTAMPDIFF(SECOND,tstb.last_seen,NOW())<15,1,0) AS Status,
    //                             tstb.last_seen,
    //                             tstb.app_id,
    //                             tstb.version_code,
    //                             tstb.version_name,
    //                             tstb.android_version,
    //                             tstb.android_api,
    //                             tstb.create_date,
    //                             tstb.update_date 
    //                         FROM
    //                             tstb
    //                     SQL;

    const SQL_MODIFY                      = 'UPDATE tstb SET name=?, location=?, ip_address=? WHERE (stb_id=?)';
    const SQL_GET_STB_FOR_SELECT          = 'SELECT name as id,name as value,app_id as data FROM tstb';
    const SQL_GET_ONELASTVERSION_BY_APPID = 'SELECT tapp.* FROM tapp WHERE app_id = ? ORDER BY version_code DESC LIMIT 1';
    const SQL_GET_ONELASTVERSION          = 'SELECT tapp.* FROM tapp ORDER BY version_code DESC LIMIT 1';

    const SQL_GET_IP_ALL            = 'SELECT troom.room_id,troom.subscriber_id,STB.stb_id,STB.ip_address FROM troom LEFT JOIN (SELECT troom_stb.room_id,tstb.stb_id,tstb.ip_address FROM tstb INNER JOIN troom_stb ON tstb.stb_id = troom_stb.stb_id)STB ON troom.room_id = STB.room_id WHERE STB.ip_address IS NOT NULL GROUP BY STB.ip_address ORDER BY troom.room_id ASC';
    const SQL_GET_ACTIVE_IP         = 'SELECT ip_address FROM tstb WHERE LENGTH(TRIM(ip_address))>0 AND TIMESTAMPDIFF(SECOND,last_seen,NOW())<15';
    const SQL_GET_IP_BY_ROOM        = 'SELECT troom.room_id,troom.subscriber_id,STB.stb_id,STB.ip_address FROM troom LEFT JOIN (SELECT troom_stb.room_id,tstb.stb_id,tstb.ip_address, tstb.last_seen FROM tstb INNER JOIN troom_stb ON tstb.stb_id = troom_stb.stb_id)STB ON troom.room_id = STB.room_id WHERE troom.room_id = ? AND TIMESTAMPDIFF(SECOND,STB.last_seen,NOW())<15 ORDER BY troom.room_id ASC';
    const SQL_GET_IP_BY_SUBSCRIBER  = 'SELECT troom.room_id,troom.subscriber_id,STB.stb_id,STB.ip_address FROM troom LEFT JOIN (SELECT troom_stb.room_id,tstb.stb_id,tstb.ip_address, tstb.last_seen FROM tstb INNER JOIN troom_stb ON tstb.stb_id = troom_stb.stb_id)STB ON troom.room_id = STB.room_id WHERE troom.subscriber_id = ? AND TIMESTAMPDIFF(SECOND,STB.last_seen,NOW())<15 ORDER BY troom.room_id ASC';
    const SQL_GET_IP_BY_STBID       = 'SELECT troom.room_id,troom.subscriber_id,STB.stb_id,STB.ip_address FROM troom LEFT JOIN (SELECT troom_stb.room_id,tstb.stb_id,tstb.ip_address, tstb.last_seen FROM tstb INNER JOIN troom_stb ON tstb.stb_id = troom_stb.stb_id)STB ON troom.room_id = STB.room_id WHERE STB.stb_id = ? AND TIMESTAMPDIFF(SECOND,STB.last_seen,NOW())<15 ORDER BY troom.room_id ASC';

    const SQL_GET_ONE_BY_STBID   = 'SELECT tstb.*,troom_stb.room_id FROM tstb LEFT JOIN troom_stb ON tstb.stb_id = troom_stb.stb_id WHERE tstb.stb_id = ?';
    const SQL_GET_ONE_BY_STBNAME = 'SELECT * FROM tstb WHERE name = ?';

    const VIEW = 'vstb_status';

    protected $table      = 'tstb';
    protected $primaryKey = 'stb_id';
    protected $allowedFields = ['name', 'mac_address', 'ip_address', 'app_id', 'version_code', 'version_name', 'location', 'android_version', 'android_api', 'last_seen', 'status', 'create_date', 'update_date'];

    public $errCode;
    public $errMessage;


    public function get($stbId)
    {
        $r = $this
            ->where('stb_id', $stbId)
            ->find();
        if ($r!=null) return $r[0];

        return null;
    }

    public function getAll(){

        // $db = db_connect();
        // return $db->query(self::SQL_GET_ALL, array())->getResult('array');
        return $this->findAll();
    }

    public function getFieldList(){
        return ['stb_id', 'ip_address', 'name', 'mac_address', 'location', 'status', 'app_id', 'version_code', 'version_name', 'android_version', 'android_api', 'last_seen', 'create_date', 'update_date'];
    }



    public function getOneLatesApkEachGroup()
    {
        $query 		= "SELECT tapp.* FROM tapp GROUP BY app_id ORDER BY version_code DESC";
        $result     = $this->db->query($query)->getResult('array');
        if($result AND count($result) > 0)
        {
            return $result; 
        }
        return [];
    }

    public function getSTBForSelectForm()
    {
        $result  = $this->db->query(self::SQL_GET_STB_FOR_SELECT)->getResult('array');
        if($result AND count($result) > 0)
        {
            return $result;
        }
        return [];
    }

    public function getOneLastVersionApkByAppId($app_id)
    {
        $result = $this->db->query(self::SQL_GET_ONELASTVERSION_BY_APPID,$app_id)->getResult('array');
        if($result AND count($result) > 0)
        {
            return $result[0];
        }
        return false;
    }

    public function getOneLastVersionApk()
    {
        $result = $this->db->query(self::SQL_GET_ONELASTVERSION)->getResult('array');
        if($result AND count($result) > 0)
        {
            return $result[0];
        }
        return false;
    }

    public function getAllIP()
    {
        return  $this->db->query(self::SQL_GET_IP_ALL,[])->getResult('array');
    }

    /**
     * hanya ambil ip yg last_seen < 15 second
     * @return mixed
     */
    public function getActiveIp()
    {
        $db = db_connect();
        return  $db->query(self::SQL_GET_ACTIVE_IP,[])->getResult('array');
    }

    public function getIPSTBDevicesByRoomID($room_id)
    {
        $db = db_connect();
        return  $db->query(self::SQL_GET_IP_BY_ROOM,[$room_id])->getResult('array');
    }

    public function getIPSTBDevicesBySubscriberID($subscriber_id)
    {
        $db = db_connect();
        return  $db->query(self::SQL_GET_IP_BY_SUBSCRIBER,[$subscriber_id])->getResult('array');
    }

    public function getIPSTBDevicesBySTBID($stb_id)
    {
        $db = db_connect();
        return  $db->query(self::SQL_GET_IP_BY_STBID,[$stb_id])->getResult('array');
    }


    public function getOneBy($stb_id)
    {
        $result = $this->db->query(self::SQL_GET_ONE_BY_STBID,[$stb_id])->getResult('array');
        if($result AND count($result) > 0)
        {
            $querymac		= "SELECT tstb_mac.mac_address FROM tstb_mac WHERE tstb_mac.stb_id = ?";
            $resultstb      = $this->db->query($querymac,[$stb_id])->getResult('array');
            $mac_addres     = [];
            if($resultstb AND count($resultstb) > 0)
            {
                foreach($resultstb as $value)
                {
                    $mac_addres[] = $value['mac_address'];
                }
            }
            $result[0]['mac_address'] = $mac_addres;
            return $result[0];
        }
        return false;
    }

    public function getOneByName($name)
    {
        $result  = $this->db->query(self::SQL_GET_ONE_BY_STBNAME, [$name])->getResult('array');
        if($result && count($result) > 0)
        {
            return $result[0];
        }
        return false;
    }

    public function findOneBy($namaColumn,$valueColumn)
    {
        $result = $this->db->get_where($this->table, array($c => $valueColumn), 1, 0)->getResult('array');
        if($result AND count($result) > 0)
        {
            return $result[0]; 
        }
        return false;
    }


    public function add($value)
    {
        try
        {
            // $value['ip_address'] = htmlentities($value['ip_address'], ENT_QUOTES, 'UTF-8');
            $value['name'] = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
            // $value['mac_address'] = htmlentities($value['mac_address'], ENT_QUOTES, 'UTF-8');
            $value['location'] = htmlentities($value['location'], ENT_QUOTES, 'UTF-8');
            
            parent::insert($value);

        }
        catch (\Exception $e){
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();

            return 0;
        }

        return $this->db->affectedRows();
    }
    

    public function createSession($stb_id,$sessionId)
    {
        $datatosave                = [];
        $datatosave['session_id']  = $sessionId;
        $datatosave['stb_id']      = $stb_id;
        $datatosave['create_date'] = date('Y-m-d H:i:s');
        $datatosave['update_date'] = date('Y-m-d H:i:s');
        $datatosave['last_seen']   = date('Y-m-d H:i:s');
        $this->db->db_debug = true;
        $statusinsert = $this->db->insert('tstb_session', $datatosave);
        $this->db->db_debug = true;
        return;
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

        $stbId = $value['stb_id'];
        $ipAddress = $value['ip_address'];

        $name = htmlentities($value['name'], ENT_QUOTES, 'UTF-8');
        // $macAddress = htmlentities($value['mac_address'], ENT_QUOTES, 'UTF-8');
        // $ipAddress = htmlentities($value['ip_address'], ENT_QUOTES, 'UTF-8');
        $location = htmlentities($value['location'], ENT_QUOTES, 'UTF-8');

        try{
            $pdo = $this->openPdo();
            $stmt = $pdo->prepare(self::SQL_MODIFY);
            $stmt->execute( [$name, $location, $ipAddress, $stbId] );

            return $stmt->rowCount();

        }catch (\PDOException $e){
            log_message('error', json_encode($e));
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return -1;
        }
    }

    public function updateversion($stb_id,$lastapk)
    {
        $datatoupdate                 = [];
        $datatoupdate['app_id']       = $lastapk['app_id'];
        $datatoupdate['version_code'] = $lastapk['version_code'];
        $datatoupdate['version_name'] = $lastapk['version_name'];
        $this->db->db_debug = true;       
        $this->db->where('stb_id', $stb_id);
        $status = $this->db->update($this->table,$datatoupdate);
        $this->db->db_debug = true;
        if($status)
        {
            $msg = "Versi Aplikasi STB Devices Berhasil Di Upgrade.";
            $this->session->set_flashdata('success_msg',$msg);   
        }
        else
        {
            $msg = "Versi Aplikasi STB Devices Gagal Upgrade.Database Processing Failed";
            $this->session->set_flashdata('error_msg',$msg);
        }
    }

    public function getRoom()
    {
        $query 		= "SELECT room_id AS id ,name AS value FROM troom";
		$result     = $this->db->query($query)->getResult('array');
    	return $result;
    }

    public function random($panjang)
    {
		$karakter = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		$string = '';
        for($i = 0; $i < $panjang; $i++)
        {
			$pos = rand(0,strlen($karakter)-1);
			$string .= $karakter[$pos];
		}
		return $string;
	}


    public function remove($stbId){
        $r = $this
            ->where('stb_id', $stbId)
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
        // return $this->_getSsp(self::VIEW, $this->primaryKey, $this->getFieldList());
        return $this->_getSsp($this->table, $this->primaryKey, $this->getFieldList());
    }
}
