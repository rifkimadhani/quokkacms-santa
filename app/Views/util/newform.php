<?php //defined('BASEPATH') OR exit('No direct script access allowed');
$router = service('router');
$currentctrl = $router->controllerName();

//  $currentctrl = $this->router->fetch_class();
  if(isset($this->headertitle))
  {
    $title = $this->headertitle;
  }
  else
  {
    $title = $currentctrl;
  } 
?>
<style>
.select2 {
width:100%!important;
}
.modal{
  overflow-y:auto;
}
.displayimagefilemanager{
  display: relative;
  max-width:230px;
  max-height:230px;
  width: auto;
  height: auto;
  vertical-align:middle;
  margin-top:20px;
}
</style>
<div class="modal-dialog modal-lg flipInX animated" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">NEW <?php echo strtoupper($title);?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

      	<form id="newForm" action="<?= base_url($currentctrl.'/create') ?>" method="post" enctype="multipart/form-data">
    		  <?php 
            if(isset($metadata))
            {
                foreach ($metadata as $key => $item) 
                {
                    if (empty($item['readonly'])) $readonly=''; else $readonly = 'readonly';

                    $label = strtoupper($key);
                  echo'<div class="form-group">';
                  if($item['type'] == 'varchar')
                  {
                    $style='';if(isset($item['style']))$style=$item["style"];
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=text minlength='{$item["min"]}' maxlength='{$item["max"]}' value='{$item["default"]}' style='{$style}' placeholder='{$item["placeholder"]}' autocomplete=off class=form-control name='{$key}' {$item["required"]} {$readonly}>";
                  }
                  if($item['type'] == 'textarea')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<textarea  rows=5 cols=100 value='{$item["default"]}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}></textarea>";
                  }
                  if($item['type'] == 'hidden')
                  {
                    echo"<input type=hidden id='{$key}' value='{$item["default"]}' class=form-control name='{$key}'>";
                  }
                  if($item['type'] == 'password')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=password id='{$key}' minlength='{$item["min"]}' maxlength='{$item["max"]}' pattern='{$item["pattern"]}' value='{$item["default"]}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' title='{$item["title"]}' {$item["required"]}>";
                    echo"<span toggle='#{$key}' class='fa fa-fw fa-eye field-icon toggle-password'></span>";
                  }
                  if($item['type'] == 'email')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=email minlength='{$item["min"]}' maxlength='{$item["max"]}' value='{$item["default"]}' placeholder='{$item["placeholder"]}' autocomplete=off class=form-control name='{$key}' {$item["required"]}>";
                  }
                  if($item['type'] == 'tel')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=tel minlength='{$item["min"]}' maxlength='{$item["max"]}' value='{$item["default"]}' placeholder='{$item["placeholder"]}' autocomplete=off class=form-control name='{$key}' {$item["required"]}>";
                  }
                  if($item['type'] == 'text')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<textarea value='{$item["default"]}' maxlength='{$item["maxlength"]}' cols=100 rows='{$item["rows"]}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}></textarea>";
                  }
                  if($item['type'] == 'datetime')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=datetime-local minlength='{$item["min"]}' maxlength='{$item["max"]}' value='{$item["default"]}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>";
                  }
                  if($item['type'] == 'date')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=date minlength='{$item["min"]}' maxlength='{$item["max"]}' value='{$item["default"]}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>";
                  }
                  if($item['type'] == 'number')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=number min='{$item["min"]}' max='{$item["max"]}' value='{$item["default"]}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>";
                  }
                  if($item['type'] == 'float')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=number min='{$item["min"]}' step='0.01' max='{$item["max"]}' value='{$item["default"]}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>";
                  }
                  if($item['type'] == 'imagefile')
                  {
                    if($item['label']!='')echo"<label class='col-form-label {$key}'><b>{$item['label']}</b></label>";
                    echo"<input type=file accept='image/*' class=form-control-file name='{$key}' {$item["required"]}>";
                  }
                  if($item['type'] == 'filemanager')
                  {
                    if($item['label']!='')echo"<label class='col-form-label {$key}'><b>{$item['label']}</b></label>";
                    echo"<div class='input-group' style='width:100%'>
                      <input type='text' name='{$key}'  id='{$key}' class='form-control' placeholder='Click browse to upload'  readonly autocomplete='off' >
                      <div class='input-group-addon' style='cursor:pointer' data-id='{$key}'>Browse</div>
                    </div>
                    <div class='images-preview'></div>";
                  }
                  if($item['type'] == 'apkmanager')
                  {
                    if($item['label']!='')echo"<label class='col-form-label {$key}'><b>{$item['label']}</b></label>";
                    echo"<div class='input-group' style='width:100%'>
                      <input type='text' name='{$key}'  id='{$key}' class='form-control' placeholder='Click browse to upload'  readonly autocomplete='off' >
                      <div class='input-group-addon' style='cursor:pointer' data-id='{$key}'>Browse</div>
                    </div>";
                  }
                  if($item['type'] == 'videofile')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=file accept='video/*' class=form-control name='{$key}' {$item["required"]}>";
                  }
                  if($item['type'] == 'apkfile')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=file accept=\".apk\" class=form-control-file name='{$key}' {$item["required"]}>";
                  }
                  if($item['type'] == 'linkurl')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=url class=form-control name='{$key}' placeholder='{$item["placeholder"]}' {$item["required"]}>";
                  }
                  if($item['type'] == 'status')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<select class=form-control id='{$key}' name='{$key}'>";
                    echo"<option value='0'>Disabled</option>";
                    echo"<option value='1' selected>Enabled</option>";
                    
                    echo"</select>";
                  }
                  if($item['type'] == 'select')
                  {
                    echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<select class='form-control' id='{$key}' name='{$key}' {$item["required"]}>";
                    if($item['placeholder'])
                    {
                      echo "<option value='' disabled selected >{$item['placeholder']}</option>";
                    }
                    if(count($item['default'])> 0 )
                    {
                      
                      foreach ($item['default'] as $key => $value) 
                      {
                        $disabled = '';
                        if(isset($value['status_select']) && $value['status_select'] == 'disabled')$disabled='disabled=disabled';
                        if(isset($value['data']))
                        {
                          if(gettype($value['data']) !== 'string')
                          {
                            $data = json_encode($value['data'],JSON_HEX_APOS);
                          }
                          else
                          {
                            $data = $value['data']; 
                          }
                          echo "<option value='{$value['id']}'  label='{$value['value']}' data-src='{$data}' {$disabled}>{$value['value']}</option>";
                        }
                        else
                        {
                          echo "<option value='{$value['id']}' label='{$value['value']}' {$disabled}>{$value['value']}</option>";
                        }
                        
                      }
                    }
                    echo'</select>';
                  }
                  if($item['type'] == 'nestedselect')
                  {
                    echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<select class='form-control' id='{$key}' name='{$key}' {$item["required"]}>";
                    echo $item['default'];
                    echo'</select>';
                  }
                  if($item['type'] == 'select2single')
                  {
                    echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<select class='js-example-basic-single form-control' id='{$key}' name='{$key}' {$item['required']}>";
                    echo "<option></option>";
                    if(count($item['default'])> 0 )
                    {
                      foreach ($item['default'] as $key => $value) 
                      {
                        $disabled = '';
                        if(isset($value['status_select']) && $value['status_select'] == 'disabled')$disabled='disabled=disabled';
                        echo "<option value='{$value['id']}'  label='{$value['value']}'  {$disabled} >{$value['value']}</option>";
                      }
                    }
                    echo'</select>';
                  }
                  if($item['type'] == 'select2multiple')
                  {
                    echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<select class='js-example-basic-multiple js-states form-control' id='{$key}' name='{$key}[]' multiple='multiple' {$item['required']}>";
                    if(count($item['default'])> 0 )
                    {
                      foreach ($item['default'] as $key => $value) 
                      {
                        echo "<option value='{$value['id']}'  label='{$value['value']}'>{$value['value']}</option>";
                      }
                    }
                    echo'</select>';
                  }
                  if($item['type'] == 'checkbox')
                  {
                    echo"<label class='col-form-label'><b>{$item['label']}</b></label></br>";
                    foreach ($item['default'] as  $value) 
                    {
                      echo"<input type='checkbox' value='{$value}'  checked name='{$key}[]' ><label class='form-check-label' for='inlineCheckbox1'>{$value}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>";
                    }
                  }
                  if($item['type'] == 'radio')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label></br>";
                    foreach ($item['default'] as  $value) 
                    {
                      echo"<label class='radio-inline'><input type='radio' name='{$key}' {$value['checked']} value='{$value['id']}'>&nbsp;&nbsp;{$value['value']}</label>&nbsp;&nbsp;&nbsp;";
                    }
                  }
                  if($item['type'] == 'iconpicker')
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<div class='input-group' style='width:100%'>
                        <input type='search' name='{$key}' class='form-control search-box icon-class-input' placeholder='{$item["placeholder"]}' {$item["required"]} autocomplete='off' readonly='true'>
                        <div class='input-group-addon picker-button' style='cursor:pointer'><i class='fa fa-search'></i></div>
                    </div>";
                  }
                  echo "</div>";
                }
            }
          ?>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success btn-newformsubmit">Submit</button>
          </div>
    		</form>
      </div>
    </div>
</div>





