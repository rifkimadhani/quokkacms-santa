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
        $htmlForm = $this->render($dialogTitle, $formId, $form, $action, $data);
        return $this->renderPlainDialog($formId, $htmlForm);

//        return <<< HTML
//<div class="modal fade dialog{$formId}" tabindex="-1" role="dialog" aria-labelledby="newModalLabel" aria-hidden="true">
//<div class="modal-dialog modal-lg flipInX animated" role="document">
//    <div class="modal-content">
//      <div class="modal-header">
//        <h5 class="modal-title" id="exampleModalLabel">{$dialogTitle}</h5>
//        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
//          <span aria-hidden="true">&times;</span>
//        </button>
//      </div>
//      <div class="modal-body">
//            {$htmlForm}
//      </div>
//    </div>
//</div>
//</div>
//HTML;
    }

    /**
     * Hanya render dialog container saja, tanpa isi.
     * ini di pergunakan utk form edit
     *
     * @param $dialogTitle
     * @param $formId
     * @param string $htmlContent
     * @return string
     */
    function renderPlainDialog($formId, $htmlContent=''){
        return <<< HTML
<div class="modal fade dialog{$formId}" tabindex="-1" role="dialog" aria-hidden="true">
    {$htmlContent}
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
    function render($dialogTitle, $formId, $form, $action, $data=[]){
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

                case 'select':
                    $element = $this->renderSelect($item, $attr);
                    break;

                default:
                    $element = '';
                    break;
            }

            $inputElement .= $element;
        }

        return <<< HTML
<div class="modal-dialog modal-lg flipInX animated" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{$dialogTitle}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form id="{$formId}" action="{$action}" method="post" enctype="multipart/form-data">
            {$inputElement}
            <div class="modal-footer">
                <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success btn-newformsubmit">Submit</button>
            </div>
        </form>

      </div>
    </div>
</div>
</div>
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
        <input name='{$item}' type='text' id='{$item}' class=form-control {$attr} {$required} {$readonly}>
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
        <textarea name='{$item}' id='{$item}' class=form-control {$attr} {$required} {$readonly}>{$message}</textarea>
    </div>
HTML;
    }

    function renderHidden($item, $value){
        $value = $this->getAndUnset($value, 'value');
        return <<< HTML
        <input name='{$item}' id='{$item}' type='hidden' value='{$value}'>
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

    function renderSelect($item, $value){
        $label = $this->getAndUnset($value, 'label');

        //semua attr yg di pakai harus di buat disini sebelum buildAttribute
        $required = $this->getAndUnset($value, 'required');
        $readonly = $this->getAndUnset($value, 'readonly');
        $valueData = $this->getAndUnset($value, 'value');
        $options = $this->getAndUnset($value, 'options'); //berisikan daftar list yg bisa di pilih
        $placeholder = $this->getAndUnset($value, 'placeholder');

        $attr = $this->buildAttribute($value);

        //build options utk select
        $htmlOptions = "<option value=''>{$placeholder}</option>";
        foreach ($options as $opt){
            $id = $opt['id'];
            $text = $opt['value'];

            //apabila value adalah pilihan maka set selected
            if ($id==$valueData) $selected = 'selected'; else $selected='';

            $htmlOptions .= "<option value='{$id}' {$selected}>{$text}</option>";
        }

        return <<< HTML
        <div class="form-group">
            <label class="col-form-label"><b>{$label}</b></label>
            <select name='{$item}' id='{$item}' class='form-control' {$attr} {$required} {$readonly}>
                {$htmlOptions}
            </select>
        </div>
HTML;


//        if(count($item['default'])> 0 )
//        {
//            if($item['placeholder'])
//            {
//                echo "<option value=''>{$item['placeholder']}</option>";
//            }
//            foreach ($item['default'] as $key => $valueoption)
//            {
//
//                $selected = false;
//                $disabled = '';
//                if($valueoption['id'] == $value)$selected='selected';
//                if(isset($valueoption['status_select']) && $valueoption['status_select'] == 'disabled' && $selected !='selected')$disabled='disabled=disabled';
//                if(isset($valueoption['data']))
//                {
//                    if(gettype($valueoption['data']) !== 'string')
//                    {
//                        $data = json_encode($valueoption['data'],JSON_HEX_APOS);
//                    }
//                    else
//                    {
//                        $data = $valueoption['data'];
//                    }
//                    echo "<option value='{$valueoption['id']}' {$selected} data-src='{$data}' label='{$valueoption['value']}' {$disabled}>{$valueoption['value']}</option>";
//                }
//                else
//                {
//                    echo "<option value='{$valueoption['id']}' {$selected} label='{$valueoption['value']}' {$disabled}>{$valueoption['value']}</option>";
//                }
//            }
//        }

    }



    /**
     * Ambil value dari array kemudian unset.
     *
     * @param $data
     * @param $key
     * @return string
     */
    function getAndUnset(&$data, $key) {
        if (isset($data[$key])) $value = $data[$key]; else $value = '';
        unset($data[$key]);
        return $value;
    }

    /**
     * buat attribut yg akan di pakai utk tag dari array
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