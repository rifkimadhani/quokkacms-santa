<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
  $currentctrl = $this->router->fetch_class();
  if(isset($this->headertitle))
  {
    $title = $this->headertitle;
  }
  else
  {
    $title = $currentctrl;
  }

  //parameter btn_update_name di pass dari parent lewat compact
  //parameter btn_update_disable di pass dari parent
  //
  if (isset($btn_update_name)==false) $btn_update_name = 'Update'; //default nama button Update
  if (isset($btn_update_disable) and $btn_update_disable==true) $btnUpdateDisable = 'disabled'; else $btnUpdateDisable = '';
?>
<div class="modal-dialog modal-lg flipInX animated" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">EDIT <?php echo strtoupper($title);?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form id="editForm" action="<?= base_url($currentctrl.'/update') ?>" method="post" enctype="multipart/form-data">
          <?php 
            if(isset($metadata))
            {
              foreach ($metadata as $key => $item)  
              {
                foreach ($entity as $parentkey => $value)
                {
                  if($item['type'] == 'varchar' && ($parentkey == $key))
                  {
                    $style='';if(isset($item['style']))$style=$item["style"];
                    if (isset($item['readonly'])) $readonly = 'readonly'; else $readonly='';
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=text minlength='{$item["min"]}' maxlength='{$item["max"]}' value='{$value}' style='{$style}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]} {$readonly}>";
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'password' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=password id='{$key}' pattern='{$item["pattern"]}' title='{$item["title"]}' minlength='{$item["min"]}' maxlength='{$item["max"]}' value='{$value}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>";
                    echo"<span toggle='#{$key}' class='fa fa-fw fa-eye field-icon toggle-password'></span>";
                    echo"</div>";
                    break;
                  }
                  if($item['type'] == 'hidden' && ($parentkey == $key))
                  {
//                    echo'<div class="form-group">';
                    echo"<input type=hidden value='{$value}' name='{$key}'>";
//                    echo"</div>";
                    break;
                  }
                  if($item['type'] == 'text' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<textarea cols=100 rows='{$item["rows"]}' maxlength='{$item["maxlength"]}' id='{$key}edit' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>{$value}</textarea>";
                    echo"</div>";
                    break;
                  }
                  if($item['type'] == 'email' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=email minlength='{$item["min"]}' maxlength='{$item["max"]}' value='{$value}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>";
                    echo"</div>";
                    break;
                  }
                  if($item['type'] == 'tel' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=tel minlength='{$item["min"]}' maxlength='{$item["max"]}' value='{$value}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>";
                    echo"</div>";
                    break;
                  }
                  if($item['type'] == 'datetime' && ($parentkey == $key))
                  {
                    if($value)
                    {
                      $tanggal = date('Y-m-d\TH:i',strtotime($value));
                    }
                    else
                    {
                      $tanggal = "";
                    }
                    
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=datetime-local minlength='{$item["min"]}' maxlength='{$item["max"]}' value='{$tanggal}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>";
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'date' && ($parentkey == $key))
                  {
                    $tanggal = date('Y-m-d',strtotime($value));
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=date minlength='{$item["min"]}' maxlength='{$item["max"]}' value='{$tanggal}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>";
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'number' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=number min='{$item["min"]}' max='{$item["max"]}' value='{$value}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>";
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'float' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=number min='{$item["min"]}' step='0.01' max='{$item["max"]}' value='{$value}' placeholder='{$item["placeholder"]}' class=form-control name='{$key}' {$item["required"]}>";
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'imagefile' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=file accept='image/*' class=form-control-file name='{$key}' {$item["required"]} value='{$value}'>";
                    echo"<br/><img src='{$value}' width='80px' height='80px'>";
                    echo "</div>";
                    break;
                  }
                  
                  if($item['type'] == 'filemanager' && ($parentkey == $key))
                  {
                    $imagefile = urldecode($value);
                    $viewimage = [];
                    if($imagefile != "" )
                    {
                      $viewimage = explode(",",$imagefile);
                    }
                    
                    $imageurl  = urldecode($value);
                    if (strpos($value,base_url()) === false) 
                    {
                      $imagefile = str_replace("{HOST-IMAGE}/",base_url(),$imagefile);
                      $imagefile = str_replace("{HOST}/",base_url(),$imagefile);
                    }
                    
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<div class='input-group' style='width:100%'>
                      <input type='text' value='{$imagefile}' name='{$key}'  id='{$key}' class='form-control' placeholder='Click browse to upload'  readonly autocomplete='off' >
                      <div class='input-group-addon' style='cursor:pointer' data-id='{$key}'>Browse</div>
                    </div><div class='images-preview'>";
                    foreach($viewimage as $imagetoshow)
                    {
                        $strreplace = str_replace("{HOST-IMAGE}/",base_url(),$imagetoshow);
                        $strreplace = str_replace("{HOST}/",base_url(),$strreplace);
                        $strreplace = str_replace("{BASE-HOST}/",base_url(),$strreplace);
                        echo "<div class='img' style='background-image:url(".$strreplace.");'><span>remove</span></div>";
                    }
                    echo "</div></div>";
                    break;
                  }
                  
                  if($item['type'] == 'videofile' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=file accept='video/*' class=form-control name='{$key}' {$item["required"]}>";
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'linkurl' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<input type=url class=form-control name='{$key}' placeholder='{$item["placeholder"]}' {$item["required"]} value='{$value}'>";
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'status' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<select class=form-control id='{$key}' name='{$key}'>";
                    echo"<option value='0'";echo $value == '0' ? 'selected':'false';echo ">Disabled</option>";
                    echo"<option value='1'";echo $value == '1' ? 'selected':'false';echo ">Enabled</option>";
                    
                    echo"</select>";
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'select' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<select class='form-control' id='{$key}' name='{$key}'>";
                    if(count($item['default'])> 0 )
                    {
                      if($item['placeholder'])
                      {
                        echo "<option value=''>{$item['placeholder']}</option>";
                      }
                      foreach ($item['default'] as $key => $valueoption) 
                      {
                        
                        $selected = false;
                        $disabled = '';
                        if($valueoption['id'] == $value)$selected='selected';
                        if(isset($valueoption['status_select']) && $valueoption['status_select'] == 'disabled' && $selected !='selected')$disabled='disabled=disabled';
                        if(isset($valueoption['data']))
                        {
                          if(gettype($valueoption['data']) !== 'string')
                          {
                            $data = json_encode($valueoption['data'],JSON_HEX_APOS);
                          }
                          else
                          {
                            $data = $valueoption['data']; 
                          }
                          echo "<option value='{$valueoption['id']}' {$selected} data-src='{$data}' label='{$valueoption['value']}' {$disabled}>{$valueoption['value']}</option>";
                        }
                        else
                        {
                          echo "<option value='{$valueoption['id']}' {$selected} label='{$valueoption['value']}' {$disabled}>{$valueoption['value']}</option>";
                        }
                      }
                    }
                    echo'</select>';
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'nestedselect' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<select class='form-control' id='{$key}' name='{$key}'>";
                    echo $item['default'];
                    echo'</select>';
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'select2single' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<select class='js-example-basic-single form-control' id='{$key}' name='{$key}'>";
                    if(count($item['default'])> 0 )
                    {
                      if($item['placeholder'])
                      {
                        echo "<option></option>";
                      }
                      foreach ($item['default'] as $key => $valueoption) 
                      {
                        $selected = false;
                        $disabled = '';
                        if($valueoption['id'] == $value)$selected='selected';
                        if(isset($valueoption['status_select']) && $valueoption['status_select'] == 'disabled' && $selected !='selected')$disabled='disabled=disabled';
                        echo "<option value='{$valueoption['id']}' {$selected} label='{$valueoption['value']}' {$disabled}>{$valueoption['value']}</option>";
                      }
                    }
                    echo'</select>';
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'select2multiple' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<select class='js-example-basic-multiple js-states  form-control' id='{$key}' name='{$key}[]' multiple='multiple'>";
                    if(count($item['default'])> 0 )
                    {
                      if($item['placeholder'])
                      {
                        echo "<option></option>";
                      }
                      foreach ($item['default'] as $key => $valueoption) 
                      {
                        $selected = false;
                        $inarray = in_array($valueoption['id'],$value);
                        if($inarray)  $selected='selected';
                        $disabled = '';
                        if($valueoption['id'] == $value)$selected='selected';
                        if(isset($valueoption['status_select']) && $valueoption['status_select'] == 'disabled' && $selected !='selected')$disabled='disabled=disabled';
                        echo "<option value='{$valueoption['id']}' {$selected} label='{$valueoption['value']}' {$disabled}>{$valueoption['value']}</option>";
                      }
                    }
                    echo'</select>';
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'checkbox' && ($parentkey == $key))
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label></br>";
                    foreach ($item['default'] as  $value) 
                    {
                      echo"<input type='checkbox' value='{$value}'  checked name='{$key}[]' ><label class='form-check-label' for='inlineCheckbox1'>{$value}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>";
                    }
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'radio' && ($parentkey == $key) )
                  {
                    echo'<div class="form-group">';
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label></br>";
                    foreach ($item['default'] as  $radiovalue) 
                    {
                      $checked = $radiovalue['id'] == $value ? 'checked':'';
                      echo"<label class='radio-inline'><input type='radio' name='{$key}' {$checked} value='{$radiovalue['id']}'>&nbsp;&nbsp;{$radiovalue['value']}</label>&nbsp;&nbsp;&nbsp;";
                    }
                    echo "</div>";
                    break;
                  }
                  if($item['type'] == 'iconpicker' && ($parentkey == $key))
                  {
                    if($item['label']!='')echo"<label class='col-form-label'><b>{$item['label']}</b></label>";
                    echo"<div class='input-group' style='width:100%'>
                        <input type='search' name='{$key}' value='{$value}' class='form-control search-box icon-class-input' placeholder='{$item["placeholder"]}' {$item["required"]} autocomplete='off' >
                        <div class='input-group-addon picker-button' style='cursor:pointer'><i class='fa fa-search'></i></div>
                    </div>";
                  }
                }
              }
            }
          ?>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary pull-right" <?=$btnUpdateDisable?> ><?=$btn_update_name?></button>
          </div>
	    </form>
      </div>
      
    </div>
</div>




