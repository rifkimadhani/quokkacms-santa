<?php
/**
 * Created by PageBuilder.
 * Date: 2023-04-04 14:18:55
 */

namespace App\Models;

class ThemeForm extends BaseForm
{
    public $theme_id;
    public $element_id;
    // public $path;
    public $url_image;
    public $color_value;

    function __construct($element=[], $theme=[])
    {
        $this->theme_id = ['type'=>'select','label'=>'Theme','options'=>$theme,'placeholder'=>'---'];
        $this->element_id = ['type'=>'select', 'label'=>'Element', 'options'=>$element, 'placeholder'=>'---'];
        // $this->path = ['type'=>'varchar', 'label'=>'Path', 'placeholder'=>'', 'required'=>''];
        // $this->url_image = ['type'=>'varchar', 'label'=>'Url Image', 'placeholder'=>'', 'required'=>''];
        $this->url_image = ['type'=>'filemanager','label'=>'Image','placeholder'=>''];
        
        $this->color_value = ['type'=>'varchar', 'label'=>'Color Value', 'placeholder'=>'', 'required'=>''];

    }
}
