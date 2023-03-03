<?php
/**
 * Created by PageBuilder.
 * Date: 2023-03-01 10:20:34
 */

namespace App\Models;

class AppForm extends BaseForm
{
    // public $app_id;
    // public $version_code;
    public $apkfile;
    public $filesize;
    public $filename;

    function __construct()
	{
        $this->apkfile = ['type'=>'apkfile','label'=>'File APK (.apk)','required'=>'required','min'=>0,'max'=>100,'default'=>null,'placeholder'=>'Masukkan App ID Disini'];
        $this->filesize = ['type'=>'varchar','label'=>'File Size','required'=>'required','min'=>0,'max'=>100,'default'=>null,'placeholder'=>'[autofill]'];
        $this->filename = ['type'=>'varchar','label'=>'File Name','required'=>'required','min'=>0,'max'=>100,'default'=>null,'placeholder'=>'[autofill]'];
    }
}
