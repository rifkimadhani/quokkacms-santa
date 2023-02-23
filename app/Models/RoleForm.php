<?php
/**
 * Created by PageBuilder.
 * Date: 2023-02-23 13:07:39
 */

namespace App\Models;

class RoleForm extends BaseForm
{
    public $role_id;
    public $role_name;


    function __construct()
	{
        $this->role_id = ['type'=>'varchar', 'label'=>'role_id', 'placeholder'=>'', 'required'=>'required'];
        $this->role_name = ['type'=>'varchar', 'label'=>'role_name', 'placeholder'=>'', 'required'=>'required'];

    }
}
