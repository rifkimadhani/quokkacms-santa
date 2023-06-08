<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-08 12:15:46
 */

namespace App\Models;

class KitchenMenuGroupForm extends BaseForm
{
    public $menu_group_id;
    public $kitchen_id;
    public $group_name;
    public $description;
    public $url_thumb;
    public $service_open;
    public $service_close;
    public $seq;


    function __construct($kitchen=[])
    {
        $this->menu_group_id = ['type'=>'numeric', 'label'=>'Menu Group Id', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->kitchen_id = ['type'=>'select', 'label'=>'Kitchen', 'placeholder'=>'Pilih kitchen', 'options'=>$kitchen];
        $this->group_name = ['type'=>'varchar', 'label'=>'Group Name', 'placeholder'=>'', 'required'=>''];
        $this->description = ['type'=>'varchar', 'label'=>'Description', 'placeholder'=>'', 'required'=>''];
        $this->url_thumb = ['type'=>'varchar', 'label'=>'Url Thumb', 'placeholder'=>'', 'required'=>''];
        $this->service_open = ['type'=>'numeric', 'label'=>'Service Open', 'placeholder'=>'', 'required'=>''];
        $this->service_close = ['type'=>'numeric', 'label'=>'Service Close', 'placeholder'=>'', 'required'=>''];
        $this->seq = ['type'=>'numeric', 'label'=>'Seq', 'placeholder'=>'', 'required'=>''];

    }
}
