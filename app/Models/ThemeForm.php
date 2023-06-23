<?php
/**
 * Created by PageBuilder.
 * Date: 2023-04-04 14:18:55
 */

namespace App\Models;

class ThemeForm extends BaseForm
{
    public $theme_id;
    public $name;

    function __construct()
    {
        $this->theme_id = ['type'=>'varchar', 'label'=>'Theme Id (autonumber', 'readonly'=>'readonly'];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'required'=>'required'];
    }
}
