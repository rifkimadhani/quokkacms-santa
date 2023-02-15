<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/16/2022
 * Time: 1:21 PM
 */

namespace App\Controllers;

class Dashboard extends BaseController {

    public $headertitle = "Dashboard";

//    public function __construct()
//    {
//        parent::__construct();
//        $this->load->model('CountingModel', 'maindbaction');
//    }

    public function index()
    {
        $baseUrl = $this->baseUrl;
        $mainview = "dashboard/index";
        $pageTitle = 'Dashboard';

        $db = new \App\Models\CountingModel();

        $datastb       = $db->getStbCounting();
        $dataroom      = $db->getRoomCounting();
        $dataguest      = $db->getGuestCounting();
        $dataemergency      = $db->getEmergencyCounting();

//        $datastb       = $this->maindbaction->getStbCounting();
//        $dataroom      = $this->maindbaction->getRoomCounting();
//        $dataguest     = $this->maindbaction->getGuestCounting();
//        $dataemergency = $this->maindbaction->getEmergencyCounting();

        return view('layout/template',compact('mainview','pageTitle','datastb','dataroom','dataguest','dataemergency'));
    }
}
