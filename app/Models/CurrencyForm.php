<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-06 12:27:39
 */

namespace App\Models;

class CurrencyForm extends BaseForm
{
    public $currency;
    public $currency_sign;
    public $description;


    function __construct()
    {
        $this->currency = ['type'=>'varchar', 'label'=>'Currency', 'placeholder'=>'', 'required'=>'required'];
        $this->currency_sign = ['type'=>'varchar', 'label'=>'Currency Sign', 'placeholder'=>'', 'required'=>''];
        $this->description = ['type'=>'varchar', 'label'=>'Description', 'placeholder'=>'', 'required'=>''];

    }
}
