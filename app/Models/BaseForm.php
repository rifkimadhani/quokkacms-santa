<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/22/2022
 * Time: 9:51 AM
 */

namespace App\Models;


use App\Libraries\FormBuilder;

class BaseForm
{
    public function renderDialog($dialogTitle, $formId, $urlAction, $data=[]){
        $builder = new FormBuilder();
        return $builder->renderDialog($dialogTitle, $formId, $this, $urlAction, $data);
    }

    public function renderPlainDialog($formId){
        $builder = new FormBuilder();
        return $builder->renderPlainDialog($formId);
    }

    public function renderForm($dialogTitle, $formId, $urlAction, $data=[]){
        $builder = new FormBuilder();
        return $builder->render($dialogTitle, $formId, $this, $urlAction, $data);
    }
}