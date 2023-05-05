<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

<title>OTT SERVER | Management</title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- Icons -->
<!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
<link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('plugin/codebaseadmin/favicons/apple-touch-icon.png') ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('plugin/codebaseadmin/favicons/favicon-32x32.png') ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('plugin/codebaseadmin/favicons/favicon-16x16.png') ?>">
<link rel="manifest" href="<?= base_url('plugin/codebaseadmin/favicons/site.webmanifest') ?>">
<!-- END Icons -->

<!-- Stylesheets -->

<!-- Page JS Plugins CSS -->
<link rel="stylesheet" href="<?= base_url('plugin/codebaseadmin/js/plugins/slick/slick.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugin/codebaseadmin/js/plugins/slick/slick-theme.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugin/codebaseadmin/js/plugins/sweetalert2/sweetalert2.min.css') ?>">

<!-- Fonts and Codebase framework -->
<link rel="stylesheet" href="<?= base_url('plugin/codebaseadmin/css/muli.css') ?>">
<link rel="stylesheet" id="css-main" href="<?= base_url('plugin/codebaseadmin/css/codebase.min.css') ?>">

<!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
<link rel="stylesheet" id="css-theme" href="<?= base_url('plugin/codebaseadmin/css/themes/earth.min.css') ?>">
<!-- END Stylesheets -->

<!-- Sync Plugin from old template -->
<!-- Bootstrap -->
<link rel="stylesheet" href="<?= base_url('plugin/adminlte/bower_components/font-awesome/css/font-awesome.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugin/adminlte/bower_components/Ionicons/css/ionicons.min.css') ?>">
<!-- <link rel="stylesheet" href="<?//= base_url('plugin/adminlte/bower_components/select2/dist/css/select2.min.css') ?>"> -->
<link rel="stylesheet" href="<?= base_url('plugin/codebaseadmin/js/plugins/select2/css/select2.min.css') ?>">

<!-- <link rel="stylesheet" href="<?//= base_url('plugin/adminlte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') ?>"> -->
<link rel="stylesheet" href="<?= base_url('plugin/codebaseadmin/js/plugins/datatables/dataTables.bootstrap4.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugin/RowReorder-1.2.6/css/rowReorder.dataTables.min.css') ?>">
<!-- <link rel="stylesheet" href="<?//= base_url('plugin/bootstrap-colorpicker-2.5.3/dist/css/bootstrap-colorpicker.min.css')?>"> -->
<link rel="stylesheet" href="<?= base_url('plugin/codebaseadmin/js/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')?>">
<link rel="stylesheet" href="<?= base_url('plugin/css/select.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugin/css/stepwizard.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugin/css/stockphoto.css') ?>">
<!-- <link rel="stylesheet" href="<?//= base_url('plugin/bootstrap-datepicker-1.9.0-dist/css/bootstrap-datepicker3.standalone.css')?>"> -->
<link rel="stylesheet" href="<?= base_url('plugin/codebaseadmin/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.css')?>">

<!-- jQuery -->
<script src="<?= base_url('plugin/codebaseadmin/js/codebase.core.min.js'); ?>"></script>
<!-- <script src="<?//= base_url('plugin/adminlte/bower_components/jquery/dist/jquery.min.js') ?>"></script> -->
<script src="<?= base_url('plugin/jstree/dist/jstree.min.js') ?>"></script>
<!-- <script src="<?//= base_url('plugin/jquery.validate-1.11.1/jquery.validate.js') ?>"></script> -->
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/jquery-validation/jquery.validate.js') ?>"></script>
<!-- <script src="<?//= base_url('plugin/ckeditor_4.10.0/ckeditor.js')?>"></script> -->
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/ckeditor/ckeditor.js')?>"></script>
<!-- <script src="<?//= base_url('plugin/bootstrap-colorpicker-2.5.3/dist/js/bootstrap-colorpicker.min.js')?>"></script> -->
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')?>"></script>
<script src="<?= base_url('plugin/hls-js/hls.js')?>"></script>

<!-- JSON Editor -->
<!-- <link rel="stylesheet" href="<?//= base_url('plugin/codebaseadmin/css/jsoneditor.min.css');?>" /> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.10.0/jsoneditor.min.css" />
<style>
    /* Customize header color of JSON editor */
    .jsoneditor {
        border: thin solid #424242;
    }
    .jsoneditor-menu {
        background-color: #424242;
    }
    .jsoneditor-menu .jsoneditor-label {
        color: #ffffff;
    }

    /* Customize Scrollbar */
    /* width */
    ::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 30px;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* FOR THEME PAGE UI */
    .grid-item {
        display: inline-block;
        width: 48%;
        margin: 1%;
        border: 1px solid #ccc;
        padding: 10px;
        box-sizing: border-box;
        vertical-align: top;
    }

    .grid-item-name {
        font-weight: bold;
        margin-bottom: 10px;
    }

    .grid-item-image {
        text-align: center;
        margin-bottom: 10px;
    }

    .urlimage {
        max-width: 100%;
        max-height: 100%;
    }
</style>