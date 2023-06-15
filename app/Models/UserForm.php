<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-14 09:28:36
 */

namespace App\Models;

class UserForm extends BaseForm
{
    public $user_id;
    public $username;
    public $hash;
    public $salt;
    public $email;
    public $email_state;
    public $email_code_count;
    public $email_code;
    public $email_code_exp;
    public $email_code_resend;
    public $msisdn;
    public $msisdn_state;
    public $msisdn_code_count;
    public $msisdn_code;
    public $msisdn_code_exp;
    public $msisdn_code_resend;
    public $facebookId;
    public $googleId;
    public $instagram_user_id;
    public $instagram_id;
    public $is_block;
    public $create_date;
    public $update_date;


    function __construct()
    {
        $this->user_id = ['type'=>'numeric', 'label'=>'User Id', 'placeholder'=>'', 'required'=>''];
        $this->username = ['type'=>'varchar', 'label'=>'Username', 'placeholder'=>'', 'required'=>''];
        $this->hash = ['type'=>'varchar', 'label'=>'Hash', 'placeholder'=>'', 'required'=>''];
        $this->salt = ['type'=>'varchar', 'label'=>'Salt', 'placeholder'=>'', 'required'=>''];
        $this->email = ['type'=>'varchar', 'label'=>'Email', 'placeholder'=>'', 'required'=>''];
        $this->email_state = ['type'=>'numeric', 'label'=>'Email State', 'placeholder'=>'', 'required'=>''];
        $this->email_code_count = ['type'=>'numeric', 'label'=>'Email Code Count', 'placeholder'=>'', 'required'=>''];
        $this->email_code = ['type'=>'varchar', 'label'=>'Email Code', 'placeholder'=>'', 'required'=>''];
        $this->email_code_exp = ['type'=>'datetime', 'label'=>'Email Code Exp', 'placeholder'=>'', 'required'=>''];
        $this->email_code_resend = ['type'=>'datetime', 'label'=>'Email Code Resend', 'placeholder'=>'', 'required'=>''];
        $this->msisdn = ['type'=>'varchar', 'label'=>'Msisdn', 'placeholder'=>'', 'required'=>''];
        $this->msisdn_state = ['type'=>'numeric', 'label'=>'Msisdn State', 'placeholder'=>'', 'required'=>''];
        $this->msisdn_code_count = ['type'=>'numeric', 'label'=>'Msisdn Code Count', 'placeholder'=>'', 'required'=>''];
        $this->msisdn_code = ['type'=>'varchar', 'label'=>'Msisdn Code', 'placeholder'=>'', 'required'=>''];
        $this->msisdn_code_exp = ['type'=>'datetime', 'label'=>'Msisdn Code Exp', 'placeholder'=>'', 'required'=>''];
        $this->msisdn_code_resend = ['type'=>'datetime', 'label'=>'Msisdn Code Resend', 'placeholder'=>'', 'required'=>''];
        $this->facebookId = ['type'=>'varchar', 'label'=>'FacebookId', 'placeholder'=>'', 'required'=>''];
        $this->googleId = ['type'=>'varchar', 'label'=>'GoogleId', 'placeholder'=>'', 'required'=>''];
        $this->instagram_user_id = ['type'=>'varchar', 'label'=>'Instagram User Id', 'placeholder'=>'', 'required'=>''];
        $this->instagram_id = ['type'=>'varchar', 'label'=>'Instagram Id', 'placeholder'=>'', 'required'=>''];
        $this->is_block = ['type'=>'numeric', 'label'=>'Is Block', 'placeholder'=>'', 'required'=>''];
        $this->create_date = ['type'=>'datetime', 'label'=>'Create Date', 'placeholder'=>'', 'required'=>''];
        $this->update_date = ['type'=>'datetime', 'label'=>'Update Date', 'placeholder'=>'', 'required'=>''];

    }
}
