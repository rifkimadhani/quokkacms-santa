<?php
/**
 * Created by PageBuilder.
 * Date: 2023-05-30 16:17:57
 */

namespace App\Models;

class LiveTVForm extends BaseForm
{
    public $livetv_id;
    public $name;
    public $url_stream1;
    public $lang_code;
    public $rating;
    public $channel_number;
    public $ord;
    public $url_station_logo;
    public $livetv_category_id;
    // public $create_date;
    // public $update_date;
    // public $running_text;
    // public $pay_tv;
    // public $countryId;
    // public $status;


    function __construct($language=[], $category=[])
    {
        $this->livetv_id = ['type'=>'numeric', 'label'=>'LiveTV Id', 'readonly'=>'readonly'];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'placeholder'=>'Station Name', 'required'=>'required'];
        $this->url_stream1 = ['type'=>'varchar', 'label'=>'Url Stream', 'placeholder'=>'Url Stream', 'required'=>''];
        $this->lang_code = ['type'=>'select','label'=>'Language','required'=>'required','options'=>$language,'placeholder'=>'Choose Language'];
        $this->rating = ['type'=>'varchar', 'label'=>'Rating', 'placeholder'=>'MPAA ratings', 'default'=>'G', 'required'=>''];
        $this->channel_number = ['type'=>'numeric', 'label'=>'Channel Number', 'placeholder'=>'Channel Number', 'required'=>''];
        $this->ord = ['type'=>'numeric', 'label'=>'Ord', 'placeholder'=>'', 'required'=>''];
        $this->url_station_logo = ['type'=>'filemanager', 'label'=>'Station Logo', 'placeholder'=>'Choose Station Logo', 'required'=>''];
        $this->livetv_category_id = ['type'=>'select', 'label'=>'Category', 'options'=>$category, 'placeholder'=>'Choose StationS Category', 'required'=>''];
        // $this->create_date = ['type'=>'datetime', 'label'=>'Create Date', 'placeholder'=>'', 'required'=>''];
        // $this->update_date = ['type'=>'datetime', 'label'=>'Update Date', 'placeholder'=>'', 'required'=>''];
        // $this->running_text = ['type'=>'varchar', 'label'=>'Running Text', 'placeholder'=>'', 'required'=>''];
        // $this->pay_tv = ['type'=>'numeric', 'label'=>'Pay Tv', 'placeholder'=>'', 'required'=>''];
        // $this->countryId = ['type'=>'varchar', 'label'=>'CountryId', 'placeholder'=>'', 'required'=>''];
        // $this->status = ['type'=>'varchar', 'label'=>'Status', 'placeholder'=>'', 'required'=>''];

    }
}
