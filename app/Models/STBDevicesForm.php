<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-06 09:43:25
 */

namespace App\Models;

class STBDevicesForm extends BaseForm
{
    public $stb_id;
    public $ip_address;
    public $name;
    public $location;


    function __construct()
    {
        $this->stb_id = ['type'=>'numeric', 'label'=>'STB ID', 'readonly'=>'readonly'];
        $this->ip_address = ['type'=>'varchar', 'label'=>'IP Address', 'readonly'=>'readonly'];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'placeholder'=>'STB Name', 'required'=>'required'];
        $this->location = ['type'=>'varchar', 'label'=>'Location', 'placeholder'=>'STB`s Location', 'required'=>''];

    }
}
