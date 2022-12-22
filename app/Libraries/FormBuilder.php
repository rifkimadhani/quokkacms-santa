<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/21/2022
 * Time: 1:11 PM
 */

namespace App\Libraries;

use CodeIgniter\Model;

/**
 * To build form with HTML
 *
 * Class FormBuilder
 * @package App\Libraries
 */
class FormBuilder
{
//    function __construct()
//    {
//
//    }

    function renderDialog($dialogTitle, $formId, $form, $action, $data=[]){
        $htmlForm = $this->render($formId, $form, $action, $data);

        return <<< HTML
<div class="modal fade dialog{$formId}" tabindex="-1" role="dialog" aria-labelledby="newModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg flipInX animated" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{$dialogTitle}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            {$htmlForm}
      </div>
    </div>
</div>
</div>
HTML;
    }

    /**
     * @param $formId
     * @param $form
     * @param $action http://192.168.2.7/alpha/auth/logincheck
     * @param array $data
     * @return string HTML
     */
    function render($formId, $form, $action, $data=[]){
//        $action = 'http://192.168.2.7/alpha/auth/logincheck';

        $inputElement = '';

        //build input html
        foreach ($form as $item => $attr){

            //skip apabila attr null
            if (is_null($attr)) continue;

//            echo "\n\nitem=$item=$attr\n\n";

            //set type & hapus type dari array
            $type = $this->getAndUnset($attr, 'type'); //if (isset($value['type'])) $type = $value['type']; $type = '';

            //pindahkan value dari data ke attr
            if (count($data)>0){
                //set value apabila ada value pada data
                if (isset($data[$item])) $attr['value'] = $data[$item];
            }

            switch ($type){
                case 'varchar':
                    $element = $this->renderVarchar($item, $attr);
                    break;

                case 'text':
                    $element = $this->renderTextarea($item, $attr);
                    break;

                case 'hidden':
                    $element = $this->renderHidden($item, $attr);
                    break;

                case 'password':
                    $element = $this->renderPassword($item, $attr);
                    break;

                default:
                    $element = '';
                    break;
            }

            $inputElement .= $element;
        }

        return <<< HTML
<form id="{$formId}" action="{$action}" method="post" enctype="multipart/form-data">
    {$inputElement}
    <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success btn-newformsubmit">Submit</button>
    </div>
</form>
HTML;
    }

    function renderVarchar($item, &$value) {
        $label = $this->getAndUnset($value, 'label');

        //required & readonly attribute yg tdk membutuhkan nama aatribute
        $required = $this->getAndUnset($value, 'required');
        $readonly = $this->getAndUnset($value, 'readonly');

        //attr di letakkan terakhir, setelah getAndunset
        $attr = $this->buildAttribute($value);

        return <<< HTML
    <div class="form-group">
        <label class='col-form-label'><b>{$label}</b></label>
        <input name='{$item}' type='text' class=form-control {$attr} {$required} {$readonly}>
    </div>
HTML;
    }

    function renderTextarea($item, $value){
        $label = $this->getAndUnset($value, 'label');
        //required & readonly attribute yg tdk membutuhkan nama aatribute
        $required = $this->getAndUnset($value, 'required');
        $readonly = $this->getAndUnset($value, 'readonly');

        $message = $this->getAndUnset($value, 'value'); //ambil value dari attr

        //attr di letakkan terakhir, setelah getAndunset
        $attr = $this->buildAttribute($value);

        return <<< HTML
    <div class="form-group">
        <label class='col-form-label'><b>{$label}</b></label>
        <textarea name='{$item}' class=form-control {$attr} {$required} {$readonly}>{$message}</textarea>
    </div>
HTML;
    }

    function renderHidden($item, $value){
        $value = $this->getAndUnset($value, 'value');
        return <<< HTML
        <input name='{$item}' type='hidden' value='{$value}'>
HTML;
    }

    function renderPassword($item, $value){
        $label = $this->getAndUnset($value, 'label');
        //required & readonly attribute yg tdk membutuhkan nama aatribute
        $required = $this->getAndUnset($value, 'required');
        $attr = $this->buildAttribute($value);

        return <<< HTML
        <div class="form-group">
            <label class="col-form-label"><b>{$label}</b></label>
                <input name="{$item}" id='{$item}' type='password' class="form-control" {$attr} {$required}>
                <span toggle='#{$item}' class='fa fa-fw fa-eye-slash field-icon toggle-password'></span>
        </div>
HTML;
    }

    /**
     * Ambil value dari array kemudian unset
     *
     * @param $data
     * @param $key
     * @return string
     */
    function getAndUnset(&$data, $key){
        if (isset($data[$key])) $value = $data[$key]; else $value = '';
        unset($data[$key]);
        return $value;
    }

    /**
     * buat string dari array
     *
     * @param $data
     * @return string
     */
    function buildAttribute($data){
        $html = '';
        foreach ($data as $attr => $value){
            $html .= "$attr='{$value}' ";
        }
        return $html;
    }
}