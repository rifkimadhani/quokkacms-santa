<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-06 12:18:00
 */

namespace App\Models;

class LanguageForm extends BaseForm
{
    public $lang_code;
    public $language;
//    public $crate_date;
//    public $update_date;


    function __construct()
    {
        $this->lang_code = ['type'=>'varchar', 'label'=>'Lang Code', 'placeholder'=>'', 'required'=>'required'];
        $this->language = ['type'=>'varchar', 'label'=>'Language', 'placeholder'=>'', 'required'=>''];
//        $this->crate_date = ['type'=>'datetime', 'label'=>'Crate Date', 'placeholder'=>'', 'required'=>''];
//        $this->update_date = ['type'=>'datetime', 'label'=>'Update Date', 'placeholder'=>'', 'required'=>''];

    }
}
