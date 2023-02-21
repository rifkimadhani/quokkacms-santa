<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/21/2023
 * Time: 10:21 AM
 */

namespace App\Controllers;

class PageBuilder extends BaseController
{
    public function index(){

        $mainview = 'empty';
        $pageTitle = "Builder";

        return view('layout/template', compact('mainview', 'pageTitle'));
    }

    public function build(){
        $filename = __DIR__ . '/../Libraries/template/sample.json';
        $file = fopen($filename, "r");
        $json = fread($file, filesize($filename));
        fclose($file);
        \App\Libraries\PageBuilder::buildAll(json_decode($json));
    }
}