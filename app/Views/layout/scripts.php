<!--
    Codebase JS

    Custom functionality including Blocks/Layout API as well as other vital and optional helpers
    webpack is putting everything together at assets/_es6/main/app.js
-->
<script src="<?= base_url('plugin/codebaseadmin/js/codebase.app.min.js'); ?>"></script>

<!-- Page JS Plugins -->
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/chartjs/Chart.bundle.min.js'); ?>"></script>
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/slick/slick.min.js'); ?>"></script>
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/bootstrap-notify/bootstrap-notify.min.js'); ?>"></script>
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/es6-promise/es6-promise.auto.min.js'); ?>"></script>
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/sweetalert2/sweetalert2.min.js'); ?>"></script>

<!-- Page JS Code -->
<script src="<?= base_url('plugin/codebaseadmin/js/pages/be_pages_dashboard.min.js'); ?>"></script>
<script src="<?= base_url('plugin/codebaseadmin/js/pages/be_tables_datatables.min.js'); ?>"></script>
<script src="<?= base_url('plugin/codebaseadmin/js/pages/be_ui_activity.min.js'); ?>"></script>


<!-- Sync from old template -->
<!-- Bootstrap -->
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('plugin/adminlte/bower_components/fastclick/lib/fastclick.js') ?>"></script>
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('plugin/RowReorder-1.2.6/js/dataTables.rowReorder.min.js') ?>"></script>
<script src="<?= base_url('plugin/adminlte/bower_components/inputmask/dist/jquery.inputmask.bundle.js') ?>"></script>
<script src="<?= base_url('plugin/codebaseadmin/js/plugins/jquery-slimscroll/jquery.slimscroll.js') ?>"></script>
<script src="<?= base_url('plugin/js/datetostring.js'); ?>"></script>
<script src="<?= base_url('plugin/js/uriquerystring.js'); ?>"></script>
<!--<script src="--><? //= base_url('plugin/js/modalutil.js'); 
                    ?><!--"></script>-->
<script src="<?= base_url('plugin/js/videohls.js'); ?>" type="text/javascript"></script>

<!-- menu active -->
<script>
    var baseurl = "<?php echo base_url() ?>";
    var url = window.location;
    var urlsplit = url.href.split('/');
    var fullurl = urlsplit[0] + '/' + urlsplit[1] + '/' + urlsplit[2] + '/' + urlsplit[3] + '/' + urlsplit[4];
    // console.log(fullurl);

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

<!-- sidebar toggle button -->
<script>
    // Function to update the data-action attribute based on screen size
    function updateDataAction() {
        const btn = document.getElementById('sidebarToggleBtn');
        const dataAction = window.innerWidth >= 992 ? 'sidebar_mini_toggle' : 'sidebar_toggle';
        btn.setAttribute('data-action', dataAction);
    }

    // Initial call to set data-action based on initial screen size
    updateDataAction();

    // Add event listener to update data-action when the window is resized
    window.addEventListener('resize', updateDataAction);
</script>

<!-- Save theme's layout -->
<script>
    // Function to apply the selected theme
    function applyTheme() {
        const selectedTheme = localStorage.getItem('selectedTheme');

        // check if a theme is selected & Apply the selected theme by setting the CSS link
        if (selectedTheme) {
            document.getElementById('css-theme').setAttribute('href', selectedTheme);
        }

        // retrieve the saved sidebar style
        const sidebarStyle = localStorage.getItem('sidebarStyle');

        // check if a sidebar style is saved and apply it
        if (sidebarStyle === 'dark') {
            // sidebar dark
            document.getElementById('page-container').classList.add('sidebar-inverse');
        } else {
            // sidebar light
            document.getElementById('page-container').classList.remove('sidebar-inverse');
        }
    }

    // Event handler for theme change
    document.querySelectorAll('[data-toggle="theme"]').forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const selectedTheme = this.getAttribute('data-theme');
            localStorage.setItem('selectedTheme', selectedTheme);
            applyTheme();
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const themeButtons = document.querySelectorAll('[data-toggle="theme"]');
        const sidebarLightButton = document.getElementById('sidebar-light');
        const sidebarDarkButton = document.getElementById('sidebar-dark');

        // theme change
        themeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const selectedTheme = this.getAttribute('data-theme');
                localStorage.setItem('selectedTheme', selectedTheme);
                applyTheme();
            });
        });

        // sidebar style change
        sidebarLightButton.addEventListener('click', function() {
            localStorage.setItem('sidebarStyle', 'light');
        });

        sidebarDarkButton.addEventListener('click', function() {
            localStorage.setItem('sidebarStyle', 'dark');
        });

        applyTheme();
    });

    // Function to reset the theme to default
    function resetThemeToDefault() {
        const defaultThemeLink = document.getElementById('default-theme');
        const defaultTheme = defaultThemeLink.getAttribute('data-theme');

        localStorage.removeItem('selectedTheme');
        document.getElementById('css-theme').setAttribute('href', defaultTheme);
        localStorage.setItem('sidebarStyle', 'dark');
        applyTheme();
    }

    // Event handler for the reset button/link
    document.getElementById('reset-theme-button').addEventListener('click', resetThemeToDefault);
</script>