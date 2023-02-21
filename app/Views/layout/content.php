<?php
    //  defined('BASEPATH') OR exit('No direct script access allowed');
    $username  = session('username');//$this->session->userdata('username');

    $username = 'admin';
    $isEmergency = false;
?>
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