<?php
    //  defined('BASEPATH') OR exit('No direct script access allowed');
    $username  = session('username');//$this->session->userdata('username');

    require_once __DIR__ . '/../../../model/ModelSetting.php';

    $fKitchen = ModelSetting::getFeatureKitchen();
    if ($fKitchen==0) $fKitchen='hidden'; else $fKitchen='';

    $fMarketing = ModelSetting::getFeatureMarketing();
    if ($fMarketing==0) $fMarketing='hidden'; else $fMarketing='';

    $fDimsum = ModelSetting::getFeatureDimsum();
    if ($fDimsum==0) $fDimsum='hidden'; else $fDimsum='';

    $fStat= ModelSetting::getFeatureLivetvStat();
    if ($fStat==0) $fStat='hidden'; else $fStat='';

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

<?= $this->include('layout/footer'); ?>