<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-05 10:36:36
 */

namespace App\Models;

class LiveTvCategoryForm extends BaseForm
{
    public $livetv_category_id;
    public $category;
    public $create_date;
    public $update_date;


    function __construct()
    {
        $this->livetv_category_id = ['type'=>'numeric', 'label'=>'Livetv Category Id', 'placeholder'=>'', 'required'=>'', 'readonly'=>'readonly'];
        $this->category = ['type'=>'varchar', 'label'=>'Category', 'placeholder'=>'', 'required'=>''];
//        $this->create_date = ['type'=>'datetime', 'label'=>'Create Date', 'placeholder'=>'', 'required'=>''];
//        $this->update_date = ['type'=>'datetime', 'label'=>'Update Date', 'placeholder'=>'', 'required'=>''];
    }
}
