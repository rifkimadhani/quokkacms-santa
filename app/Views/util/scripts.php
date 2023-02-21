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

    //handle click button browse
    $(document.body).on( 'click', '.input-group-addon', function (event)
    {
        $('#modal-galery').modal();
        const inputId = $(this).attr('input-id');
        const formId = $(this).attr('form-id');

        //hasil pilihan akan masuk ke input formId_inputId
        const returnId = formId + "_" + inputId; //filemanager akan fill input ini

        const filemanagerurl    = $('#modal-galery').find('iframe').attr('src');

        //filemanager akan otomatis fill returnId
        const filemanagerurlnew = uriquerystring.updateQueryStringParameter(filemanagerurl,'field_id', returnId);
        $('#modal-galery').find('iframe').attr('src',filemanagerurlnew);

        //simpan formId dan inputId
        localStorage.setItem('formId', formId);
        localStorage.setItem('inputId', inputId);
    });

    //handle saat filemanager dialog close
    $('#modal-galery').on('hidden.bs.modal', function (event)
    {
        const inputId = localStorage.getItem('inputId');
        const formId = localStorage.getItem('formId');
        const returnId = formId + "_" + inputId;
        const newurlvalue = $("#"+returnId).val();

//        newurlvalue = returnValue;

        if(newurlvalue.length > 0)
        {
            const imagetoupload  = newurlvalue.replace(/[\[\]["]+/g,'').split(",");

//            console.log(imagetoupload);

            $.each(imagetoupload,function(index,value)
            {
                $('#images-preview-'+formId+'-'+inputId).prepend('<div class="img" style="background-image: url(' + value + ');"><span>remove</span></div>');
//                $('.images-preview').prepend('<div class="img" style="background-image: url(' + value + ');"><span>remove</span></div>');
            });

            const arUrl = getUrlImagePreview(formId, inputId);
            $("#" + formId + " input[name="+inputId+"]").val(arUrl.toString());
        }
    });

    //ambil semua url dari img
    //
    function getUrlImagePreview(formId, inputId) {
        const elementimages = $('#' + formId + " input[name="+inputId+"]").parent().next().children();
        var newarrayimage = [];

        $.each(elementimages,function(idx, val)
        {
            const imagestoupload = $(this).css("background-image");
            const stringimageulr = imagestoupload.replace('url(','').replace(')','').replace(/\"/gi, "");
            newarrayimage.push(stringimageulr);
        });

        return newarrayimage;
    }

    //handle klik remove image pada filemanager
    //
    $(document.body).on( 'click', '.images-preview .img', function (event)
    {
        const divImagePreview = $(this).parent(); //div image-preview
        const formId = divImagePreview.attr('form-id');
        const inputId = divImagePreview.attr('input-id');

        //ambil urlImage dari preview
        const background = $(this).css('background-image');
        const urlimage   = background.replace('url(','').replace(')','').replace(/\"/gi, "");

        var urlvalue   = $("#"+formId+ " input[name="+inputId+"]").val().split(",");
        var newurl     = [];
        $.each(urlvalue, function( index, value )
        {
            if(value != urlimage)
            {
                newurl.push(value);
            }
        });
        $("#"+formId+" input[name="+inputId+"]").val(newurl.toString());
        $(this).remove();
    });

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
