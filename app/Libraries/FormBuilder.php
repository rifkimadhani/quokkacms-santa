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
    function renderDialog($dialogTitle, $formId, $form, $action, $data=[]){
        $htmlForm = $this->render($dialogTitle, $formId, $form, $action, $data);
        return $this->renderPlainDialog($formId, $htmlForm);
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

        $inputElement = $this->renderBody($dialogTitle, $formId, $form, $action, $data);

        return <<<HTML
        <div class="modal-dialog modal-lg modal-dialog-popout" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">{$dialogTitle}</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
                {$inputElement}
            </div>
        </div>
HTML;
    }

    /**
     * hanya render form saja, tanpa ada ada dialog
     *
     * @param $dialogTitle
     * @param $formId
     * @param $form
     * @param $action
     * @param array $data
     * @return string
     */
    function renderBody($dialogTitle, $formId, $form, $action, $data=[]){
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

                case 'select-multiple':
                    $element = $this->renderSelectMultiple($item, $attr);
                    break;

                case 'filemanager':
                    $element = $this->renderFilemanager($formId, $item, $attr);
                    break;

                case 'numeric':
                    $element = $this->renderNumeric($formId, $item, $attr);
                    break;
                case 'apkfile':
                    $element = $this->renderApkFile($item, $attr);
                    break;

                case 'datetime':
                    $element = $this->renderDatetime($formId, $item, $attr);
                    break;

                case 'checkbox':
                    $element = $this->renderCheckbox($formId, $item, $attr);
                    break;

                default:
                    $element = '';
                    break;
            }

            $inputElement .= $element;
        }

        return <<< HTML
        <form id="{$formId}" action="{$action}" method="post" enctype="multipart/form-data">
            <div class="block-content">
                {$inputElement}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-alt-primary">
                    <i class="fa fa-check"></i> Submit
                </button>
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

    function renderSelect($item, $data){
        $label = $this->getAndUnset($data, 'label');

        //semua attr yg di pakai harus di buat disini sebelum buildAttribute
        $required = $this->getAndUnset($data, 'required');
        $readonly = $this->getAndUnset($data, 'readonly');
        $options = $this->getAndUnset($data, 'options'); //berisikan daftar list yg bisa di pilih
        $placeholder = $this->getAndUnset($data, 'placeholder');

        //ambil value dari default atau dari value
        if (isset($data['value'])){
            $value = $this->getAndUnset($data, 'value');
        } else {
            $value = $this->getAndUnset($data, 'default');
        }

        $attr = $this->buildAttribute($data);

        //build options utk select
        $htmlOptions = "<option value=''>{$placeholder}</option>";
        foreach ($options as $opt){
            $id = $opt['id'];
            $text = $opt['value'];

            //apabila value adalah pilihan maka set selected
            if ($id==$value) $selected = 'selected'; else $selected='';

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
    }

    function renderSelectMultiple($item, $data){
        $label = $this->getAndUnset($data, 'label');

        //semua attr yg di pakai harus di buat disini sebelum buildAttribute
        $required = $this->getAndUnset($data, 'required');
        $readonly = $this->getAndUnset($data, 'readonly');
        $options = $this->getAndUnset($data, 'options'); //berisikan daftar list yg bisa di pilih
        $placeholder = $this->getAndUnset($data, 'placeholder');

        //ambil value dari default atau dari value
        // if (isset($data['value'])){
        //     $value = $this->getAndUnset($data, 'value');
        // } else {
        //     $value = $this->getAndUnset($data, 'default');
        // }
        if (isset($data['value'])) {
            $value = $this->getAndUnset($data, 'value');
            if (!is_array($value)) {
                $value = [$value]; // Convert to array if it's a single value
            }
        } else {
            $value = $this->getAndUnset($data, 'default');
            if (!is_array($value)) {
                $value = [$value]; // Convert to array if it's a single value
            }
        }

        $attr = $this->buildAttribute($data);

        //build options utk select
        $htmlOptions = '';//"<option value=''>{$placeholder}</option>";
        foreach ($options as $opt){
            $id = $opt['id'];
            $text = $opt['value'];

            //apabila value adalah pilihan maka set selected
            // if ($id==$value) $selected = 'selected'; else $selected='';
            $selected = '';
            if (is_array($value) && in_array($id, $value)) {
                $selected = 'selected';
            } elseif ($id == $value) {
                $selected = 'selected';
            }

            $htmlOptions .= "<option value='{$id}' label='{$text}' {$selected}>{$text}</option>";
        }

        return <<< HTML
            <div class="form-group">
                <label class="col-form-label"><b>{$label}</b></label>
                <select name='{$item}[]' id='{$item}' class='js-example-basic-multiple form-control' multiple="multiple" {$attr} {$required} {$readonly}>
                    {$htmlOptions}
                </select>
            </div>
        HTML;
    }

    function renderFilemanager($formId, $item, $data){
        $label = $this->getAndUnset($data, 'label');
        $required = $this->getAndUnset($data, 'required');
        $readonly = $this->getAndUnset($data, 'readonly');

        $attr = $this->buildAttribute($data);

        $value = $this->getAndUnset($data, 'value');

        $htmlImagePreview = '';
        if (strlen($value)>0){
            foreach(explode(',', $value) as $url)
            {
                $htmlImagePreview .= "<div class='img' style='background-image:url({$url});'><span>remove</span></div>";
            }
        }

        return <<< HTML
                <div class="form-group">
                    <label class="col-form-label"><b>{$label}</b></label>
                    <div class="input-group" style="width: 100%;">
                        <input type="hidden" id="{$formId}_{$item}">
                        <input type="text" name="{$item}" id="{$item}" class="form-control" autocomplete="off" {$readonly} {$required} {$attr}/>
                        <div class="btn btn-alt-primary input-group-addon" style="cursor: pointer;" form-id="{$formId}" input-id="{$item}">Browse</div>
                    </div>
                    <div id="images-preview-{$formId}-{$item}" class="images-preview" form-id="{$formId}" input-id="{$item}">
                        {$htmlImagePreview}
                    </div>
                </div>
        HTML;
    }

    // function renderApkFile($item, $attr) {
    //     $label = isset($attr['label']) ? "<label class='col-form-label'><b>{$attr['label']}</b></label>" : "";
    //     $required = isset($attr['required']) ? $attr['required'] : "";
    //     $input = "<input type='file' accept='.apk' class='form-control-file' name='{$item}' {$required}>";

    //     return $label . $input;
    // }
    function renderApkFile($item, $data){
        $label = $this->getAndUnset($data, 'label');
        $required = $this->getAndUnset($data, 'required');

        $attr = $this->buildAttribute($data);

        return <<< HTML
        <div class="form-group">
            <label class="col-form-label"><b>{$label}</b></label>
            <input type="file" name="{$item}" id="{$item}" accept=".apk" {$required} {$attr}>
        </div>
        HTML;
    }    

    function renderNumeric($formId, $item, $data){
        $label = $this->getAndUnset($data, 'label');
        $required = $this->getAndUnset($data, 'required');
        $readonly = $this->getAndUnset($data, 'readonly');

        $attr = $this->buildAttribute($data);

        return <<< HTML
        <div class="form-group">
            <label class='col-form-label'><b>{$label}</b></label>
            <input name='{$item}' id='{$item}' type='number' class=form-control {$attr} {$required} {$readonly}>
        </div>
HTML;
    }

    function renderDatetime($formId, $item, $data){
        $label = $this->getAndUnset($data, 'label');
        $required = $this->getAndUnset($data, 'required');
        $readonly = $this->getAndUnset($data, 'readonly');

        //conversikan value
        if(isset($data['value']))
        {
            $value = date('Y-m-d\TH:i',strtotime($data['value']));
        }
        else
        {
            $value = '';
        }

        $attr = $this->buildAttribute($data);

        return <<< HTML
            <div class="form-group">
            <label class='col-form-label'><b>{$label}</b></label>
            <input name='{$item}' id='{$item}' type=datetime-local class=form-control {$attr} {$required} {$readonly}>
            </div>
HTML;
    }

    function renderCheckbox($formId, $item, $data){
        $label = $this->getAndUnset($data, 'label');
        $required = $this->getAndUnset($data, 'required');
        $readonly = $this->getAndUnset($data, 'readonly');
        $attr = $this->buildAttribute($data);

        //set nilai value
        if (isset($data['value'])){
            $value = $data['value'];
        } else {
            $value = 0;
        }

        //apabila value>0 maka checked di activekan
        if ($value>0){
            $checked = 'checked';
        } else {
            $checked = '';
        }

        return <<< HTML
            <div class="form-group">
            <label class='col-form-label'><b>{$label}</b></label>
            <input name='{$item}' id='{$item}' type='checkbox' value='{$value}' {$checked} {$attr} {$required} {$readonly}><label class='form-check-label' for='inlineCheckbox1'></label>
            </div>
HTML;
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