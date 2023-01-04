<?php

namespace App\Models;

class SubscriberForm extends BaseForm
{
    public $group_id;
    public $subscriber_id;

    public $salutation;
    public $name;
    public $last_name;
    public $room_id;

    public function __construct($room=[], $group=[])
	{
        $this->subscriber_id= ['type'=>'hidden', 'value'=>'0'];

        $this->group_id = ['type'=>'select','label'=>'Group','options'=>$group,'placeholder'=>'---'];

        $this->salutation = ['type'=>'varchar','label'=>'Salutation','placeholder'=>'Mr. Mrs. Ms.'];
        $this->name = ['type'=>'varchar','label'=>'First Name','placeholder'=>'Masukkan Nama Pelanggan Disini'];
        $this->last_name = ['type'=>'varchar','label'=>'Last Name','placeholder'=>'Masukkan Nama Pelanggan Disini'];
        $this->room_id = ['type'=>'select-multiple','label'=>'Room','required'=>'required','options'=>$room,'placeholder'=>'Pilih Room Disini'];
    }
}
