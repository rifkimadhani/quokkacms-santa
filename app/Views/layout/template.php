<?php
    //  defined('BASEPATH') OR exit('No direct script access allowed');

    $session = session();

    $username  = session('username');//$this->session->userdata('username');

//    $username = 'admin';
    $isEmergency = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= view('layout/head'); ?>
</head>
<body>
    <!-- Page Container -->
    <div id="page-container" class="sidebar-o sidebar-inverse enable-page-overlay page-header-fixed page-header-modern side-scroll main-content-narrow">

        <!-- Header -->
        <?= view('layout/header', compact('username')); ?>

        <!-- Sidebar menu -->
        <?= view('layout/navbar', compact('isEmergency','username')); ?>

        <!-- Page content -->
        <?= view('layout/content'); ?>

        <!-- Footer -->
        <?= view('layout/footer'); ?>

    </div>
    
    <!-- Modal Options -->
    <!-- A modal for showing and hiding columns in the table. */ -->
    <div class="modal fade" id="modal-checkbox" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popout" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title"><?= $pageTitle; ?>'s Options</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row no-gutters items-push checkboxdisplay"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-alt-success" data-dismiss="modal">
                        <i class="fa fa-check"></i> Perfect
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?= view('layout/scripts'); ?>
    
    <!-- Alert Notification -->
    <?php if ($session->getFlashdata('type')) : ?>
        <script>
            jQuery(function(){ 
                Codebase.helpers('notify',{
                    align: 'right',             
                    from: 'top',                
                    type: '<?php echo $session->getFlashdata('type'); ?>',
                    icon: '<?php echo ($session->getFlashdata('type') == 'success') ? "fa fa-check mr-5" : "fa fa-exclamation-triangle mr-5"; ?>',
                    message: '<?php echo $session->getFlashdata("message"); ?>',
                    delay: 9000, // Delay in milliseconds
                    timer: 1000 // Timer interval in milliseconds
                }); 
            });
        </script>
    <?php endif; ?>
    <!-- End Alert Notification -->    
</body>
</html>