<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-08 12:29:19
 */

namespace App\Models;

class KitchenMenuForm extends BaseForm
{
    public $menu_id;
//    public $kitchen_id;
    public $menu_group_id;
    public $name;
    public $description;
    public $price;
    public $in_room_dining;
    public $url_image;


    function __construct($arKithenGroup=[])
    {
        $this->menu_id = ['type'=>'numeric', 'label'=>'Menu Id', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
//        $this->kitchen_id = ['type'=>'numeric', 'label'=>'Kitchen Id', 'placeholder'=>'', 'required'=>''];
        $this->menu_group_id = ['type'=>'select', 'label'=>'Menu Group Id', 'options'=>$arKithenGroup, 'placeholder'=>'Select menu group', 'required'=>'required'];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'placeholder'=>'', 'required'=>''];
        $this->description = ['type'=>'varchar', 'label'=>'Description', 'placeholder'=>'', 'required'=>''];
        $this->price = ['type'=>'varchar', 'label'=>'Price', 'placeholder'=>'', 'required'=>''];
        $this->in_room_dining = ['type'=>'varchar', 'label'=>'In Room Dining', 'placeholder'=>'', 'required'=>''];
        $this->url_image = ['type'=>'filemanager', 'label'=>'Url Image', 'placeholder'=>'', 'required'=>''];

    }
}
