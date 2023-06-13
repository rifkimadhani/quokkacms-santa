<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-12 12:48:42
 */

namespace App\Models;

class RoomServiceForm extends BaseForm
{
    public $order_code;
    public $subscriber_id;
    public $room_id;
    public $kitchen_id;
    public $kitchen_name;
    public $order_date;
    public $purchase_amount;
    public $percent_service_charge;
    public $service_charge;
    public $percent_tax;
    public $tax;
    public $notes;
    public $status;
    public $payment_type;
    public $delivery_fee;


    function __construct()
    {
        $this->order_code = ['type'=>'varchar', 'label'=>'Order Code', 'placeholder'=>'', 'required'=>'required'];
        $this->subscriber_id = ['type'=>'numeric', 'label'=>'Subscriber Id', 'placeholder'=>'', 'required'=>''];
        $this->room_id = ['type'=>'numeric', 'label'=>'Room Id', 'placeholder'=>'', 'required'=>''];
        $this->kitchen_id = ['type'=>'numeric', 'label'=>'Kitchen Id', 'placeholder'=>'', 'required'=>''];
        $this->kitchen_name = ['type'=>'varchar', 'label'=>'Kitchen Name', 'placeholder'=>'', 'required'=>''];
        $this->order_date = ['type'=>'datetime', 'label'=>'Order Date', 'placeholder'=>'', 'required'=>''];
        $this->purchase_amount = ['type'=>'varchar', 'label'=>'Purchase Amount', 'placeholder'=>'', 'required'=>''];
        $this->percent_service_charge = ['type'=>'varchar', 'label'=>'Percent Service Charge', 'placeholder'=>'', 'required'=>''];
        $this->service_charge = ['type'=>'varchar', 'label'=>'Service Charge', 'placeholder'=>'', 'required'=>''];
        $this->percent_tax = ['type'=>'varchar', 'label'=>'Percent Tax', 'placeholder'=>'', 'required'=>''];
        $this->tax = ['type'=>'varchar', 'label'=>'Tax', 'placeholder'=>'', 'required'=>''];
        $this->notes = ['type'=>'varchar', 'label'=>'Notes', 'placeholder'=>'', 'required'=>''];
        $this->status = ['type'=>'varchar', 'label'=>'Status', 'placeholder'=>'', 'required'=>''];
        $this->payment_type = ['type'=>'varchar', 'label'=>'Payment Type', 'placeholder'=>'', 'required'=>''];
        $this->delivery_fee = ['type'=>'varchar', 'label'=>'Delivery Fee', 'placeholder'=>'', 'required'=>''];

    }
}
