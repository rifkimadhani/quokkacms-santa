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
    public $username;
    public $json;
    public $password;
    public $password2;

    public function __construct( $role=[], $jsonData=[])
    {
        $this->admin_id = ['type'=>'hidden'];
        $this->json = ['type' => 'select-multiple-2', 'label' => 'Roles', 'options' => $role, 'selected' => $jsonData];
        $this->username = ['type'=>'varchar', 'label'=>'Username', 'required'=>'required'];
        $this->password = ['type'=>'password', 'label'=>'Password', 'required'=>'required'];
        $this->password2 = ['type'=>'password', 'label'=>'Password retype', 'required'=>'required'];
        
    }

}