<?php

    use App\Libraries\Dialog;
?>

<?php if ($oneactive->emergency_history_id == 0) : ?>
    <div class="alert alert-success alert-dismissible d-flex align-items-center justify-content-between mb-15" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="flex-fill mr-10">
            <h3 class="alert-heading font-size-h4 font-w400">EMERGENCY</h3>
            <p class="mb-0">EMERGENCY CURRENT STATUS: OFF!</p>
        </div>
        <div class="flex-00-auto">
            <i class="fa fa-4x fa-info-circle"></i>
        </div>
    </div>
<?php else : ?>
    <div class="alert alert-danger alert-dismissible d-flex align-items-center justify-content-between mb-15" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="flex-fill mr-10">
            <h3 class="alert-heading font-size-h4 font-w400">EMERGENCY</h3>
            <p class="mb-0">EMERGENCY CURRENT STATUS : ON!</p>
        </div>
        <div class="flex-00-auto">
            <i class="fa fa-4x fa-exclamation-triangle"></i>
        </div>
    </div>
<?php endif; ?>

<div class="block-content block-content-full" style="padding: 2% 20% 5% 20%">
    <div class="form-group">
        <label class='col-form-label'><b>Emergency Reason</b></label>
        <select class='form-control form-control-lg' id='emergency_reason' name='emergency_reason'>
            <?php
            if(count($category)> 0 )
            {
                foreach ($category as $key => $valueoption) 
                {
                    $selected = '';
                    if($oneactive->emergency_code == $valueoption->id)
                    {
                        $selected = 'selected';
                    }
                    echo "<option value='{$valueoption->id}' {$selected} >{$valueoption->id}</option>";
                }
            }
            ?>
        </select>
    </div>
    <div class="mt-4 text-center">
        <div style="display: inline-flex; align-items: center;">
            <?php if($oneactive->emergency_history_id != 0):?>
                <button type="button" class="btn btn-alt-danger btn-lg btn-rounded btn-emergency" style="display: flex; align-items: center;">
                    <i class="fa fa-power-off fa-2x"></i>
                    <span>&nbsp;&nbsp;&nbsp;&nbsp;STOP EMERGENCY ALERTS</span>
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-danger btn-lg btn-rounded btn-emergency" style="display: flex; align-items: center;">
                    <i class="fa fa-power-off fa-2x"></i> 
                    <span>&nbsp;&nbsp;&nbsp;&nbsp;SEND EMERGENCY ALERTS</span>
                </button>
            <?php endif;?>
        </div>
    </div>
</div>


<!-- Dialog emergency active confirmation -->
<div class="modal fade" id="modal-emergency" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <?php if($oneactive->emergency_history_id != 0):?>
                        <h3 class="block-title font-size-xl">DEACTIVATE EMERGENCY STATUS</h3>
                    <?php else: ?>
                        <h3 class="block-title font-size-xl">ACTIVATE EMERGENCY STATUS</h3>
                    <?php endif;?>                    
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <!-- <div class="row no-gutters items-push checkboxdisplay"></div> -->
                    <form id="newForm" action="<?=$baseUrl?>/turnemergency" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="emergency_code">
                        <input type="hidden" name="emergency_history_id" value="<?php echo $oneactive->emergency_history_id; ?>">
                        <?php if($oneactive->emergency_history_id != 0):?>
                            <p class="font-size-l">Confirm deactivation? Critical procedures will halt.</p>
                        <?php else: ?>
                            <p class="font-size-l">Proceed with caution. Confirm emergency activation?</p>
                        <?php endif;?>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fa fa-check fa-fw"></i>CONFIRM</button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>


<?=view('util/scripts.php')?>
<?=view('util/filemanager.php')?>

<script>
    var isEmergencyactive   = "<?php echo $oneactive->emergency_history_id; ?>";
    $('document').ready(function()
    {
        var categoryselected = $('#emergency_reason').val();
        if(isEmergencyactive != 0)
        {
            $('#emergency_reason').prop('disabled', true);
        }

        $('.btn-emergency').click(function()
        { 
            $('#modal-emergency').modal(); 
            $('#newForm input[name=emergency_code]').val(categoryselected);
        });

        $('#emergency_reason').change(function()
        {  
            categoryselected =  $(this).val();
        });
    });
</script>