<?php
/**
 * Created by PageBuilder.
 * Date: 2023-05-10 11:25:21
 */

namespace App\Models;

class RoomTypeForm extends BaseForm
{
    public $room_type_id;
    public $type;
    public $type_order;


    function __construct()
    {
        $this->room_type_id = ['type'=>'numeric', 'label'=>'Room Type Id', 'placeholder'=>'', 'required'=>'required'];
        $this->type = ['type'=>'varchar', 'label'=>'Type', 'placeholder'=>'', 'required'=>''];
        $this->type_order = ['type'=>'numeric', 'label'=>'Type Order', 'placeholder'=>'', 'required'=>''];

    }
}
