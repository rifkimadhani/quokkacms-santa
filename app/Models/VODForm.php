<?php
/**
 * Created by PageBuilder.
 * Date: 2023-05-10 11:42:27
 */

namespace App\Models;

class VODForm extends BaseForm
{
    public $vod_id;
    public $genre;
    public $title;
    public $description;
    public $rating_value;
    public $rating_count;
    // public $image_poster;
    public $url_poster;
    public $url_stream1;
    public $url_trailer;
    public $duration;
    public $currency;
    public $price;
    // public $path_poster;
    // public $path_trailer;
    // public $path_stream1;
    public $year_release;
    
    public $production;
    public $mpaa_rating;
    public $lang_code;
    


    const CURRENCY = [    
        ['id'=>'IDR', 'value'=>'IDR'],
        ['id'=>'SGD', 'value'=>'SGD'],
        ['id'=>'USD', 'value'=>'USD'],
        ['id'=>'EUR', 'value'=>'EUR'],
        ['id'=>'JPY', 'value'=>'JPY'],
        ['id'=>'CNY', 'value'=>'CNY'],
        ['id'=>'AUD', 'value'=>'AUD'],
        ['id'=>'CAD', 'value'=>'CAD'],
        ['id'=>'CHF', 'value'=>'CHF'],
        ['id'=>'HKD', 'value'=>'HKD'],
        ['id'=>'NZD', 'value'=>'NZD']
    ];



    function __construct($genres=[], $selectedGenres = [])
    // function __construct($selected=[])
    {
        $this->vod_id = ['type'=>'numeric', 'label'=>'Vod Id', 'readonly'=>'readonly'];
        $this->genre = ['type'=>'select-multiple','label'=>'Genre','required'=>false,'options'=>$genres, 'value'=>$selectedGenres ,'placeholder'=>'Pilih Genre Disini'];
        $this->title = ['type'=>'varchar', 'label'=>'Title', 'placeholder'=>'', 'required'=>''];
        $this->description = ['type'=>'text', 'label'=>'Description', 'placeholder'=>'', 'required'=>''];
        $this->rating_value = ['type'=>'numeric', 'label'=>'Rating Value (0-100)', 'min'=>0,'max'=>100, 'placeholder'=>''];
        $this->rating_count = ['type'=>'numeric', 'label'=>'Rating Count', 'placeholder'=>''];
        // $this->image_poster = ['type'=>'varchar', 'label'=>'Image Poster', 'placeholder'=>'', 'required'=>''];
        $this->url_poster = ['type'=>'filemanager', 'label'=>'Url Poster', 'placeholder'=>'Movie Poster'];
        $this->url_stream1 = ['type'=>'filemanager', 'label'=>'Url Stream1', 'placeholder'=>'Stream'];
        $this->url_trailer = ['type'=>'filemanager', 'label'=>'Url Trailer', 'placeholder'=>'Trailer'];
        $this->duration = ['type'=>'numeric', 'label'=>'Duration (minutes)', 'placeholder'=>''];
        $this->currency = ['type'=>'select', 'label'=>'Currency','options'=>self::CURRENCY,'default'=>'IDR', 'placeholder'=>'Select Currency', 'required'=>''];
        $this->price = ['type'=>'numeric', 'label'=>'Price', 'placeholder'=>'', 'required'=>''];
        // $this->path_poster = ['type'=>'varchar', 'label'=>'Path Poster', 'placeholder'=>'', 'required'=>''];
        // $this->path_trailer = ['type'=>'varchar', 'label'=>'Path Trailer', 'placeholder'=>'', 'required'=>''];
        // $this->path_stream1 = ['type'=>'varchar', 'label'=>'Path Stream1', 'placeholder'=>'', 'required'=>''];
        $this->year_release = ['type'=>'numeric', 'label'=>'Year Release', 'min'=>1980, 'placeholder'=>'', 'required'=>''];
        $this->production = ['type'=>'varchar', 'label'=>'Production', 'placeholder'=>''];
        $this->mpaa_rating = ['type'=>'varchar', 'label'=>'Mpaa Rating', 'placeholder'=>''];
        $this->lang_code = ['type'=>'varchar', 'label'=>'Lang Code', 'placeholder'=>''];

    }
}
