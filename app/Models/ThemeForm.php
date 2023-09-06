<?php
/**
 * Created by PageBuilder.
 * Date: 2023-04-04 14:18:55
 */

namespace App\Models;

class ThemeForm extends BaseForm
{
    public $set_as_default;
    public $clone_theme_id;
    public $theme_id;
    public $name;

    function __construct($clone=[])
    {
        $this->set_as_default = ['type'=>'checkbox','label'=>'Set as default'];
        $this->clone_theme_id = ['type'=>'select','label'=>'Clone from theme','options'=>$clone,'placeholder'=>'choose one'];
        $this->theme_id = ['type'=>'varchar', 'label'=>'Theme Id (autonumber)', 'readonly'=>'readonly'];
        $this->name = ['type'=>'varchar', 'label'=>'Name', 'required'=>'required'];
    }

    /**
     * Utk edit theme (rubah nama), tdk perlu lagi field clone
     */
    public function removeCloneTheme(){
        unset($this->clone_theme_id);
    }

    public function removeSetAsDefault(){
        unset($this->set_as_default);
    }

    public function setDefault($value){
        $this->set_as_default['value'] = $value;
    }
}
