<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 12:31 PM
 */

namespace App\Models;

class MessageFormGroup extends BaseForm
{
    const STATUS = [
        ['id'=>'NEW', 'value'=>'NEW'],
        ['id'=>'READ', 'value'=>'READ']
    ];

    public $from;
    public $message_id;
    public $group_id;
    public $title;
    public $message;
    public $url_image;
    public $status;

    public function __construct($group=[])
    {
        $this->from       = ['type'=>'hidden', 'value'=>'admin'];
        $this->message_id = ['type'=>'varchar', 'label'=>'Message Id', 'readonly'=>'readonly'];
        $this->group_id   = ['type'=>'select', 'label'=>'Guest', 'options'=>$group, 'placeholder'=>'Pilih Guest Group', 'required'=>'required'];
    
        $this->title      = ['type'=>'varchar','label'=>'Judul Message','required'=>'required','min'=>0,'max'=>100,'default'=>null,'placeholder'=>'Judul'];
        $this->message    = ['type'=>'text','label'=>'Isi Message','required'=>'required','maxlength'=>null,'rows'=>5,'default'=>null,'placeholder'=>'Isi'];
        $this->url_image  = ['type'=>'filemanager','label'=>'Image','placeholder'=>'Media'];

        $this->status     = ['type'=>'select','label'=>'Status', 'default'=>'NEW', 'options'=>self::STATUS, 'placeholder'=>'---', 'required'=>'required'];
    }

}