<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/21/2023
 * Time: 10:21 AM
 */

namespace App\Controllers;

use App\Libraries\PageBuilder;

class Builder extends BaseController
{
    public function index(){

        $mainview = 'builder/index';
        $pageTitle = "Builder";
        $baseUrl = $this->baseUrl;

        //ambil file sample.json, utk di tampilkan di ui
        $filename = __DIR__ . '/../Libraries/template/sample.json';
        $sample = PageBuilder::readFile($filename);

        return view('layout/template', compact('mainview', 'pageTitle', 'baseUrl', 'sample'));
    }

    public function build(){
        //convert json string ke obj.
        //utk mencheck apakah json string yg di input valid atau tdk
        $obj = json_decode($_POST['json']);

        //obj null, maka ada kesalahan pada json string
        if ($obj==null){
            return 'JSON not valid, please check your json';
        }

        PageBuilder::buildAll($obj);

        return 'SUCCESS';
    }
}