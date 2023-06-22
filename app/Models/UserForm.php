<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-14 09:28:36
 */

namespace App\Models;

class UserForm extends BaseForm
{
    public $user_id;
    public $is_block;
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


    function __construct()
    {
        $this->user_id = ['type'=>'numeric', 'label'=>'User Id', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->is_block = ['type'=>'checkbox', 'label'=>'Is Block', 'placeholder'=>'', 'required'=>''];
        $this->username = ['type'=>'varchar', 'label'=>'Username', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->hash = ['type'=>'varchar', 'label'=>'Hash', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->salt = ['type'=>'varchar', 'label'=>'Salt', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->email = ['type'=>'varchar', 'label'=>'Email', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->email_state = ['type'=>'numeric', 'label'=>'Email State', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->email_code_count = ['type'=>'numeric', 'label'=>'Email Code Count', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->email_code = ['type'=>'varchar', 'label'=>'Email Code', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->email_code_exp = ['type'=>'datetime', 'label'=>'Email Code Exp', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->email_code_resend = ['type'=>'datetime', 'label'=>'Email Code Resend', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->msisdn = ['type'=>'varchar', 'label'=>'Msisdn', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->msisdn_state = ['type'=>'numeric', 'label'=>'Msisdn State', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->msisdn_code_count = ['type'=>'numeric', 'label'=>'Msisdn Code Count', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->msisdn_code = ['type'=>'varchar', 'label'=>'Msisdn Code', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->msisdn_code_exp = ['type'=>'datetime', 'label'=>'Msisdn Code Exp', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->msisdn_code_resend = ['type'=>'datetime', 'label'=>'Msisdn Code Resend', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->facebookId = ['type'=>'varchar', 'label'=>'FacebookId', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->googleId = ['type'=>'varchar', 'label'=>'GoogleId', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->instagram_user_id = ['type'=>'varchar', 'label'=>'Instagram User Id', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->instagram_id = ['type'=>'varchar', 'label'=>'Instagram Id', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];

    }
}
