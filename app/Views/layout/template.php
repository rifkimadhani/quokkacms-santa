<?php
    //  defined('BASEPATH') OR exit('No direct script access allowed');
    $username  = session('username');//$this->session->userdata('username');

    $username = 'admin';
    $isEmergency = false;
?>

<?= $this->include('layout/head'); ?>
<?= $this->include('layout/navbar'); ?>

<!-- Main Container -->
<main id="main-container"> 
    <!-- Page Content -->
    <div class="content">
        <!-- Emergency Alert -->
        <?php if($isEmergency): ?>
            <div class="alert alert-danger alert-dismissable" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="alert-heading font-size-h4 font-w400">Emergency</h3>
                <p class="mb-0">EMERGENCY CURRENT STATUS : ON!</p>
            </div>
        <?php endif; ?>
        <!-- END Emergency Alert -->
        <?= view('util/flash', compact('pageTitle')); ?>

        <div class="block">
            <div class="block-content">
                <?= view($mainview); ?>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
</main>
<!-- END Main Container -->

<!-- Modal Options (show & hide column) -->
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

<?= $this->include('layout/footer'); ?>