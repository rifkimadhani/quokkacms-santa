<?php
/**
 * File ini berisikan script2 kecil yg di pergunakan utk menampilkan dialog,
 * close preview pada filemanager
 *
 * dll...
 *
 * Created by PhpStorm.
 * User: erick
 * Date: 1/4/2023
 * Time: 1:46 PM
 */
?>
<style>
    /*style ini  utk multi select*/
    .select2 {
        width:100%!important;
    }
</style>

<!--scripts.php-->
<script>

    function initDataTableOptions(object) {
        // Show and Hide table column
        // trigger: button Options
        var arraycolumndisplay = object.columns().visible();
        var arraycolumnname = object.columns().header().toArray().map((x) => x.innerText);
        for (var i = 0; i < arraycolumndisplay.length - 1; i++) {
            var checked = '';
            if (arraycolumndisplay[i]) checked = 'checked';
            jQuery('.checkboxdisplay').append(
                '<div class="col-6"><label class="css-control css-control-primary css-switch">' +
                '<input type="checkbox" ' +
                checked +
                ' data-column="' +
                i +
                '" class="css-control-input toggle-vis"><span class="css-control-indicator"></span>'+ arraycolumnname[i] +'</label></div>'
            );
        }

        jQuery('.toggle-vis').change(function () {
            var column = object.column($(this).attr('data-column'));
            column.visible(!column.visible());
        });

        jQuery('.showOptionsModal').click(function () {
            jQuery('#modal-checkbox').modal();
        });
    }

    // options for export to xml
    jQuery('.showExportModal').click(function () {
            jQuery('#modal-export').modal();
        });


    //call ini apabila ada multi select, spt room pada subscriber
    //
    function initSelectMultiple() {
        //handle select-multiple
        //https://select2.org/
        $('.js-example-basic-multiple').select2();
    }

    //tampilkan dialog berdasarkan nama dialog
    //
    function showDialog(dialogName) {
        $(dialogName).modal();
    }

    //tampilkan dialog utk delete
    //
    function showDialogDelete(formId, message, callback) {
        $('.dialog'+formId).modal();
        $('.dialog'+formId + ' #message'+formId).text(message);
        $('.dialog'+formId + ' #btnDelete'+formId).on('click', function() {
            callback();
        });
    }

    //toggle password
    $(document.body).on("click", ".toggle-password", function(event)
    {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password")
        {
            input.attr("type", "text");
        }
        else
        {
            input.attr("type", "password");
        }
    });

</script>
<!--scripts.php end-->
