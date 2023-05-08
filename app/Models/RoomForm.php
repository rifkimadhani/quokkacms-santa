<?php
/**
 * Created by PageBuilder.
 * Date: 2023-05-08 15:14:15
 */

namespace App\Models;

class RoomForm extends BaseForm
{
    public $room_id;
    public $name;
    public $location;
    public $room_type_id;
    public $theme_id;
    public $package_id;
    public $subscriber_id;
    public $create_date;
    public $update_date;
    public $status;
    public $security_pin;


    function __construct()
    {
        $this->room_id = ['type'=>'numeric', 'label'=>'Room Id', 'placeholder'=>'', 'required'=>''];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'placeholder'=>'', 'required'=>''];
        $this->location = ['type'=>'varchar', 'label'=>'Location', 'placeholder'=>'', 'required'=>''];
        $this->room_type_id = ['type'=>'numeric', 'label'=>'Room Type Id', 'placeholder'=>'', 'required'=>''];
        $this->theme_id = ['type'=>'numeric', 'label'=>'Theme Id', 'placeholder'=>'', 'required'=>''];
        $this->package_id = ['type'=>'numeric', 'label'=>'Package Id', 'placeholder'=>'', 'required'=>''];
        $this->subscriber_id = ['type'=>'numeric', 'label'=>'Subscriber Id', 'placeholder'=>'', 'required'=>''];
        $this->create_date = ['type'=>'datetime', 'label'=>'Create Date', 'placeholder'=>'', 'required'=>''];
        $this->update_date = ['type'=>'datetime', 'label'=>'Update Date', 'placeholder'=>'', 'required'=>''];
        $this->status = ['type'=>'varchar', 'label'=>'Status', 'placeholder'=>'', 'required'=>''];
        $this->security_pin = ['type'=>'varchar', 'label'=>'Security Pin', 'placeholder'=>'', 'required'=>''];

    }
}
