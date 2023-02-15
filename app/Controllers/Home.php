<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $pageTitle = 'Home';

        $mainview = 'welcome_message';
        return view('layout/template', compact('mainview', 'pageTitle'));

//        return view('welcome_message');
    }
}
