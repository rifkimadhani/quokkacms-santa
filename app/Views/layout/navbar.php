<?php
require_once __DIR__ . '/../../../model/ModelSetting.php';

//$fKitchen = ModelSetting::getFeatureKitchen();
//if ($fKitchen == 0) $fKitchen = 'hidden';
//else $fKitchen = '';

//$fMarketing = ModelSetting::getFeatureMarketing();
//if ($fMarketing == 0) $fMarketing = 'hidden';
//else $fMarketing = '';

//$fDimsum = ModelSetting::getFeatureDimsum();
//if ($fDimsum == 0) $fDimsum = 'hidden';
//else $fDimsum = '';

//$fStat = ModelSetting::getFeatureLivetvStat();
//if ($fStat == 0) $fStat = 'hidden';
//else $fStat = '';

$fInbox = 1; //1=inbox menu visible, 0=hide
$fInbox = ($fInbox == 0) ? 'hidden' : '';

//utk saat ini shop blm di dukung, jadi di hide saja
//$fShop = 0;
//if ($fShop == 0) $fShop = 'hidden';
//else $fShop = '';

//role
$isAdmin = hasRole('admin'); //admin bisa semua role

//role kitchen dan room service sama2 mengerjakan room service task
$isKitchen = $isAdmin | hasRole('kitchen'); //rubah nama menu dan juga room service
$isRoomService = $isAdmin | $isKitchen | hasRole('room_service'); //user kitchen harus bisa rubah status roomservice

//concierge & house keeping sama2 mengerjakan hotel service task
$isConcierge = $isAdmin | hasRole('concierge');
$isHouseKeeping = $isAdmin | hasRole('housekeeping');
$isStaff = $isConcierge | $isHouseKeeping;

//set vibility utk setiap role
$adminVisibility = ($isAdmin) ? '' : 'hidden';
$kitchenVisibility = ($isKitchen) ? '' : 'hidden';
$staffVisibility = ($isStaff) ? '' : 'hidden';
$rsVisibility = ($isRoomService) ? '' : 'hidden';

// {"roles":["admin","housekeeping","room_service","concierge","kitchen"]}
function hasRole($roleName)
{
    try {
        $roles = session()->get('roles');
        if (!in_array($roleName, $roles)) return false;
        return true;
    } catch (\Exception $e) {
    }
    return false;
}
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
                    <a class="link-effect font-w700" href="<?= base_url('dashboard'); ?>">
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
                <img class="img-avatar img-avatar32" src="<?= base_url('res/assets/media/profile/avatar01.png') ?>" alt="">
            </div>
            <!-- END Visible only in mini mode -->

            <!-- Visible only in normal mode -->
            <div class="sidebar-mini-hidden-b text-center">
                <a class="img-link" href="<?= base_url('adminprofile'); ?>">
                    <img class="img-avatar" src="<?= base_url('res/assets/media/profile/avatar01.png') ?>" alt="">
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
                <li <?= $adminVisibility ?>>
                    <a href="<?= base_url('subscriber'); ?>"><i class="si si-user"></i> <span class="sidebar-mini-hide">Guest</span></a>
                </li>
                <li <?= $adminVisibility ?>>
                    <a href="<?= base_url('subscribergroup'); ?>"><i class="si si-users"></i> <span class="sidebar-mini-hide">Guest Group</span></a>
                </li>
                <!--                <li>-->
                <!--                    <a href="-->
                <? //= base_url('user'); 
                ?>
                <!--"><i class="si si-screen-smartphone"></i> <span class="sidebar-mini-hide">User Mobile</span></a>-->
                <!--                </li>-->
                <li <?= $adminVisibility ?>>
                    <a href="<?= base_url('message'); ?>"><i class="si si-bubbles"></i> <span class="sidebar-mini-hide">Messages</span></a>
                </li>
                <li <?= $fInbox ?>>
                    <a href="<?= base_url('inbox'); ?>"><i class="si si-envelope"></i> <span class="sidebar-mini-hide">Inbox</span></a>
                </li>
                <li <?= $staffVisibility ?>>
                    <a href="<?= base_url('hotelservice'); ?>"><i class="si si-handbag"></i> <span class="sidebar-mini-hide">Hotel Service</span></a>
                </li>
                <li <?= $rsVisibility ?>>
                    <a href="<?= base_url('roomservice'); ?>"><i class="si si-basket"></i> <span class="sidebar-mini-hide">Room Service</span></a>
                </li>

                <li <?= $adminVisibility ?>>
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
                                    <a href="<?= base_url('livetvpackage'); ?>">Package</a>
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
                            <a href="<?= base_url('stbdevices'); ?>">Devices</a>
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
                            <a href="<?= base_url('facility'); ?>">Hotel Facility</a>
                        </li>
                        <li>
                            <a href="<?= base_url('locality'); ?>">Tourist Info</a>
                        </li>
                        <li>
                            <a href="<?= base_url('filemanager/filemanager/dialog.php'); ?>" target="_blank">File
                                Manager</a>
                        </li>
                        <li>
                            <a href="<?= base_url('setting/simple'); ?>">Settings</a>
                        </li>
                    </ul>
                </li>

                <li <?= $kitchenVisibility ?>>
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
                <!--                <li -->
                <? //= $fShop 
                ?>
                <!-->
<!--                    <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-bag"></i><span class="sidebar-mini-hide">Shop</span></a>-->
                <!--                    <ul>-->
                <!--                        <li>-->
                <!--                            <a href="-->
                <? //= base_url('shop'); 
                ?>
                <!--">Seller</a>-->
                <!--                        </li>-->
                <!--                        <li>-->
                <!--                            <a href="-->
                <? //= base_url('shopproduct'); 
                ?>
                <!--">Product</a>-->
                <!--                        </li>-->
                <!--                        <li>-->
                <!--                            <a href="-->
                <? //= base_url('shoporder'); 
                ?>
                <!--">Order</a>-->
                <!--                        </li>-->
                <!--                    </ul>-->
                <!--                </li>-->
                <!--shop END-->

                <!--                <li -->
                <? //= $fMarketing 
                ?>
                <!-->
<!--                    <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-badge"></i><span class="sidebar-mini-hide">Marketing</span></a>-->
                <!--                    <ul>-->
                <!--                        <li>-->
                <!--                            <a href="-->
                <? //= base_url('marketingvod'); 
                ?>
                <!--">VOD</a>-->
                <!--                        </li>-->
                <!--                        <li>-->
                <!--                            <a href="-->
                <? //= base_url('marketingkaraoke'); 
                ?>
                <!--">Karaoke</a>-->
                <!--                        </li>-->
                <!--                    </ul>-->
                <!--                </li>-->

                <li <?= $adminVisibility ?>>
                    <a href="<?= base_url('emergency'); ?>"><i class="si si-bell"></i> <span class="sidebar-mini-hide">Emergency</span></a>
                </li>
                <li <?= $adminVisibility ?>>
                    <a href="<?= base_url('statistic'); ?>"><i class="si si-bar-chart"></i> <span class="sidebar-mini-hide">Statistic (Live TV)</span></a>
                </li>

                <!-- Admin Menu -->
                <li class="nav-main-heading">
                    <span class="sidebar-mini-visible">ADM</span><span class="sidebar-mini-hidden">Administrator</span>
                </li>
                <li <?= $adminVisibility ?>>
                    <a href="<?= base_url('admin'); ?>"><i class="si si-user-following"></i> <span class="sidebar-mini-hide">User</span></a>
                </li>
                <li <?= $adminVisibility ?>>
                    <a href="<?= base_url('role'); ?>"><i class="si si-puzzle"></i> <span class="sidebar-mini-hide">Role</span></a>
                </li>
                <li <?= $adminVisibility ?>>
                    <a href="<?= base_url('builder'); ?>"><i class="fa fa-code"></i> <span class="sidebar-mini-hide">Page Builder</span></a>
                </li>

            </ul>
        </div>
        <!-- END Side Navigation -->
    </div>
    <!-- Sidebar Content -->
</nav>
<!-- END Sidebar -->