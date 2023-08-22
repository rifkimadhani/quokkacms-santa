<?php
/**
 * Created by PageBuilder.
 * Date: 2023-03-28 12:20:30
 */

namespace App\Models;

class LocalityForm extends BaseForm
{
    public $locality_id;
    public $title;
    public $description;
    public $url_media;
    public $ord;

    function __construct()
    {
        $this->locality_id = ['type'=>'varchar', 'label'=>'Locality Id', 'readonly'=>'readonly' ];
        $this->title = ['type'=>'varchar', 'label'=>'Title', 'placeholder'=>'', 'required'=>''];
        $this->description = ['type'=>'text', 'label'=>'Description', 'placeholder'=>'Enter description here', 'rows'=>'8'];
        $this->url_media     = ['type'=>'filemanager','label'=>'Image','placeholder'=>''];
        $this->ord = ['type'=>'numeric', 'label'=>'Ord', 'placeholder'=>'', 'required'=>''];
    }
}
