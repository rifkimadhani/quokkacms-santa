<?php
require_once __DIR__ . '/../../../config/version.php';
$version = VERSION;
?>

<!-- Footer -->
<footer id="page-footer" class="opacity-0">
  <div class="content py-20 font-size-xs clearfix">
    <div class="float-right">
      <!-- Crafted with <i class="fa fa-heart text-pulse"></i> by <a class="font-w600" href="https://1.envato.market/ydb" target="_blank">pixelcave</a> -->
      Version <?= $version ?> - <?= date(DATE_RFC2822) ?>
    </div>
    <div class="float-left">
      <strong>
        Copyright &copy;<a class="font-w600" href="https://madeiraresearch.com" target="_blank">Madeira
          Entertainz</a>
      </strong>
    </div>
  </div>
</footer>
<!-- END Footer -->