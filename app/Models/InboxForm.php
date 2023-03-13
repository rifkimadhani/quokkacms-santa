<?php
/**
 * Created by PageBuilder.
 * Date: 2023-03-06 10:56:19
 */

namespace App\Models;

class InboxForm extends BaseForm
{
    public $inbox_id;
    public $user_id;
    public $title;
    public $content;
    public $url_image;
    public $path_image;
    public $status;
    public $exp_date;
    public $create_date;
    public $update_date;


    function __construct()
    {
        $this->inbox_id = ['type'=>'numeric', 'label'=>'Inbox Id', 'placeholder'=>'', 'required'=>''];
        $this->user_id = ['type'=>'numeric', 'label'=>'User Id', 'placeholder'=>'', 'required'=>''];
        $this->title = ['type'=>'varchar', 'label'=>'Title', 'placeholder'=>'', 'required'=>''];
        $this->content = ['type'=>'varchar', 'label'=>'Content', 'placeholder'=>'', 'required'=>''];
        $this->url_image = ['type'=>'varchar', 'label'=>'Url Image', 'placeholder'=>'', 'required'=>''];
        $this->path_image = ['type'=>'varchar', 'label'=>'Path Image', 'placeholder'=>'', 'required'=>''];
        $this->status = ['type'=>'varchar', 'label'=>'Status', 'placeholder'=>'', 'required'=>''];
        $this->exp_date = ['type'=>'datetime', 'label'=>'Exp Date', 'placeholder'=>'', 'required'=>''];
        $this->create_date = ['type'=>'datetime', 'label'=>'Create Date', 'placeholder'=>'', 'required'=>''];
        $this->update_date = ['type'=>'datetime', 'label'=>'Update Date', 'placeholder'=>'', 'required'=>''];

    }
}
