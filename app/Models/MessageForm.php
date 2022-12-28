<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 12:31 PM
 */

namespace App\Models;

class MessageForm extends BaseForm
{
    const STATUS = [
        ['id'=>'NEW', 'value'=>'NEW'],
        ['id'=>'READ', 'value'=>'READ']
    ];

    public $message_id;
    public $subscriber_id;
    public $room_id;

    public $title;
    public $message;
    public $url_image;
    public $url_image2;
    public $status;

    public $password;
    public $password2;

    public function __construct( $subscriber=[], $room=[])
    {
        $this->message_id = ['type'=>'varchar', 'label'=>'Message Id', 'readonly'=>'readonly'];
        $this->subscriber_id = ['type'=>'select', 'label'=>'Guest', 'options'=>$subscriber, 'placeholder'=>'Pilih guest', 'required'=>'required'];
        $this->room_id       = ['type'=>'select','label'=>'Room', 'options'=>$room, 'placeholder'=>'Pilih room', 'required'=>'required'];

        $this->title         = ['type'=>'varchar','label'=>'Judul Message','required'=>'required','min'=>0,'max'=>100,'default'=>null,'placeholder'=>''];
        $this->message       = ['type'=>'text','label'=>'Isi Message','required'=>'required','maxlength'=>null,'rows'=>5,'default'=>null,'placeholder'=>''];
        $this->url_image     = ['type'=>'filemanager','label'=>'Image','placeholder'=>''];
        $this->url_image2    = ['type'=>'filemanager','label'=>'Image','placeholder'=>''];

        $this->status = ['type'=>'select','label'=>'Status', 'options'=>self::STATUS, 'placeholder'=>'---', 'required'=>'required'];
//        $this->password = ['type'=>'password','label'=>'Password', 'placeholder'=>'---'];
//        $this->password2 = ['type'=>'password','label'=>'Password', 'placeholder'=>'---'];
    }

}