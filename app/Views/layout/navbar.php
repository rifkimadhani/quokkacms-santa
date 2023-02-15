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
<body>

        <!-- Page Container -->
        <!--
            Available classes for #page-container:

        GENERIC

            'enable-cookies'                            Remembers active color theme between pages (when set through color theme helper Template._uiHandleTheme())

        SIDEBAR & SIDE OVERLAY

            'sidebar-r'                                 Right Sidebar and left Side Overlay (default is left Sidebar and right Side Overlay)
            'sidebar-mini'                              Mini hoverable Sidebar (screen width > 991px)
            'sidebar-o'                                 Visible Sidebar by default (screen width > 991px)
            'sidebar-o-xs'                              Visible Sidebar by default (screen width < 992px)
            'sidebar-inverse'                           Dark themed sidebar

            'side-overlay-hover'                        Hoverable Side Overlay (screen width > 991px)
            'side-overlay-o'                            Visible Side Overlay by default

            'enable-page-overlay'                       Enables a visible clickable Page Overlay (closes Side Overlay on click) when Side Overlay opens

            'side-scroll'                               Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (screen width > 991px)

        HEADER

            ''                                          Static Header if no class is added
            'page-header-fixed'                         Fixed Header

        HEADER STYLE

            ''                                          Classic Header style if no class is added
            'page-header-modern'                        Modern Header style
            'page-header-inverse'                       Dark themed Header (works only with classic Header style)
            'page-header-glass'                         Light themed Header with transparency by default
                                                        (absolute position, perfect for light images underneath - solid light background on scroll if the Header is also set as fixed)
            'page-header-glass page-header-inverse'     Dark themed Header with transparency by default
                                                        (absolute position, perfect for dark images underneath - solid dark background on scroll if the Header is also set as fixed)

        MAIN CONTENT LAYOUT

            ''                                          Full width Main Content if no class is added
            'main-content-boxed'                        Full width Main Content with a specific maximum width (screen width > 1200px)
            'main-content-narrow'                       Full width Main Content with a percentage width (screen width > 1200px)
        -->
        <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-fixed page-header-modern main-content-narrow">

            <!-- Sidebar -->
            <!--
                Helper classes

                Adding .sidebar-mini-hide to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
                Adding .sidebar-mini-show to an element will make it visible (opacity: 1) when the sidebar is in mini mode
                    If you would like to disable the transition, just add the .sidebar-mini-notrans along with one of the previous 2 classes

                Adding .sidebar-mini-hidden to an element will hide it when the sidebar is in mini mode
                Adding .sidebar-mini-visible to an element will show it only when the sidebar is in mini mode
                    - use .sidebar-mini-visible-b if you would like to be a block when visible (display: block)
            -->
            <nav id="sidebar">
                <!-- Sidebar Content -->
                <div class="sidebar-content">
                    <!-- Side Header -->
                    <div class="content-header content-header-fullrow px-15">
                        <!-- Mini Mode -->
                        <div class="content-header-section sidebar-mini-visible-b">
                            <!-- Logo -->
                            <span class="content-header-item font-w700 font-size-xl float-left animated fadeIn">
                                <span class="text-dual-primary-dark">e</span><span class="text-primary">m</span>
                            </span>
                            <!-- END Logo -->
                        </div>
                        <!-- END Mini Mode -->

                        <!-- Normal Mode -->
                        <div class="content-header-section text-center align-parent sidebar-mini-hidden">
                            <!-- Close Sidebar, Visible only on mobile screens -->
                            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                            <button type="button" class="btn btn-circle btn-dual-secondary d-lg-none align-v-r" data-toggle="layout" data-action="sidebar_close">
                                <i class="fa fa-times text-danger"></i>
                            </button>
                            <!-- END Close Sidebar -->

                            <!-- Logo -->
                            <div class="content-header-item">
                                <a class="link-effect font-w700" href="index.html">
                                    <span class="font-size-l text-dual-primary-dark">entertainz</span><span class="font-size-l text-primary">management</span>
                                </a>
                            </div>
                            <!-- END Logo -->
                        </div>
                        <!-- END Normal Mode -->
                    </div>
                    <!-- END Side Header -->

                    <!-- Side User -->
                    <div class="content-side content-side-full content-side-user px-10 align-parent">
                        <!-- Visible only in mini mode -->
                        <div class="sidebar-mini-visible-b align-v animated fadeIn">
                            <img class="img-avatar img-avatar32" src="<?= base_url('plugin/adminlte/dist/img/avatar04.png') ?>" alt="">
                        </div>
                        <!-- END Visible only in mini mode -->

                        <!-- Visible only in normal mode -->
                        <div class="sidebar-mini-hidden-b text-center">
                            <a class="img-link" href="<?= base_url('adminprofile'); ?>">
                                <img class="img-avatar" src="<?= base_url('plugin/adminlte/dist/img/avatar04.png') ?>" alt="">
                            </a>
                            <ul class="list-inline mt-10">
                                <li class="list-inline-item">
                                    <a class="link-effect text-dual-primary-dark font-size-xs font-w600 text-uppercase" href="<?= base_url('adminprofile'); ?>"><?php echo $username; ?></a>
                                </li>
                                <li class="list-inline-item">
                                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                                    <a class="link-effect text-dual-primary-dark" data-toggle="layout" data-action="sidebar_style_inverse_toggle" href="javascript:void(0)">
                                        <i class="si si-drop"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="link-effect text-dual-primary-dark" href="<?= base_url('auth/logout'); ?>">
                                        <i class="si si-logout"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- END Visible only in normal mode -->
                    </div>
                    <!-- END Side User -->

                    <!-- Side Navigation -->
                    <div class="content-side content-side-full">
                        <ul class="nav-main">
                            <li class="nav-main-heading">
                              <span class="sidebar-mini-visible">MN</span><span class="sidebar-mini-hidden">Main Navigation</span>
                            </li>
                            
                            <li>
                              <a href="<?= base_url('dashboard'); ?>"><i class="si si-home"></i> <span class="sidebar-mini-hide">Home</span></a>
                            </li>
                            <li>
                              <a href="<?= base_url('subscriber'); ?>"><i class="si si-user"></i> <span class="sidebar-mini-hide">Guest</span></a>
                            </li>
                            <li>
                              <a href="<?= base_url('subscribergroup'); ?>"><i class="si si-users"></i> <span class="sidebar-mini-hide">Guest Group</span></a>
                            </li>
                            <li>
                              <a href="<?= base_url('user'); ?>"><i class="si si-screen-smartphone"></i> <span class="sidebar-mini-hide">User Mobile</span></a>
                            </li>
                            <li>
                              <a href="<?= base_url('message'); ?>"><i class="si si-bubbles"></i> <span class="sidebar-mini-hide">Messages</span></a>
                            </li>
                            <li>
                              <a href="<?= base_url('inbox'); ?>"><i class="si si-envelope"></i> <span class="sidebar-mini-hide">Inbox</span></a>
                            </li>
                            <li>
                              <a href="<?= base_url('hotelservice'); ?>"><i class="si si-handbag"></i> <span class="sidebar-mini-hide">Hotel Service</span></a>
                            </li>
                            <li>
                              <a href="<?= base_url('roomservice'); ?>"><i class="si si-basket"></i> <span class="sidebar-mini-hide">Room Service</span></a>
                            </li>
                            <li>
                                <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-settings"></i><span class="sidebar-mini-hide">Setup</span></a>
                                <ul>
                                    <li>
                                        <a class="nav-submenu" data-toggle="nav-submenu" href="#">App</a>
                                        <ul>
                                            <li>
                                                <a href="<?= base_url('app'); ?>">Application</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('installer'); ?>">Installer</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="nav-submenu" data-toggle="nav-submenu" href="#">Theme & Element</a>
                                        <ul>
                                            <li>
                                                <a href="<?= base_url('theme'); ?>">Theme</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('element'); ?>">Element</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="nav-submenu" data-toggle="nav-submenu" href="#">Channel & Package</a>
                                        <ul>
                                            <li>
                                                <a href="<?= base_url('livetv'); ?>">Live TV</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('livetvcategory'); ?>">Category</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('package'); ?>">Package</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('livetv_epg'); ?>">EPG</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="nav-submenu" data-toggle="nav-submenu" href="#">Ads & Spot</a>
                                        <ul>
                                            <li>
                                                <a href="<?= base_url('spot'); ?>">Spot</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('ads'); ?>">Ads</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="<?= base_url('stbdevices'); ?>">STB Devices</a>
                                    </li>
                                    <li>
                                        <a class="nav-submenu" data-toggle="nav-submenu" href="#">Room & Type</a>
                                        <ul>
                                            <li>
                                                <a href="<?= base_url('room'); ?>">Room</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('roomtype'); ?>">Type</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="nav-submenu" data-toggle="nav-submenu" href="#">VOD & Genre</a>
                                        <ul>
                                            <li>
                                                <a href="<?= base_url('vod'); ?>">VOD</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('vodgenre'); ?>">Genre</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="nav-submenu" data-toggle="nav-submenu" href="#">Emergency</a>
                                        <ul>
                                            <li>
                                                <a href="<?= base_url('emergencycategory'); ?>">Category</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('emergencyhistory'); ?>">History</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="<?= base_url('language'); ?>">Language</a>
                                    </li>
                                    <li>
                                        <a href="<?= base_url('currency'); ?>">Currency</a>
                                    </li>
                                    <li>
                                        <a href="<?= base_url('facility'); ?>">Hotel Service</a>
                                    </li>
                                    <li>
                                        <a href="<?= base_url('locality'); ?>">Tourist Info</a>
                                    </li>
                                    <li>
                                        <a href="<?= base_url('filemanager/filemanager/dialog.php'); ?>" target="_blank">File Manager</a>
                                    </li>
                                    <li>
                                        <a href="<?= base_url('setting'); ?>">Setting</a>
                                    </li>
                                </ul>
                            </li>
                            <li <?=$fKitchen?>>
                              <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-cup"></i><span class="sidebar-mini-hide">Kitchen & Menu</span></a>
                              <ul>
                                  <li>
                                      <a href="<?= base_url('kitchen'); ?>">Kitchen</a>
                                  </li>
                                  <li>
                                      <a href="<?= base_url('kitchenmenugroup'); ?>">Menu Grup</a>
                                  </li>
                                  <li>
                                      <a href="<?= base_url('kitchenmenu'); ?>">Menu</a>
                                  </li>
                              </ul>
                            </li>
                            <!-- shop START-->
                            <li>
                              <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-bag"></i><span class="sidebar-mini-hide">Shop</span></a>
                              <ul>
                                  <li>
                                      <a href="<?= base_url('shop'); ?>">Seller</a>
                                  </li>
                                  <li>
                                      <a href="<?= base_url('shopproduct'); ?>">Product</a>
                                  </li>
                                  <li>
                                      <a href="<?= base_url('shoporder'); ?>">Order</a>
                                  </li>
                              </ul>
                            </li>
                            <!--shop END-->
                            <li>
                              <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-badge"></i><span class="sidebar-mini-hide">Marketing</span></a>
                              <ul>
                                  <li>
                                      <a href="<?= base_url('marketingvod'); ?>">VOD</a>
                                  </li>
                                  <li>
                                      <a href="<?= base_url('marketingkaraoke'); ?>">Karaoke</a>
                                  </li>
                              </ul>
                            </li>
                            <li>
                              <a href="<?= base_url('emergency'); ?>"><i class="si si-bell"></i> <span class="sidebar-mini-hide">Emergency</span></a>
                            </li>
                            <li>
                              <a href="<?= base_url('statistic'); ?>"><i class="si si-bar-chart"></i> <span class="sidebar-mini-hide">Statistic (Live TV)</span></a>
                            </li>
                        </ul>
                    </div>
                    <!-- END Side Navigation -->
                </div>
                <!-- Sidebar Content -->
            </nav>
            <!-- END Sidebar -->

            <!-- Header -->
            <header id="page-header">
                <!-- Header Content -->
                <div class="content-header">
                    <!-- Left Section -->
                    <div class="content-header-section">
                        <!-- Toggle Sidebar -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <button type="button" class="btn btn-circle btn-dual-secondary" data-toggle="layout" data-action="sidebar_mini_toggle">
                            <i class="fa fa-navicon"></i>
                        </button>
                        <!-- END Toggle Sidebar -->

                        <!-- Layout Options -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-circle btn-dual-secondary" id="page-header-options-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-wrench"></i>
                            </button>
                            <div class="dropdown-menu min-width-300" aria-labelledby="page-header-options-dropdown">
                                <h5 class="h6 text-center py-10 mb-10 border-b text-uppercase">Settings</h5>
                                <h6 class="dropdown-header">Color Themes</h6>
                                <div class="row no-gutters text-center mb-5">
                                    <div class="col-2 mb-5">
                                        <a class="text-default" data-toggle="theme" data-theme="default" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                    <div class="col-2 mb-5">
                                        <a class="text-elegance" data-toggle="theme" data-theme="<?= base_url('assets/assets/css/themes/elegance.min.css'); ?>" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                    <div class="col-2 mb-5">
                                        <a class="text-pulse" data-toggle="theme" data-theme="<?= base_url('assets/assets/css/themes/pulse.min.css'); ?>" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                    <div class="col-2 mb-5">
                                        <a class="text-flat" data-toggle="theme" data-theme="<?= base_url('assets/assets/css/themes/flat.min.css'); ?>" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                    <div class="col-2 mb-5">
                                        <a class="text-corporate" data-toggle="theme" data-theme="<?= base_url('assets/assets/css/themes/corporate.min.css'); ?>" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                    <div class="col-2 mb-5">
                                        <a class="text-earth" data-toggle="theme" data-theme="<?= base_url('assets/assets/css/themes/earth.min.css'); ?>" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                </div>
                                <h6 class="dropdown-header">Sidebar</h6>
                                <div class="row gutters-tiny text-center mb-5">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-sm btn-block btn-alt-secondary mb-10" data-toggle="layout" data-action="sidebar_style_inverse_off">Light</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-sm btn-block btn-alt-secondary mb-10" data-toggle="layout" data-action="sidebar_style_inverse_on">Dark</button>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                            </div>
                        </div>
                        <!-- END Layout Options -->
                    </div>
                    <!-- END Left Section -->

                    <!-- Right Section -->
                    <div class="content-header-section">
                        <!-- User Dropdown -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user d-sm-none"></i>
                                <span class="d-none d-sm-inline-block"><?php echo $username; ?></span>
                                <i class="fa fa-angle-down ml-5"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right min-width-200" aria-labelledby="page-header-user-dropdown">
                                <h5 class="h6 text-center py-10 mb-5 border-b text-uppercase">User</h5>
                                <a class="dropdown-item" href="<?= base_url('adminprofile'); ?>">
                                    <i class="si si-user mr-5"></i> Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= base_url('auth/logout'); ?>">
                                    <i class="si si-logout mr-5"></i> Sign Out
                                </a>
                            </div>
                        </div>
                        <!-- END User Dropdown -->
                    </div>
                    <!-- END Right Section -->
                </div>
                <!-- END Header Content -->

                <!-- Header Search -->
                <div id="page-header-search" class="overlay-header">
                    <div class="content-header content-header-fullrow">
                        <form action="be_pages_generic_search.html" method="post">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <!-- Close Search Section -->
                                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                                    <button type="button" class="btn btn-secondary" data-toggle="layout" data-action="header_search_off">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <!-- END Close Search Section -->
                                </div>
                                <input type="text" class="form-control" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- END Header Search -->

                <!-- Header Loader -->
                <!-- Please check out the Activity page under Elements category to see examples of showing/hiding it -->
                <div id="page-header-loader" class="overlay-header bg-primary">
                    <div class="content-header content-header-fullrow text-center">
                        <div class="content-header-item">
                            <i class="fa fa-sun-o fa-spin text-white"></i>
                        </div>
                    </div>
                </div>
                <!-- END Header Loader -->
            </header>
            <!-- END Header -->