<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
	$currentctrl = $this->router->fetch_class(); 
?>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Confirm Delete</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form id="deleteForm" action="<?= base_url($currentctrl.'/delete') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" id="columnName" name="columnName">
            <input type="hidden" id="columnValue" name="columnValue">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-newformsubmit full-right">Delete</button>
            </div>
        </form>
      </div>
    </div>
</div>





