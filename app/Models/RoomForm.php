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
    public $stb;
    public $theme_id;
    // public $package_id;
    // public $subscriber_id;
    public $status;
    public $security_pin;

    const STATUS = [
        ['id'=>'VACANT', 'value'=>'VACANT'],
        ['id'=>'OCCUPIED', 'value'=>'OCCUPIED']
    ];


    function __construct($type=[], $theme=[], $stbData=[], $selectedStb=[])
    {
        $this->room_id = ['type'=>'numeric', 'label'=>'Room Id', 'readonly'=>'readonly'];
        $this->name = ['type'=>'varchar', 'label'=>'Room Name', 'placeholder'=>'Input Room Name', 'required'=>'required'];
        $this->location = ['type'=>'varchar', 'label'=>'Location', 'placeholder'=>'Input Room Location', 'required'=>''];
        $this->room_type_id = ['type'=>'select', 'label'=>'Room Type', 'options'=>$type, 'placeholder'=>'Select Room Type'];
        $this->stb = ['type'=>'select-multiple','label'=>'STB','required'=>false,'options'=>$stbData, 'value'=>$selectedStb ,'placeholder'=>'Choose STB here.'];
        $this->theme_id = ['type'=>'select','label'=>'Theme', 'options'=>$theme, 'placeholder'=>'Select Theme'];
        // $this->package_id = ['type'=>'numeric', 'label'=>'Package Id', 'placeholder'=>'', 'required'=>''];
        // $this->subscriber_id = ['type'=>'numeric', 'label'=>'Subscriber Id', 'placeholder'=>'', 'required'=>''];
        $this->status = ['type'=>'select', 'label'=>'Status', 'options'=>self::STATUS,'placeholder'=>'Select Room Status', 'required'=>''];
        $this->security_pin = ['type'=>'varchar', 'label'=>'Security PIN', 'min'=>0, 'max' => 4, 'placeholder'=>'Security Pin must be a 4-digit number', 'required'=>''];

    }
}
