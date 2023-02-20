<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 12:31 PM
 */

namespace App\Models;

class AdminForm extends BaseForm
{
    public $admin_id;
    public $role_id;
    public $username;
    public $password;
    public $password2;

    public function __construct( $role=[])
    {
        $this->admin_id = ['type'=>'hidden'];
        $this->role_id = ['type'=>'select','label'=>'Role','options'=>$role,'placeholder'=>'---', 'required'=>'required'];
        $this->username = ['type'=>'varchar', 'label'=>'Username', 'required'=>'required'];
        $this->password = ['type'=>'password', 'label'=>'Password', 'required'=>'required'];
        $this->password2 = ['type'=>'password', 'label'=>'Password retype', 'required'=>'required'];
    }

}