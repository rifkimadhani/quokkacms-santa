<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-06 16:24:59
 */

namespace App\Models;

class HotelServiceForm extends BaseForm
{
    public $task_id;
    public $room_id;
    public $subscriber_id;
    public $type;
    public $status;
//    public $create_date;
//    public $update_date;
    public $data;


    function __construct()
    {
        $this->task_id = ['type'=>'numeric', 'label'=>'Task Id', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->room_id = ['type'=>'numeric', 'label'=>'Room Id', 'placeholder'=>'', 'required'=>''];
        $this->subscriber_id = ['type'=>'numeric', 'label'=>'Subscriber Id', 'placeholder'=>'', 'required'=>''];
        $this->type = ['type'=>'varchar', 'label'=>'Type', 'placeholder'=>'', 'required'=>''];
        $this->status = ['type'=>'varchar', 'label'=>'Status', 'placeholder'=>'', 'required'=>''];
//        $this->create_date = ['type'=>'datetime', 'label'=>'Create Date', 'placeholder'=>'', 'required'=>''];
//        $this->update_date = ['type'=>'datetime', 'label'=>'Update Date', 'placeholder'=>'', 'required'=>''];
        $this->data = ['type'=>'varchar', 'label'=>'Data', 'placeholder'=>'', 'required'=>''];

    }
}
