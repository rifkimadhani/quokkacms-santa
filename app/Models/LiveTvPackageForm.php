<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-05 11:03:40
 */

namespace App\Models;

class LiveTvPackageForm extends BaseForm
{
    public $set_as_default;
    public $package_id;
    public $name;
    public $description;
//    public $url_package_logo;
//    public $price;
//    public $currency;
//    public $currency_sign;
//    public $rent_duration;
//    public $percent_tax;
//    public $url_image;
//    public $create_date;
//    public $update_date;


    function __construct()
    {
        $this->set_as_default = ['type'=>'checkbox','label'=>'Set as default'];
        $this->package_id = ['type'=>'numeric', 'label'=>'Package Id', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'placeholder'=>'', 'required'=>''];
        $this->description = ['type'=>'varchar', 'label'=>'Description', 'placeholder'=>'', 'required'=>''];
//        $this->url_package_logo = ['type'=>'varchar', 'label'=>'Url Package Logo', 'placeholder'=>'', 'required'=>''];
//        $this->price = ['type'=>'varchar', 'label'=>'Price', 'placeholder'=>'', 'required'=>''];
//        $this->currency = ['type'=>'varchar', 'label'=>'Currency', 'placeholder'=>'', 'required'=>''];
//        $this->currency_sign = ['type'=>'varchar', 'label'=>'Currency Sign', 'placeholder'=>'', 'required'=>''];
//        $this->rent_duration = ['type'=>'numeric', 'label'=>'Rent Duration', 'placeholder'=>'', 'required'=>''];
//        $this->percent_tax = ['type'=>'varchar', 'label'=>'Percent Tax', 'placeholder'=>'', 'required'=>''];
//        $this->url_image = ['type'=>'varchar', 'label'=>'Url Image', 'placeholder'=>'', 'required'=>''];
//        $this->create_date = ['type'=>'datetime', 'label'=>'Create Date', 'placeholder'=>'', 'required'=>''];
//        $this->update_date = ['type'=>'datetime', 'label'=>'Update Date', 'placeholder'=>'', 'required'=>''];

    }

    public function removeSetAsDefault(){
        unset($this->set_as_default);
    }

    /**
     * @param $value =1 --> checkbox on
     */
    public function setDefault($value){
        $this->set_as_default['value'] = $value;
    }
}
