<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/21/2023
 * Time: 10:21 AM
 */

namespace App\Controllers;

use App\Libraries\PageBuilder;
use App\Models\BuilderModel;

class Builder extends BaseController
{
    public function index(){

        $mainview = 'builder/index';
        $pageTitle = "Builder";
        $baseUrl = $this->baseUrl;

        //pakai sample sebagai awal json
        $sample = PageBuilder::readFile(__DIR__ . '/../Libraries/template/blank.json');

        $model = new BuilderModel();
        $tables = $model->getTables();

        return view('layout/template', compact('mainview', 'pageTitle', 'baseUrl', 'sample', 'tables'));
    }

    public function ajaxGetFields($tableName){
        $model = new BuilderModel();
        $fields = $model->getFields($tableName);

        $this->response->setContentType("application/json");
        echo json_encode($fields);
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

        return json_encode(['status'=>'SUCCESS, please check output folder']);
    }

}