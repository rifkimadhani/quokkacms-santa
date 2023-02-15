<?php

//defined('BASEPATH') OR exit('No direct script access allowed');

$session = session();
?>

	<?php if (!empty($session->getFlashdata('error_msg'))) : ?>	
		<!-- Error Alert -->
		<div class="alert alert-danger alert-dismissable" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<h3 class="alert-heading font-size-h4 font-w400">Error!</h3>
			<p class="mb-0"><?= $session->getFlashdata('error_msg') ?></p>
		</div>
		<!-- END Error Alert -->
	<?php elseif (!empty($session->getFlashdata('success_msg'))) : ?>
		<!-- Success Alert -->
		<div class="alert alert-success alert-dismissable" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<h3 class="alert-heading font-size-h4 font-w400">Success!</h3>
			<p class="mb-0"><?= $session->getFlashdata('success_msg') ?></p>
		</div>
		<!-- END Success Alert -->	
	<?php endif;?>
	
	<?php if(isset($pageTitle)):?>
		<!-- Page Title -->
		<div class="block">
			<div class="block-header block-header-default">
				<h3 class="block-title"><?= $pageTitle; ?></h3>
			</div>
		</div>
    <?php endif;?>
