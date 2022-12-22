<?php

namespace App\Models;

class SubscriberGroupForm extends BaseForm
{
    const STATUS = [
        ['id'=>'ACTIVE', 'value'=>'ACTIVE'],
        ['id'=>'INACTIVE', 'value'=>'INACTIVE']
    ];

    public $group_id;
    public $name;
    public $status;

    function __construct()
	{
        $this->group_id = ['type'=>'varchar','label'=>'Group Id','required'=>'','min'=>0,'max'=>100,'placeholder'=>'','readonly'=>'readonly'];
        $this->name = ['type'=>'varchar','label'=>'Name of group','required'=>'required','min'=>0,'max'=>100,'default'=>null,'placeholder'=>'eg. World peace'];
        $this->status = ['type'=>'select','label'=>'Status','required'=>'required','default'=>self::STATUS,'placeholder'=>'Choose status'];
    }
}
