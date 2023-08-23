<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-06 12:33:41
 */

namespace App\Models;

class SettingForm extends BaseForm
{
    public $setting_id;
    public $name;
    public $value_int;
    public $value_float;
    public $value_string;

    function __construct()
    {
        $this->setting_id = ['type'=>'numeric', 'label'=>'Setting Id', 'placeholder'=>'', 'required'=>'required'];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'placeholder'=>'', 'required'=>''];
        $this->value_int = ['type'=>'numeric', 'label'=>'Value Int'];
        $this->value_float = ['type'=>'numeric', 'label'=>'Value Float', 'step'=>'0.01'];
        $this->value_string = ['type'=>'text', 'label'=>'Value String', 'rows'=>'7'];
    }
}
