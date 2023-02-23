<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

namespace App\Models;
use CodeIgniter\Model;

class SubscriberFormModel extends Model
{
    public $salutation;
    public $name;
    public $last_name;
    public $country;
    public $age_bracket;
    public $diet;
    public $room_id;
    public $theme_id;
    public $package_id;
    
    public function __construct($room=[],$theme=[],$package=[])
	{
        $this->salutation        = ['type'=>'varchar','label'=>'Salutation','required'=>'required','min'=>0,'max'=>100,'default'=>null,'placeholder'=>'Masukkan Salutation Disini (Tuan,Nyonya,Nona)'];
        $this->name        = ['type'=>'varchar','label'=>'First Name','required'=>'required','min'=>0,'max'=>100,'default'=>null,'placeholder'=>'Masukkan Nama Pelanggan Disini'];
        $this->last_name        = ['type'=>'varchar','label'=>'Last Name','required'=>null,'min'=>0,'max'=>100,'default'=>null,'placeholder'=>'Masukkan Nama Pelanggan Disini'];
        $this->country        = ['type'=>'varchar','label'=>'Origin Country','required'=>null,'min'=>0,'max'=>100,'default'=>null,'placeholder'=>'Masukkan Negara Asal Disini'];
        $this->age_bracket    = ['type'=>'select','label'=>'Age Bracket','required'=>null,'min'=>0,'max'=>100,'default'=>[['id'=>'17-30','value'=>'17 - 30 Year'],['id'=>'31-50','value'=>'31 - 50 Year'],['id'=>'51-70','value'=>'51 - 70 Year'],['id'=>'>70','value'=>'> 70 Year']],'placeholder'=>'Pilih Age Bracket Disini'];
        $this->diet    = ['type'=>'select2multiple','label'=>'Meal Diet','required'=>'required','min'=>0,'max'=>100,'default'=>[ ['id'=>'HALAL','value'=>'Halal'],['id'=>'VEGETARIAN','value'=>'Vegetarian'],['id'=>'NOBEEF','value'=>'No Beef'],['id'=>'NOSEAFOOD','value'=>'No Seafood']],'placeholder'=>'Pilih Meal Diet Disini'];
        
        $this->room_id    = ['type'=>'select2multiple','label'=>'Room','required'=>'required','min'=>0,'max'=>100,'default'=>$room,'placeholder'=>'Pilih Room Disini'];
        $this->theme_id    = ['type'=>'select','label'=>'Theme','required'=>null,'min'=>0,'max'=>100,'default'=>$theme,'placeholder'=>'Pilih Thema Disini,Jika Tidak Dipilih Nilai Default Akan Diterapkan'];
        $this->package_id  = ['type'=>'select','label'=>'Package','required'=>null,'min'=>0,'max'=>100,'default'=>$package,'placeholder'=>'Pilih Live TV Package Disini,Jika Tidak Dipilih Nilai Default Akan Diterapkan'];
    }
}
