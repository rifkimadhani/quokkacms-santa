<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {

        $mainview = 'welcome_message';
        return view('template', compact('mainview'));

//        return view('welcome_message');
    }
}
