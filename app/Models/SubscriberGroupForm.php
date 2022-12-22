<?php

namespace App\Models;

class SubscriberGroupForm
{
    const STATUS = [
        ['id'=>'ACTIVE', 'value'=>'ACTIVE'],
        ['id'=>'INACTIVE', 'value'=>'INACTIVE']
    ];

    public $group_id;
    public $name;
    public $status;

    //test field
    public $text;
    public $hidden;
    public $password;

    function __construct()
	{
        $this->group_id = ['type'=>'varchar','label'=>'Group Id','required'=>'','min'=>0,'max'=>100,'placeholder'=>'','readonly'=>'readonly'];
        $this->name = ['type'=>'varchar','label'=>'Name of group','required'=>'required','min'=>0,'max'=>100,'default'=>null,'placeholder'=>'eg. World peace'];
        $this->status = ['type'=>'select','label'=>'Status','required'=>'','min'=>0,'max'=>100,'default'=>self::STATUS,'placeholder'=>''];

        //test field
        $this->textarea = ['type'=>'text', 'value'=>'Hello', 'rows'=>3];
        $this->hidden = ['type'=>'hidden', 'value'=>'hidden value'];
        $this->password = ['type'=>'password', 'value'=>'P@ssword', 'placeholder'=>'Enter password'];
    }
}
