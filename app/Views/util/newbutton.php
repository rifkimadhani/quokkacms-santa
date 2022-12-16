<?php defined('BASEPATH') OR exit('No direct script access allowed');
    $currentctrl = $this->router->fetch_class();
?>
<?php if(isset($usemodal)): ?>
<div style="margin-bottom:10px;">
    <?php if($usemodal): ?>
    <button type="button" class="btn btn-primary showNewModal" style="margin-bottom: 10px;">
        Create 
        <?php 
         if(isset($headertitle))
            {
                echo $headertitle;
            }
         else
            {
                echo ucfirst($currentctrl);
            } 
        ?>
    </button>
    <?php else: ?>
    <a href="<?= base_url($currentctrl.'/new') ?>" class="btn btn-primary" role="button" >
        Create <?php echo ucfirst($currentctrl) ?>
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>