<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-07 15:53:32
 */

namespace App\Models;

class EmergencyCategoryForm extends BaseForm
{
    public $emergency_code;
    public $name;
    // public $path;
    public $url_image;
    // public $create_date;
    // public $update_date;


    function __construct()
    {
        $this->emergency_code = ['type'=>'varchar', 'label'=>'Emergency Code', 'placeholder'=>'Emergency Code (e.g., FIRE, FLOOD, EARTHQUAKE)', 'required'=>'required'];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'placeholder'=>'Emergency Name', 'required'=>''];
        // $this->path = ['type'=>'varchar', 'label'=>'Path', 'placeholder'=>'', 'required'=>''];
        $this->url_image = ['type'=>'filemanager', 'label'=>'Emergency Image', 'placeholder'=>'Choose Emergency Image', 'required'=>''];
        // $this->create_date = ['type'=>'datetime', 'label'=>'Create Date', 'placeholder'=>'', 'required'=>''];
        // $this->update_date = ['type'=>'datetime', 'label'=>'Update Date', 'placeholder'=>'', 'required'=>''];

    }
}
