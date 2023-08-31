<?php
//handle flashdata
//
$htmlFlashdata = '';
if (empty(session()->getFlashdata('type')) == false) {
    $type = session()->getFlashdata('type');
    $message = session()->getFlashdata('message');
    if ($type == 'error') {
        $html = <<<HTML
            <div id="username-error" class="alert animated fadeInDown text-danger login-alert" style="padding: 0px !important;">{$message}.</div>
        HTML;
    } else {
        $html = <<<HTML
            <div id="username-error" class="alert animated fadeInDown text-danger login-alert" style="padding: 0px !important;">{$message}.</div>
        HTML;
    }
    $htmlFlashdata = $html;
}
?>
<!DOCTYPE html>
<html lang="en" class="no-focus">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>OTT SERVER</title>

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('plugin/codebaseadmin/favicons/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('plugin/codebaseadmin/favicons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('plugin/codebaseadmin/favicons/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= base_url('plugin/codebaseadmin/favicons/site.webmanifest') ?>">
    <!-- END Icons -->

    <!-- Stylesheets -->

    <!-- Fonts and Codebase framework -->
    <link rel="stylesheet" href="<?= base_url('plugin/codebaseadmin/css/muli.css') ?>">
    <link rel="stylesheet" id="css-main" href="<?= base_url('plugin/codebaseadmin/css/codebase.min.css') ?>">

    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="<? //= base_url('plugin/codebaseadmin/css/themes/corporate.min.css') 
                                                        ?>"> -->
    <!-- END Stylesheets -->
</head>

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
    <div id="page-container" class="main-content-boxed">

        <!-- Main Container -->
        <main id="main-container">

            <!-- Page Content -->
            <div class="bg-image" style="background-image: url('<?= base_url('res/assets/media/bg/bg-quokka.png') ?>');">
                <div class="row mx-0">
                    <div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
                        <div class="p-5 invisible" data-toggle="appear">
                            <p class="font-size-h3 font-w600 text-white">
                                <!-- Entertainz Hospitality Solutions. -->
                            </p>
                            <p class="font-italic text-white-op">
                                Copyright &copy; <span>Madeira Research Pte. Ltd.</span>
                            </p>
                        </div>
                    </div>
                    <div class="hero-static col-md-6 col-xl-4 d-flex align-items-center bg-white-op invisible" data-toggle="appear" data-class="animated fadeInRight">
                        <div class="content content-full">
                            <a href="<?= base_url(); ?>">
                                <center><img src="<?= base_url('res/me_logo_fa_3.png'); ?>" style="margin-bottom: 5em;" width="200px"></center>
                            </a>
                            <!-- Header -->
                            <div class="px-30 py-10">
                                <h1 class="h3 font-w700 mt-30 mb-10 text-dark">Welcome to your OTT Dashboard</h1>
                                <h2 class="h5 font-w400 text-muted mb-0">Login to CMS.</h2>
                                <?= $htmlFlashdata ?>
                            </div>
                            <!-- END Header -->

                            <!-- Sign In Form -->
                            <!-- jQuery Validation functionality is initialized with .js-validation-signin class in js/pages/op_auth_signin.min.js which was auto compiled from _es6/pages/op_auth_signin.js -->
                            <!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
                            <form class="js-validation-signin px-30" action="<?= base_url('login/login'); ?>" method="POST">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <div class="form-material floating text-dark">
                                            <input type="text" class="form-control text-black" id="username" name="username" required>
                                            <label for="login-username">Username or Email</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <div class="form-material floating text-dark">
                                            <input type="password" class="form-control text-black" id="password" name="password" required>
                                            <label for="login-password">Password</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <!-- <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="login-remember-me" name="login-remember-me">
                                                <label class="custom-control-label" for="login-remember-me">Remember Me</label>
                                            </div> -->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-sm btn-hero btn-noborder btn-light text-black-op" name="login" data-toggle="click-ripple">
                                        <i class="si si-login mr-10"></i>Login
                                    </button>
                                    <!-- <div class="mt-30">
                                        <a class="link-effect text-muted mr-10 mb-5 d-inline-block forget-password" href="">
                                            <i class="fa fa-warning mr-5"></i> Forgot Password
                                        </a>
                                    </div> -->
                                </div>
                            </form>
                            <!-- END Sign In Form -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Page Content -->

        </main>
        <!-- END Main Container -->
    </div>
    <!-- END Page Container -->

    <!-- Reset Password -->
    <div class="modal fade newModal" tabindex="-1" role="dialog" aria-labelledby="newModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg flipInX animated" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">RESET PASSWORD</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" method="POST" action="<?= base_url('auth/resetpassword'); ?>">
                        <div class="form-group">
                            <label for="emailAdress" class="col-12 control-label"><b>Your Email Account</b></label>
                            <div class="col-12">
                                <input type="email" class="form-control" id="user_name" name="user_name" placeholder="Please Input Your Email" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" name="login" style="border-color: #F89938; border-radius:10px; background-color: #F89938;cursor:pointer;">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END Reset Password -->


    <!--
            Codebase JS Core

            Vital libraries and plugins used in all pages. You can choose to not include this file if you would like
            to handle those dependencies through webpack. Please check out assets/_es6/main/bootstrap.js for more info.

            If you like, you could also include them separately directly from the assets/js/core folder in the following
            order. That can come in handy if you would like to include a few of them (eg jQuery) from a CDN.

            assets/js/core/jquery.min.js
            assets/js/core/bootstrap.bundle.min.js
            assets/js/core/simplebar.min.js
            assets/js/core/jquery-scrollLock.min.js
            assets/js/core/jquery.appear.min.js
            assets/js/core/jquery.countTo.min.js
            assets/js/core/js.cookie.min.js
        -->
    <script src="<?= base_url('plugin/codebaseadmin/js/codebase.core.min.js'); ?>"></script>

    <!--
            Codebase JS

            Custom functionality including Blocks/Layout API as well as other vital and optional helpers
            webpack is putting everything together at assets/_es6/main/app.js
        -->
    <script src="<?= base_url('plugin/codebaseadmin/js/codebase.app.min.js'); ?>"></script>

    <!-- Page JS Plugins -->
    <script src="<?= base_url('plugin/codebaseadmin/js/plugins/jquery-validation/jquery.validate.min.js'); ?>"></script>

    <!-- Page JS Code -->
    <script src="<?= base_url('plugin/codebaseadmin/js/pages/op_auth_signin.min.js'); ?>"></script>

    <script>
        jQuery('document').ready(function() {
            jQuery('.forget-password').click(function() {
                jQuery('.newModal').modal();
            });
            jQuery(".toggle-password").click(function() {
                jQuery(this).toggleClass("fa-eye fa-eye-slash");
                var input = jQuery(jQuery(this).attr("toggle"));
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        });
    </script>
</body>

</html>