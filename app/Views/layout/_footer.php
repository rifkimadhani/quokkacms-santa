            <!-- Footer -->
            <footer id="page-footer" class="opacity-0">
                <div class="content py-20 font-size-xs clearfix">
                    <div class="float-right">
                        <!-- Crafted with <i class="fa fa-heart text-pulse"></i> by <a class="font-w600" href="https://1.envato.market/ydb" target="_blank">pixelcave</a> -->
                        Version 1.0.1 - <?=date(DATE_RFC2822)?>
                    </div>
                    <div class="float-left">
                      <strong>
                        Copyright &copy;<a class="font-w600" href="https://madeiraresearch.com" target="_blank">Madeira Research Pte Ltd</a>
                      </strong>
                    </div>
                </div>
            </footer>
            <!-- END Footer -->
        </div>
        <!-- END Page Container -->
        
        <script src="<?= base_url('assets/assets/js/codebase.core.min.js'); ?>"></script>

        <!--
            Codebase JS

            Custom functionality including Blocks/Layout API as well as other vital and optional helpers
            webpack is putting everything together at assets/_es6/main/app.js
        -->
        <script src="<?= base_url('assets/assets/js/codebase.app.min.js'); ?>"></script>

        <!-- Page JS Plugins -->
        <script src="<?= base_url('assets/assets/js/plugins/chartjs/Chart.bundle.min.js'); ?>"></script>
        <script src="<?= base_url('assets/assets/js/plugins/slick/slick.min.js'); ?>"></script>

        <!-- Page JS Code -->
        <script src="<?= base_url('assets/assets/js/pages/be_pages_dashboard.min.js'); ?>"></script>
        <script src="<?= base_url('assets/assets/js/pages/be_tables_datatables.min.js'); ?>"></script>

        <!-- Sync from old template -->

        <!-- Bootstrap -->
        <!-- <script src="<?//= base_url('plugin/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script> -->
        <!-- <script src="<?//= base_url('plugin/adminlte/bower_components/select2/dist/js/select2.full.min.js') ?>"></script> -->
        <script src="<?= base_url('assets/assets/js/plugins/select2/js/select2.full.min.js') ?>"></script>
        <script src="<?= base_url('plugin/adminlte/bower_components/fastclick/lib/fastclick.js') ?>"></script>
        <!-- <script src="<?//= base_url('plugin/adminlte/bower_components/datatables.net/js/jquery.dataTables.min.js') ?>"></script> -->
        <script src="<?= base_url('assets/assets/js/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
        <!-- <script src="<?//= base_url('plugin/adminlte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') ?>"></script> -->
        <script src="<?= base_url('assets/assets/js/plugins/datatables/dataTables.bootstrap4.min.js') ?>"></script>
        <script src="<?= base_url('plugin/RowReorder-1.2.6/js/dataTables.rowReorder.min.js') ?>"></script>
        <script src="<?= base_url('plugin/adminlte/bower_components/inputmask/dist/jquery.inputmask.bundle.js') ?>"></script>
        <!-- <script src="<?//= base_url('plugin/adminlte/dist/js/adminlte.min.js') ?>"></script> -->
        <!-- <script src="<?//= base_url('plugin/adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.js') ?>"></script> -->
        <script src="<?= base_url('assets/assets/js/plugins/jquery-slimscroll/jquery.slimscroll.js') ?>"></script>
        <script src="<?= base_url('plugin/js/datetostring.js'); ?>"></script>
        <script src="<?= base_url('plugin/js/uriquerystring.js'); ?>"></script>
        <script src="<?= base_url('plugin/js/modalutil.js'); ?>"></script>
        <script src="<?= base_url('plugin/js/videohls.js'); ?>" type="text/javascript"></script>

        <!-- menu active -->
        <script>
          var baseurl = "<?php echo base_url() ?>";
          var url = window.location;
          var urlsplit = url.href.split('/');
          var fullurl = urlsplit[0]+'/'+urlsplit[1]+'/'+urlsplit[2]+'/' + urlsplit[3]+'/'+urlsplit[4];
          console.log(fullurl);

          $('ul.nav-main > li > a').filter(function() {
            // console.log(this.href);
            return this.href == fullurl;
          }).siblings().removeClass('active').end().addClass('active');

          $('ul.nav-main > li > a.nav-submenu').filter(function() {
            return $(this).parent().find('ul > li > a').filter(function() {
              return this.href == fullurl;
            }).length;
          }).parent().siblings().removeClass('open').end().addClass('open');
          
          $('li.open > ul > li > a').filter(function() {
              return this.href == fullurl;
          }).siblings().removeClass('active').end().addClass('active');

          $('li.open > ul > li > a.nav-submenu').filter(function() {
            return $(this).parent().find('ul > li > a').filter(function() {
              return this.href == fullurl;
            }).length;
          }).parent().siblings().removeClass('open').end().addClass('open');

          $('li.open > ul > li.open > ul > li > a').filter(function() {
              return this.href == fullurl;
          }).siblings().removeClass('active').end().addClass('active');
        </script>
    </body>
</html>