<?php

/**
 * Created by PageBuilder
 * Date: 2023-06-06 09:43:25
 */

namespace App\Controllers;

use App\Models\STBDevicesModel;
use App\Models\STBDevicesForm;

class STBDevices extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'stbdevices/index';
        $primaryKey = 'stb_id';
        $pageTitle = 'Devices';

        $model = new STBDevicesModel();
        $fieldList = $model->getFieldList();
        $deviceList = $model->getAll();
        // dd($deviceList, $fieldList);

        // $macAddress = [];
        $room = $model->getRoom();
        $form = new STBDevicesForm();

        return view('layout/template', compact('mainview', 'primaryKey','fieldList', 'pageTitle', 'baseUrl', 'form', 'deviceList'));
    }

    // public function updateversion()
	// {
	// 	if($this->input->server('REQUEST_METHOD') == 'POST')
    //     {
	// 		$stb_id 	 = $this->input->post('stb_id');
	// 		$datastb     = $this->maindbaction->getOneBy($stb_id);
	// 		$ip_address  = $datastb['ip_address'];
	// 		$ports       = 5555;
	// 		$devicestcp  = "{$ip_address}:{$ports}";
	// 		$lastapk     = $this->maindbaction->getOneLastVersionApk();
	// 		$lastapkid   = $lastapk['app_id'];
	// 		$mainactivty = $lastapk['main_activity'];
	// 		$lastapkpath = $lastapk['path'];
	// 		$apkfilename = basename($lastapkpath);

	// 		$timezoneres = $this->settingmodel->findOneBy('setting_id',14);
	// 		$serverapi   = $this->settingmodel->findOneBy('setting_id',1);

	// 		$pathAndroid = '/storage/sdcard0/Pictures/DariRemote';
	// 		$hostApi     = $serverapi['value_string'];
	// 		$timeZone    = $timezoneres['value_string'];
	// 		$configFile  = 'config.json';
	// 		$sessionId   = $this->maindbaction->random(24);
	// 		$this->maindbaction->createSession($stb_id,$sessionId);

	// 		$configJson = "{\"host_api\": \"{$hostApi}\", \"time_zone\": \"{$timeZone}\", \"session_id\": \"{$sessionId}\"}";
	// 		$configJson = str_replace('"', '\"', $configJson);

	// 		$resdevices = $this->adb->devices();
	// 		$resconnect = $this->adb->connect($ip_address,5555);
	// 		$resmkdir   = $this->adb->mkdir($devicestcp,$pathAndroid);
	// 		$reschmod   = $this->adb->chmod($devicestcp,$pathAndroid);
	// 		$respush    = $this->adb->push($devicestcp,$lastapkpath,$pathAndroid);
	// 		$resinject  = $this->adb->shell($devicestcp,"echo '{$configJson}' > {$pathAndroid}/{$configFile}" );
	// 		$resinstall = $this->adb->install($devicestcp,$pathAndroid,$apkfilename);
	// 		$resrunapp  = $this->adb->runApp($devicestcp,$lastapkid,$mainactivty);
	// 		$resdisconnect  = $this->adb->disconnect($devicestcp);

	// 		if($resinstall['retString'] === 'Success')
	// 		{
	// 			$this->maindbaction->updateversion($stb_id,$lastapk);
	// 		}
	// 		else
	// 		{
	// 			$msg = "Versi Aplikasi STB Devices Gagal Upgrade.Check Detail Prosesnya Dibawah.";
    //         	$this->session->set_flashdata('error_msg',$msg);
	// 		}
			
	// 		$mainview   = "stbdevices/updateversion";
	// 		$this->load->view('layout/template', compact('mainview','resdevices','resconnect','resmkdir','reschmod','respush','resinject','resinstall','resrunapp','resdisconnect'));	
    //     }
    //     else
	// 	{
	// 		redirect('errorpage/methodnotallowed');	
	// 	}
    // }

    public function ssp()
    {
        $model = new STBDevicesModel();

//        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

//        echo json_encode($data);

        return $this->response->setJSON($data);
    }

    public function insert(){
        $model = new STBDevicesModel();

        $this->normalizeData($_POST, true);

        // Check if mac_address is empty and set it to null
        if (empty($_POST['mac_address'])) {
            $_POST['mac_address'] = null;
        }

        $r = $model->add($_POST);

        if ($r>0){
            $this->setSuccessMessage('Insert success');
        } else {
            $this->setErrorMessage('Insert fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * function ini di call saat user click row pada table
     *
     * @return mixed html dialog
     */
    public function edit($stbId)
    {
        $model = new STBDevicesModel();
        $data = $model->get($stbId);

        // $macAddress = $model->getMacForSelect();
        $room = $model->getRoom();
        $form = new STBDevicesForm();

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new STBDevicesModel();

        $this->normalizeData($_POST);

        // if (empty($_POST['mac_address'])) {
        //     $_POST['mac_address'] = null;
        // }

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($stbId){
        $model = new STBDevicesModel();
        $r = $model->remove($stbId);

        if ($r>0){
            $this->setSuccessMessage('Delete success');
        } else {
            $this->setErrorMessage('Delete fail');
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * melaukan proses normalisasi data apabila di butuhkan
     *
     * @param $data array, sbg in dan out
     * @param bool $isInsert
     */
    protected function normalizeData(&$data, $isInsert=false){

    }

    /**
     * melakukan conversi data ke asalnya, misalnya utk url balik dari BASE-HOST -> http://
     * @param $data
     */
    protected function sspDataConversion(&$data){

        foreach($data['data'] as &$row){
            $lastSeen = $row[11];
            $row[5] = $this->isLive($lastSeen);
        }
    }

    function isLive($date_string) {
        // Convert the input date string to a timestamp
        $input_date = strtotime($date_string);

        // Get the current time and add 15 seconds
        $current_time_plus_15 = time() + 15;

        // Compare the input date with current time + 15 seconds
        if ($input_date > $current_time_plus_15) {
            return 1;
        } else {
            return 0;
        }
    }
}
