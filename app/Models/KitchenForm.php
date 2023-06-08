<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-08 12:02:59
 */

namespace App\Models;

class KitchenForm extends BaseForm
{
    public $kitchen_id;
    public $name;
    public $opening_hours;
    public $food_type;
    public $url_image_background;
    public $delivery_fee;
    public $currency;
    public $currency_sign;
    public $percent_service_charge;
    public $percent_tax;


    function __construct()
    {
        $this->kitchen_id = ['type'=>'numeric', 'label'=>'Kitchen Id', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'placeholder'=>'', 'required'=>''];
        $this->opening_hours = ['type'=>'varchar', 'label'=>'Opening Hours', 'placeholder'=>'', 'required'=>''];
        $this->food_type = ['type'=>'varchar', 'label'=>'Food Type', 'placeholder'=>'', 'required'=>''];
        $this->url_image_background = ['type'=>'varchar', 'label'=>'Url Image Background', 'placeholder'=>'', 'required'=>''];
        $this->delivery_fee = ['type'=>'varchar', 'label'=>'Delivery Fee', 'placeholder'=>'', 'required'=>''];
        $this->currency = ['type'=>'varchar', 'label'=>'Currency', 'placeholder'=>'', 'required'=>''];
        $this->currency_sign = ['type'=>'varchar', 'label'=>'Currency Sign', 'placeholder'=>'', 'required'=>''];
        $this->percent_service_charge = ['type'=>'varchar', 'label'=>'Percent Service Charge', 'placeholder'=>'', 'required'=>''];
        $this->percent_tax = ['type'=>'varchar', 'label'=>'Percent Tax', 'placeholder'=>'', 'required'=>''];

    }
}
