<?php
/**
 * Created by PageBuilder.
 * Date: 2023-03-31 13:21:41
 */

namespace App\Models;

class ElementForm extends BaseForm
{
    public $element_id;
    public $name;
    public $group;
    public $type;
    // public $width;
    // public $height;
    // public $create_date;
    // public $update_date;

    const GROUP = [
        ['id'=>'HOTELIPTV3', 'value'=>'HOTELIPTV3'],
        ['id'=>'RUNNING-TEXT', 'value'=>'RUNNING-TEXT']
    ];

    const TYPE = [
        ['id'=>'IMAGE','value'=>'IMAGE'],
        ['id'=>'COLOR','value'=>'COLOR'],
        ['id'=>'AUDIO','value'=>'AUDIO'],
        ['id'=>'VIDEO','value'=>'VIDEO'],
        ['id'=>'STREAM','value'=>'STREAM'],
    ];

    function __construct()
    {
        $this->element_id = ['type'=>'numeric', 'label'=>'Element Id', 'placeholder'=>'', 'required'=>'required'];
        $this->name = ['type'=>'varchar', 'label'=>'Element Name', 'placeholder'=>'', 'required'=>''];
        $this->group = ['type'=>'select','label'=>'Grup Element', 'default'=>'HOTELIPTV3', 'options'=>self::GROUP,'placeholder'=>'Pilih Group Element Disini', 'required'=>'',];
        $this->type = ['type'=>'select', 'label'=>'Element Type', 'options'=>self::TYPE, 'placeholder'=>'Pilih Type Element Disini', 'required'=>''];

        // $this->width = ['type'=>'numeric', 'label'=>'Width', 'placeholder'=>'', 'required'=>''];
        // $this->height = ['type'=>'numeric', 'label'=>'Height', 'placeholder'=>'', 'required'=>''];
        // $this->create_date = ['type'=>'datetime', 'label'=>'Create Date', 'placeholder'=>'', 'required'=>''];
        // $this->update_date = ['type'=>'datetime', 'label'=>'Update Date', 'placeholder'=>'', 'required'=>''];

    }
}
