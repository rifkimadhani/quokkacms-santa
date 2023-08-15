<?php

namespace App\Controllers;

use App\Models\AppModel;

class Installer extends BaseController
{
    const APPLICATION_ID = 'com.madeiraresearch.hoteliptv3';
    const PORT = 5555;
    const pathBase = '/data/mr'; //path untuk upload
//    public $lastapk;
//    public $lastapkid;
//    public $mainactivty;
//    public $lastapkpath;
//    public $apkfilename;

//    public function __construct()
//	{
//        parent::__construct();
//		$this->load->model('STBDevicesModel', 'maindbaction');
//		$this->load->model('SettingModel', 'settingmodel');
//		$this->load->model('STBDevicesFormModel');
//        $this->load->model('STBDevicesSspModel','sspmodel');
//        $this->lastapk     = $this->maindbaction->getOneLastVersionApk();
//        $this->lastapkid   = $this->lastapk['app_id'];
//        $this->mainactivty = $this->lastapk['main_activity'];
//        $this->lastapkpath = $this->lastapk['path'];
//        $this->apkfilename = basename($this->lastapkpath);
//	}

	public function index()
	{
        $baseUrl = $this->getBaseUrl();

        $model = new AppModel();

        $mainview = 'installer/index';
        $pageTitle = 'Installer';
        $latestgroupapk = $model->getLatestApk(self::APPLICATION_ID);
        $stbdevices = $model->getStbForSelect();

        return view('layout/template', compact('mainview', 'baseUrl', 'pageTitle', 'latestgroupapk', 'stbdevices'));
    }

    public function installApp(){
        require_once __DIR__ . '/../../model/ModelApp.php';

        $this->varDump($_POST);


        $ip = $_POST['ip_address'];
        $appId = $_POST['latest_apk'];
        $stbId= $_POST['stb_id'];

        $r = \ModelApp::install($appId, $ip, $stbId);

        return $this->index();
    }

    public function ajaxInstall(){
        require_once __DIR__ . '/../../model/ModelApp.php';

        $ip = $_POST['ip_address'];
        $appId = $_POST['latest_apk'];
        $stbId= $_POST['stb_id'];

        $r = \ModelApp::install($appId, $ip, $stbId);

        header('Content-Type: application/json');
        return json_encode($r);
    }

//    public function connect()
//    {
//        if($this->input->server('REQUEST_METHOD') == 'POST')
//        {
//            $ip_address  = $this->input->post('ip_address');
//            $devicestcp  = "{$ip_address}:{$this->ports}";
//
//            $r = $this->adb->startServer(); //start server manual
//            $this->logVarDump($r);
//
//            $resconnect  = $this->adb->connect($ip_address,$this->ports);
//
//            header('Content-Type: application/json');
//            if(strpos($resconnect['retString'],'failed to connect') !== false)
//            {
//                $this->adb->disconnect($devicestcp);
//                http_response_code(400);
//                echo json_encode(["error"=> strtoupper($resconnect['retString']) ]);
//            }
//            elseif(strpos($resconnect['retString'],'unable to connect to') !== false)
//            {
//                $this->adb->disconnect($devicestcp);
//                http_response_code(400);
//                echo json_encode(["error"=> strtoupper($resconnect['retString']) ]);
//            }
//            elseif(strpos($resconnect['retString'],'already connected to') !== false)
//            {
//                http_response_code(200);
//                echo json_encode($resconnect);
//            }
//            elseif(strpos($resconnect['retString'],'connected to') !== false)
//            {
//                http_response_code(200);
//                echo json_encode($resconnect);
//            }
//            else
//            {
//                $this->adb->disconnect($devicestcp);
//                http_response_code(400);
//                echo json_encode(["error"=> strtoupper($resconnect['retString']) ]);
//            }
//        }
//    }
//
////    function str_var_dump($value){
////        ob_start();
////        var_dump($value);
////        return ob_get_clean();
////    }
//
//    public function mkdir()
//    {
//        if($this->input->server('REQUEST_METHOD') == 'POST')
//        {
//            $ip_address  = $this->input->post('ip_address');
//            $devicestcp  = "{$ip_address}:{$this->ports}";
//
//            $resmkdir    = $this->adb->mkdir($devicestcp,$this->pathAndroid);
//
//            header('Content-Type: application/json');
//            if(strpos($resmkdir['retString'],'Permission denied') !== false)
//            {
//                http_response_code(400);
//                echo json_encode(["error"=> strtoupper($resmkdir['retString']) ]);
//            }
//            else
//            {
//                http_response_code(200);
//                echo json_encode($resmkdir);
//            }
//        }
//    }
//
//    public function chmod()
//    {
//        if($this->input->server('REQUEST_METHOD') == 'POST')
//        {
//            $ip_address  = $this->input->post('ip_address');
//            $devicestcp  = "{$ip_address}:{$this->ports}";
//
//            $reschmod    = $this->adb->chmod($devicestcp,$this->pathAndroid);
//            header('Content-Type: application/json');
//            echo json_encode($reschmod);
//        }
//    }
//
//    public function push()
//    {
//        if($this->input->server('REQUEST_METHOD') == 'POST')
//        {
//            $ip_address  = $this->input->post('ip_address');
//            $devicestcp  = "{$ip_address}:{$this->ports}";
//            $app_id      = $this->input->post('latest_apk');
//
//            $this->lastapk     = $this->maindbaction->getOneLastVersionApkByAppId($app_id);
//            $this->lastapkpath = $this->lastapk['path'];
//            $this->apkfilename = basename($this->lastapkpath);
//
//
//            $respush     = $this->adb->push($devicestcp,$this->lastapkpath,$this->pathAndroid);
//            unset($respush['output']);
//            header('Content-Type: application/json');
//            echo json_encode($respush);
//        }
//    }
//
//    public function injectconfig()
//    {
//        if($this->input->server('REQUEST_METHOD') == 'POST')
//        {
//            $ip_address  = $this->input->post('ip_address');
//            $stb_name    = $this->input->post('stb_name');
//            $devicestcp  = "{$ip_address}:{$this->ports}";
//
//            $timezoneres = $this->settingmodel->findOneBy('setting_id',14);
//            $serverapi   = $this->settingmodel->findOneBy('setting_id',1);
//
//            $hostApi     = base_url();//$serverapi['value_string'];
//			$timeZone    = $timezoneres['value_string'];
//			$configFile  = 'config.json';
//            $sessionId   = $this->maindbaction->random(24);
//
//            $namexist    = $this->maindbaction->getOneByName($stb_name);
//            if(!$namexist)
//            {
//                $datatosave = [];
//                $datatosave['name']         = $stb_name;
//                $datatosave['ip_address']   = $ip_address;
//                $datatosave['app_id']       = $this->lastapk['app_id'];
//                $datatosave['version_code'] = $this->lastapk['version_code'];
//                $datatosave['version_name'] = $this->lastapk['version_name'];
//                $stb_id = $this->maindbaction->insert($datatosave);
//            }
//            else
//            {
//                $datatoupdate = [];
//                $stb_id                       = $namexist['stb_id'];
//                $datatoupdate['app_id']       = $this->lastapk['app_id'];
//                $datatoupdate['version_code'] = $this->lastapk['version_code'];
//                $datatoupdate['version_name'] = $this->lastapk['version_name'];
//                $this->maindbaction->updateversion($stb_id,$datatoupdate);
//            }
//			$this->maindbaction->createSession($stb_id,$sessionId);
//
//			$configJson = "{\"host_api\": \"{$hostApi}\", \"time_zone\": \"{$timeZone}\", \"session_id\": \"{$sessionId}\"}";
//            $configJson = str_replace('"', '\"', $configJson);
//
//            $resinject  = $this->adb->shell($devicestcp,"echo '{$configJson}' > {$this->pathAndroid}/{$configFile}" );
//            header('Content-Type: application/json');
//            echo json_encode($resinject);
//        }
//    }
//
//    public function install()
//    {
//        $ip_address  = $_POST('ip_address');
//        $app_id      = $_POST('latest_apk');
//
//        $model = new AppModel();
//
//        $this->lastapk     = $this->maindbaction->getOneLastVersionApkByAppId($app_id);
//        $this->lastapkpath = $this->lastapk['path'];
//        $this->apkfilename = basename($this->lastapkpath);
//
//
//        $devicestcp  = "{$ip_address}:{$this->ports}";
//        $fullpathapk = $this->pathAndroid.'/'.$this->apkfilename;
//        $resinstall = $this->adb->install($devicestcp,$fullpathapk);
//
//        header('Content-Type: application/json');
//        if($resinstall['retString'] === 'Success')
//        {
//            http_response_code(200);
//            echo json_encode($resinstall);
//        }
//        else
//        {
//            http_response_code(400);
//            echo json_encode(["error"=> $resinstall ]);
//        }
//    }
//
//    public function runapp()
//    {
//        if($this->input->server('REQUEST_METHOD') == 'POST')
//        {
//            $ip_address        = $this->input->post('ip_address');
//            $devicestcp        = "{$ip_address}:{$this->ports}";
//            $app_id            = $this->input->post('latest_apk');
//            $this->lastapk     = $this->maindbaction->getOneLastVersionApkByAppId($app_id);
//            $this->lastapkid   = $this->lastapk['app_id'];
//            $this->mainactivty = $this->lastapk['main_activity'];
//
//            $resrunapp  = $this->adb->runApp($devicestcp,$this->lastapkid,$this->mainactivty);
//            header('Content-Type: application/json');
//            echo json_encode($resrunapp);
//        }
//    }
//
//    public function disconnect()
//    {
//        if($this->input->server('REQUEST_METHOD') == 'POST')
//        {
//            $ip_address  = $this->input->post('ip_address');
//            $devicestcp  = "{$ip_address}:{$this->ports}";
//
//            $resdisconnect  = $this->adb->disconnect($devicestcp);
//            header('Content-Type: application/json');
//            echo json_encode($resdisconnect);
//        }
//    }
//
//
//
//    public function manualconnect()
//    {
//        $ip_address  = $gridorlist = $this->input->get('ip_stb', TRUE);
//        $devicestcp  = "{$ip_address}:{$this->ports}";
//
//        $resconnect  = $this->adb->connect($ip_address,$this->ports);
//
//        header('Content-Type: application/json');
//        if(strpos($resconnect['retString'],'failed to connect') !== false)
//        {
//            $this->adb->disconnect($devicestcp);
//            http_response_code(400);
//            echo json_encode(["error"=> strtoupper($resconnect['retString']) ]);
//        }
//        elseif(strpos($resconnect['retString'],'unable to connect to') !== false)
//        {
//            $this->adb->disconnect($devicestcp);
//            http_response_code(400);
//            echo json_encode(["error"=> strtoupper($resconnect['retString']) ]);
//        }
//        else
//        {
//            http_response_code(200);
//            echo json_encode($resconnect);
//        }
//    }
//
//    public function manualmkdir()
//    {
//        $ip_address  = $this->input->get('ip_stb', TRUE);
//        $devicestcp  = "{$ip_address}:{$this->ports}";
//
//        $resmkdir    = $this->adb->mkdir($devicestcp,$this->pathAndroid);
//
//        header('Content-Type: application/json');
//        if(strpos($resmkdir['retString'],'Permission denied') !== false)
//        {
//            http_response_code(400);
//            echo json_encode(["error"=> strtoupper($resmkdir['retString']) ]);
//        }
//        else
//        {
//            http_response_code(200);
//            echo json_encode($resmkdir);
//        }
//    }
//
//    public function manualchmod()
//    {
//        $ip_address  = $this->input->get('ip_stb', TRUE);
//        $devicestcp  = "{$ip_address}:{$this->ports}";
//
//        $reschmod    = $this->adb->chmod($devicestcp,$this->pathAndroid);
//        header('Content-Type: application/json');
//        echo json_encode($reschmod);
//    }
//
//    public function manualinjectconfig()
//    {
//        $ip_address  = $this->input->get('ip_stb', TRUE);
//        $stb_name    = $this->input->get('stb_name', TRUE);
//        $devicestcp  = "{$ip_address}:{$this->ports}";
//
//        $timezoneres = $this->settingmodel->findOneBy('setting_id',14);
//        $serverapi   = $this->settingmodel->findOneBy('setting_id',1);
//
//        $hostApi     = $serverapi['value_string'];
//        $timeZone    = $timezoneres['value_string'];
//        $sessionId   = $this->maindbaction->random(24);
//
//        $namexist    = $this->maindbaction->getOneByName($stb_name);
//        if(!$namexist)
//        {
//            $datatosave = [];
//            $datatosave['name']         = $stb_name;
//            $datatosave['ip_address']   = $ip_address;
//            $datatosave['app_id']       = $this->lastapk['app_id'];
//            $datatosave['version_code'] = $this->lastapk['version_code'];
//            $datatosave['version_name'] = $this->lastapk['version_name'];
//            $stb_id = $this->maindbaction->insert($datatosave);
//        }
//        else
//        {
//            $datatoupdate = [];
//            $stb_id                       = $namexist['stb_id'];
//            $datatoupdate['app_id']       = $this->lastapk['app_id'];
//            $datatoupdate['version_code'] = $this->lastapk['version_code'];
//            $datatoupdate['version_name'] = $this->lastapk['version_name'];
//            $this->maindbaction->updateversion($stb_id,$datatoupdate);
//        }
//        $this->maindbaction->createSession($stb_id,$sessionId);
//
//        $dataconfig               = [];
//        $dataconfig['host_api']   = $hostApi;
//        $dataconfig['time_zone']  = $timeZone;
//        $dataconfig['session_id'] = $sessionId;
//
//        $configJson = json_encode($dataconfig);
//        $filename   = config.json;
//
//        $resinject  = $this->adb->shell($devicestcp,"echo '{$configJson}' > {$this->pathAndroid}/{$configFile}" );
//        header('Content-Type: application/json');
//        echo json_encode($resinject);
//    }
//
//    public function manualpush()
//    {
//        $ip_address        = $this->input->get('ip_stb', TRUE);
//        $devicestcp        = "{$ip_address}:{$this->ports}";
//        $app_id            = $this->input->get('app_id', TRUE);
//
//        $this->lastapk     = $this->maindbaction->getOneLastVersionApkByAppId($app_id);
//        $this->lastapkpath = $this->lastapk['path'];
//
//        $respush     = $this->adb->push($devicestcp,$this->lastapkpath,$this->pathAndroid);
//        header('Content-Type: application/json');
//        echo json_encode($respush);
//    }
//
//    public function manualinstall()
//    {
//        $ip_address        = $this->input->get('ip_stb', TRUE);
//        $devicestcp        = "{$ip_address}:{$this->ports}";
//        $app_id            = $this->input->get('app_id', TRUE);
//
//        $this->lastapk     = $this->maindbaction->getOneLastVersionApkByAppId($app_id);
//        $this->lastapkpath = $this->lastapk['path'];
//        $this->apkfilename = basename($this->lastapkpath);
//
//        $fullpathapk = $this->pathAndroid.'/'.$this->apkfilename;
//        $resinstall = $this->adb->install($devicestcp,$fullpathapk);
//
//        header('Content-Type: application/json');
//        if($resinstall['retString'] === 'Success')
//        {
//            http_response_code(200);
//            echo json_encode($resinstall);
//        }
//        else
//        {
//            http_response_code(400);
//            echo json_encode(["error"=> $resinstall ]);
//        }
//    }
//
//    public function manualrunapp()
//    {
//        $ip_address        = $this->input->get('ip_stb', TRUE);
//        $devicestcp        = "{$ip_address}:{$this->ports}";
//        $app_id            = $this->input->get('app_id', TRUE);
//
//        $this->lastapk     = $this->maindbaction->getOneLastVersionApkByAppId($app_id);
//        $this->lastapkid   = $this->lastapk['app_id'];
//        $this->mainactivty = $this->lastapk['main_activity'];
//
//        $resrunapp  = $this->adb->runApp($devicestcp,$this->lastapkid,$this->mainactivty);
//        header('Content-Type: application/json');
//        echo json_encode($resrunapp);
//    }
//
//    public function manualdisconnect()
//    {
//        $ip_address  = $this->input->get('ip_stb', TRUE);
//        $devicestcp  = "{$ip_address}:{$this->ports}";
//
//        $resdisconnect  = $this->adb->disconnect($devicestcp);
//        header('Content-Type: application/json');
//        echo json_encode($resdisconnect);
//    }
}
