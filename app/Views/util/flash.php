<?php

//defined('BASEPATH') OR exit('No direct script access allowed');

$session = session();
?>

<?php if(isset($pageTitle)):?>
	<div class="block">
		<div class="block-header block-header-default">
			<!-- Page Title -->
			<h3 class="block-title"><?= $pageTitle; ?></h3>
			<!-- END Page Title -->
		</div>
	</div>
<?php endif;?>