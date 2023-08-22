<?php
/**
 * Created by PageBuilder.
 * Date: 2023-03-30 15:24:23
 */

namespace App\Models;

class FacilityForm extends BaseForm
{
    public $facility_id;
    public $name;
    public $description;
    public $url_image;

    function __construct()
    {
        $this->facility_id = ['type'=>'varchar', 'label'=>'Facility Id', 'readonly'=>'readonly' ];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'placeholder'=>'', 'required'=>''];
        $this->description = ['type'=>'text', 'label'=>'Description', 'placeholder'=>'Enter description here', 'rows'=>'8'];
        $this->url_image     = ['type'=>'filemanager','label'=>'Image','placeholder'=>''];
    }
}
