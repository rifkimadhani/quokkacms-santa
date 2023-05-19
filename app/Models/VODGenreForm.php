<?php
/**
 * Created by PageBuilder.
 * Date: 2023-05-11 13:48:16
 */

namespace App\Models;

class VODGenreForm extends BaseForm
{
    public $genre_id;
    public $genre;
    // public $create_date;
    // public $update_date;


    function __construct()
    {
        $this->genre_id = ['type'=>'numeric', 'label'=>'Genre ID', 'readonly'=>'readonly'];
        $this->genre = ['type'=>'varchar', 'label'=>'Genre', 'placeholder'=>'', 'required'=>''];
        // $this->create_date = ['type'=>'datetime', 'label'=>'Create Date', 'placeholder'=>'', 'required'=>''];
        // $this->update_date = ['type'=>'datetime', 'label'=>'Update Date', 'placeholder'=>'', 'required'=>''];

    }
}
