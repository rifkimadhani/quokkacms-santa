<?php
    require_once __DIR__ . '/../../../model/ModelSetting.php';

    $fKitchen = ModelSetting::getFeatureKitchen();
    if ($fKitchen==0) $fKitchen='hidden'; else $fKitchen='';

    $fMarketing = ModelSetting::getFeatureMarketing();
    if ($fMarketing==0) $fMarketing='hidden'; else $fMarketing='';

    $fDimsum = ModelSetting::getFeatureDimsum();
    if ($fDimsum==0) $fDimsum='hidden'; else $fDimsum='';

    $fStat= ModelSetting::getFeatureLivetvStat();
    if ($fStat==0) $fStat='hidden'; else $fStat='';
?>
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
                        <a class="link-effect text-dual-primary-dark" href="<?= base_url('login/logout'); ?>">
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