<?php

//defined('BASEPATH') OR exit('No direct script access allowed');

$session = session();
?>


<section class="content-header">
	<?php if (!empty($session->getFlashdata('error_msg'))) : ?>
		<div class="alert alert-danger alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-ban"></i> Error!</h4>
			<?= $session->getFlashdata('error_msg') ?>
		</div>
	<?php elseif (!empty($session->getFlashdata('success_msg'))) : ?>
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-check"></i> Success!</h4>
			<?= $session->getFlashdata('success_msg') ?>
		</div>
	<?php endif;?>
	<?php if(isset($this->headertitle)):?>
    	<h1><?php echo $this->headertitle; ?></h1>
    <?php endif;?>
</section>