<?php
//  defined('BASEPATH') OR exit('No direct script access allowed');
  $username  = session('username');//$this->session->userdata('username');

require_once __DIR__ . '/../../model/ModelSetting.php';

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
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>OTT SERVER | Management</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?= base_url('plugin/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/adminlte/bower_components/font-awesome/css/font-awesome.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/adminlte/bower_components/Ionicons/css/ionicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/adminlte/bower_components/select2/dist/css/select2.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/adminlte/dist/css/AdminLTE.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/adminlte/dist/css/skins/_all-skins.min.css') ?>">
  
  <link rel="stylesheet" href="<?= base_url('plugin/adminlte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/RowReorder-1.2.6/css/rowReorder.dataTables.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/jstree/dist/themes/default/style.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/source-sans-pro/font.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/bootstrap-colorpicker-2.5.3/dist/css/bootstrap-colorpicker.min.css')?>">
  <link rel="stylesheet" href="<?= base_url('plugin/css/select.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/css/stepwizard.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/css/stockphoto.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugin/bootstrap-datepicker-1.9.0-dist/css/bootstrap-datepicker3.standalone.css')?>">

  <!-- jQuery 3 -->
<script src="<?= base_url('plugin/adminlte/bower_components/jquery/dist/jquery.min.js') ?>"></script>
<script src="<?= base_url('plugin/adminlte/bower_components/jquery-ui/jquery-ui.min.js') ?>"></script>
<script src="<?= base_url('plugin/jstree/dist/jstree.min.js') ?>"></script>
<script src="<?= base_url('plugin/jquery.validate-1.11.1/jquery.validate.js') ?>"></script>
<script src="<?= base_url('plugin/ckeditor_4.10.0/ckeditor.js')?>"></script>
<script src="<?= base_url('plugin/bootstrap-colorpicker-2.5.3/dist/js/bootstrap-colorpicker.min.js')?>"></script>
<script src="<?= base_url('plugin/hls-js/hls.js')?>"></script>
</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>ENZ</b>M</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b style="color: white;font-size:14px;">ENTERTAINZ MANAGEMENT</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?= base_url('plugin/adminlte/dist/img/avatar04.png') ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?= $username ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?= base_url('plugin/adminlte/dist/img/avatar04.png') ?>" class="img-circle" alt="User Image">

                <p><?php echo $username; ?> </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?= base_url('adminprofile'); ?>" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="<?= base_url('auth/logout'); ?>" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?= base_url('plugin/adminlte/dist/img/avatar04.png') ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
          <a href="javascript:;"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li><a href="<?= base_url('dashboard'); ?>"><i class="fa fa-home"></i> <span>Home</span></a></li>
        <li><a href="<?= base_url('subscriber'); ?>"><i class="fa fa-group"></i> <span>Guest</span></a></li>
        <li><a href="<?= base_url('subscribergroup'); ?>"><i class="fa fa-group"></i> <span>Guest group</span></a></li>
        <li><a href="<?= base_url('user'); ?>"><i class="fa fa-group"></i> <span>User mobile</span></a></li>
        <li><a href="<?= base_url('message'); ?>"><i class="fa fa-envelope-o"></i> <span>Messages</span></a></li>
        <li><a href="<?= base_url('inbox'); ?>"><i class="fa fa-envelope-o"></i> <span>Inbox</span></a></li>
        <li><a href="<?= base_url('hotelservice'); ?>"><i class="fa fa-envelope-o"></i> <span>Hotel Service</span></a></li>
        <li <?=$fKitchen?>><a href="<?= base_url('roomservice'); ?>"><i class="fa fa-shopping-cart"></i> <span>Room Service</span></a></li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-cog"></i> <span>Setup</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview">
              <a href="javascript:;">
                <i class="fa fa-wifi"></i> <span>App</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= base_url('app'); ?>"><i class="fa fa-circle-o"></i>Application</a></li>
                
                <li><a href="<?= base_url('installer'); ?>"><i class="fa fa-circle-o"></i>Installer</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="javascript:;">
                <i class="fa fa-text-width"></i> <span>Theme & Element</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
              <li><a href="<?= base_url('theme'); ?>"><i class="fa fa-circle-o"></i>Theme</a></li>
                <li><a href="<?= base_url('element'); ?>"><i class="fa fa-circle-o"></i>Element</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="javascript:;">
                <i class="fa fa-tv"></i> <span>Live TV</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= base_url('livetv'); ?>"><i class="fa fa-circle-o"></i>Live TV</a></li>
                <li><a href="<?= base_url('livetvcategory'); ?>"><i class="fa fa-circle-o"></i>Category</a></li>
                <li><a href="<?= base_url('package'); ?>"><i class="fa fa-circle-o"></i>Package</a></li>
                <li><a href="<?= base_url('livetv_epg'); ?>"><i class="fa fa-circle-o"></i>EPG</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="javascript:;">
                <i class="fa fa-font"></i> <span>Ads & Spot</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= base_url('spot'); ?>"><i class="fa fa-circle-o"></i>Spot</a></li>
                <li><a href="<?= base_url('ads'); ?>"><i class="fa fa-circle-o"></i>Ads</a></li>
              </ul>
            </li>
            <li><a href="<?= base_url('stbdevices'); ?>"><i class="fa fa-circle-o"></i>STB Devices</a></li>
            <li class="treeview">
              <a href="javascript:;">
                <i class="fa fa-bed"></i> <span>Room & Type </span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= base_url('room'); ?>"><i class="fa fa-circle-o"></i>Room</a></li>
                <li><a href="<?= base_url('roomtype'); ?>"><i class="fa fa-circle-o"></i>Type</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="javascript:;">
                <i class="fa fa-film"></i> <span>VOD & Genre </span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= base_url('vod'); ?>"><i class="fa fa-circle-o"></i>VOD</a></li>
                <li><a href="<?= base_url('vodgenre'); ?>"><i class="fa fa-circle-o"></i>Genre</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="javascript:;">
                <i class="fa fa-bed"></i> <span>Emergency </span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= base_url('emergencycategory'); ?>"><i class="fa fa-circle-o"></i>Category</a></li>
                <li><a href="<?= base_url('emergencyhistory'); ?>"><i class="fa fa-circle-o"></i>History</a></li>
              </ul>
            </li>
            <li><a href="<?= base_url('language'); ?>"><i class="fa fa-italic"></i> <span>Language</span></a></li>
            <li><a href="<?= base_url('currency'); ?>"><i class="fa fa-money"></i> <span>Currency</span></a></li>
            <li><a href="<?= base_url('facility'); ?>"><i class="fa fa-university"></i> <span>Facility</span></a></li>
            <li><a href="<?= base_url('locality'); ?>"><i class="fa fa-share-square"></i> <span>Tourist Info</span></a></li>
            <!-- <li><a href="<?= base_url('banner'); ?>"><i class="fa fa-picture-o"></i> <span>Banner</span></a></li> -->
            <li><a href="<?= base_url('filemanager/filemanager/dialog.php'); ?>" target="_blank"><i class="fa fa-upload"></i> <span>File Manager</span></a></li>
            <li><a href="<?= base_url('setting'); ?>"><i class="fa fa-gear"></i> <span>Setting</span></a></li>
          </ul>
        </li>
        <li class="treeview" <?=$fKitchen?>>
          <a href="javascript:;">
            <i class="fa fa-shopping-cart"></i> <span>Kitchen & Menu</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?= base_url('kitchen'); ?>"><i class="fa fa-circle-o"></i>Kitchen</a></li>
            <li><a href="<?= base_url('kitchenmenugroup'); ?>"><i class="fa fa-circle-o"></i>Menu Grup</a></li>
            <li><a href="<?= base_url('kitchenmenu'); ?>"><i class="fa fa-circle-o"></i>Menu</a></li>
          </ul>
        </li>
<!-- shop START-->
          <li class="treeview">
              <a href="javascript:;">
                  <i class="fa fa-shopping-cart"></i> <span>Shop</span>
                  <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
              </a>
              <ul class="treeview-menu">
                  <li><a href="<?= base_url('shop'); ?>"><i class="fa fa-circle-o"></i>Seller</a></li>
                  <li><a href="<?= base_url('shopproduct'); ?>"><i class="fa fa-circle-"></i>Product</a></li>
                  <li><a href="<?= base_url('shoporder'); ?>"><i class="fa fa-circle-"></i>Order</a></li>
              </ul>
          </li>
<!--shop END-->
          <li class="treeview" <?=$fMarketing?>>
          <a href="javascript:;">
            <i class="fa fa-font"></i> <span>Marketing</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?= base_url('marketingvod'); ?>"><i class="fa fa-circle-o"></i>VOD</a></li>
            <li><a href="<?= base_url('marketingkaraoke'); ?>"><i class="fa fa-circle-o"></i>Karaoke</a></li>
          </ul>
        </li>

<!--        Menu gimsum-->
        <li <?=$fDimsum?>><a href="<?= base_url('dimsum'); ?>"><i class=""></i> <span>Dim Sum</span></a></li>
<!--        Menu Emergency-->
        <li><a href="<?= base_url('emergency'); ?>"><i class="fa fa-bullhorn"></i> <span>Emergency</span></a></li>
<!--        Menu Statistik-->
        <li <?=$fStat?>><a href="<?= base_url('statistic'); ?>"><i class="fa fa-line-chart"></i> <span>Statistic (Live TV)</span></a></li>

        <li class="header">ADMIN NAVIGATION</li>
        <li class="treeview">
          <a href="javascript:;">
            <i class="fa fa-user-plus"></i> <span>Administrator</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?= base_url('adminlogin'); ?>"><i class="fa fa-circle-o"></i>Login</a></li>
            <li><a href="<?= base_url('adminrole'); ?>"><i class="fa fa-circle-o"></i>Role</a></li>
            <li><a href="<?= base_url('adminprofile'); ?>"><i class="fa fa-circle-o"></i>Profile</a></li>
          </ul>
        </li>
        <!-- <li class="treeview">
          <a href="javascript:;">
            <i class="fa fa-lock"></i> <span>Permission & Privilage</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?= base_url('adminpermission'); ?>"><i class="fa fa-circle-o"></i>Permission</a></li>
            <li><a href="<?= base_url('admincontrollist'); ?>"><i class="fa fa-circle-o"></i>Control List</a></li>
          </ul>
        </li> -->
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <div id="overlay-loader-indicator">
        <div class="loader-indicator"></div>
    </div>
    <?php if($isEmergency): ?>
      <section class="content-header">
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> EMERGENCY CURRENT STATUS : ON!</h4>
        </div>
      </section>
    <?php endif; ?>
    <?= view('util/flash', compact('pageTitle')); ?>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <?= view($mainview); ?>
            </div>
        </div>
    </section>
<!--    --><?//= view('util/modal'); ?>
<!--    --><?//= view('util/modal-image-preview');?>
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.1
    </div>
    <strong>Copyright &copy;<a href="https://madeiraresearch.com">Madeira Research Pte Ltd</a></strong>
  </footer>
</div>
<!-- ./wrapper -->


<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url('plugin/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
<script src="<?= base_url('plugin/adminlte/bower_components/select2/dist/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('plugin/adminlte/bower_components/fastclick/lib/fastclick.js') ?>"></script>
<script src="<?= base_url('plugin/adminlte/bower_components/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugin/adminlte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') ?>"></script>
<script src="<?= base_url('plugin/RowReorder-1.2.6/js/dataTables.rowReorder.min.js') ?>"></script>
<script src="<?= base_url('plugin/adminlte/bower_components/inputmask/dist/jquery.inputmask.bundle.js') ?>"></script>
<script src="<?= base_url('plugin/adminlte/dist/js/adminlte.min.js') ?>"></script>
<script src="<?= base_url('plugin/adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.js') ?>"></script>
<script src="<?= base_url('plugin/js/datetostring.js'); ?>"></script>
<script src="<?= base_url('plugin/js/uriquerystring.js'); ?>"></script>
<!--<script src="--><?//= base_url('plugin/js/modalutil.js'); ?><!--"></script>-->
<script src="<?= base_url('plugin/js/videohls.js'); ?>" type="text/javascript"></script>

<script>
  var baseurl  = "<?php echo base_url() ?>";
  var url      = window.location;
  var urlsplit = url.href.split('/');
  var fullurl  = urlsplit[0]+'/'+urlsplit[1]+'/'+urlsplit[2]+'/'+urlsplit[3]+'/'+urlsplit[4];
  // for sidebar menu but not for treeview submenu
  $('ul.sidebar-menu a').filter(function() 
  {
    return this.href == fullurl;
  }).parent().siblings().removeClass('active').end().addClass('active');
  // for treeview which is like a submenu
  $('ul.treeview-menu a').filter(function() 
  {
    return this.href == fullurl;
  }).parentsUntil(".sidebar-menu > .treeview-menu").siblings().removeClass('active menu-open').end().addClass('active menu-open');
</script>
</body>
</html>
