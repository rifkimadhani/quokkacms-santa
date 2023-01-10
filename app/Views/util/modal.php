<?php //defined('BASEPATH') OR exit('No direct script access allowed');

exit();


$router = service('router');
$currentctrl = $router->controllerName();

//  $currentctrl = $this->router->fetch_class();
?>
<div class="modal fade newModal" tabindex="-1" role="dialog" aria-labelledby="newModalLabel" aria-hidden="true">
    <?= view('util/newform');?>
</div>

<div class="modal fade editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">

</div>

<div class="modal fade deleteModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <?= view('util/deleteform');?>
</div>

<div class="modal fade" id="modal-checkbox" style="min-width:1200px" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="width:1000px;margin-left:-200px;">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php if(isset($this->headertitle))echo $this->headertitle; ?> OPTIONS</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row checkboxdisplay"></div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-galery" style="min-width:1200px" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="width:1000px;margin-left:-200px;">
      <div class="modal-body">
        <iframe src="./filemanager/filemanager/dialog.php" width="100%" height="600px;" style="border: 0"></iframe>
      </div>
    </div>
  </div>
</div>


