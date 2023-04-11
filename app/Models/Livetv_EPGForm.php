<?php
/**
 * Created by PageBuilder.
 * Date: 2023-04-06 09:47:23
 */

namespace App\Models;

class Livetv_EPGForm extends BaseForm
{
    public $epg_id;
    public $livetv_id;
    public $start_date;
    public $end_date;
    // public $duration;
    public $name;
    public $sinopsis;


    function __construct($livetv=[])
    {
        $this->epg_id = ['type'=>'varchar', 'label'=>'Epg ID', 'placeholder'=>'auto-generate', 'readonly'=>'readonly'];
        $this->livetv_id = ['type'=>'select', 'label'=>'Livetv', 'options'=>$livetv, 'placeholder'=>'Pilih channel', 'required'=>''];
        $this->start_date = ['type'=>'datetime', 'label'=>'Start Date', 'placeholder'=>'', 'required'=>''];
        $this->end_date = ['type'=>'datetime', 'label'=>'End Date', 'placeholder'=>'', 'required'=>''];
        // $this->duration = ['type'=>'numeric', 'label'=>'Duration', 'placeholder'=>'', 'required'=>''];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'placeholder'=>'Judul program', 'required'=>''];
        $this->sinopsis = ['type'=>'varchar', 'label'=>'Sinopsis', 'placeholder'=>'Synopsis', 'required'=>''];

    }
}
