<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 1/4/2023
 * Time: 1:53 PM
 */
?>
<!--filemanager-->
<div class="modal fade" id="modal-galery" style="min-width:1200px" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:1000px;margin-left:-200px;">
            <div class="modal-body">
                <iframe src="<?= base_url('filemanager/filemanager/dialog.php'); ?>" width="100%" height="600px;" style="border: 0"></iframe>
            </div>
        </div>
    </div>
</div>
<script>
    //handle click button browse
    $(document.body).on( 'click', '.input-group-addon', function (event)
    {
        $('#modal-galery').modal();
        const inputId = $(this).attr('input-id');
        const formId = $(this).attr('form-id');

        //hasil pilihan akan masuk ke input formId_inputId
        const returnId = formId + "_" + inputId; //filemanager akan fill input ini

        const filemanagerurl = $('#modal-galery').find('iframe').attr('src');

        //filemanager akan otomatis fill returnId
        const filemanagerurlnew = uriquerystring.updateQueryStringParameter(filemanagerurl, 'field_id', returnId);
        $('#modal-galery').find('iframe').attr('src', filemanagerurlnew);

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
        const newurlvalue = $("#" + returnId).val();

        // newurlvalue = returnValue;

        if(newurlvalue.length > 0)
        {
            const imagetoupload  = newurlvalue.replace(/[\[\]["]+/g,'').split(",");

            // console.log(imagetoupload);

            $.each(imagetoupload,function(index,value)
            {
                $('#images-preview-'+formId+'-'+inputId).prepend('<div class="img" style="background-image: url(' + value + ');"><span>remove</span></div>');
//                $('.images-preview').prepend('<div class="img" style="background-image: url(' + value + ');"><span>remove</span></div>');
            });

            const arUrl = getUrlImagePreview(formId, inputId);
            $("#" + formId + " input[name="+inputId+"]").val(arUrl.toString());
        }

        // kembalikan scroll focus ke modal dialog 
        $('body').addClass('modal-open');

        // padding-right: 0px; to correctly sized and positioned 'body' element saat modal di close
        $('body').css('padding-right', '0px');
    });

    //ambil semua url dari img
    //
    function getUrlImagePreview(formId, inputId) {
        const elementimages = $('#' + formId + " input[name=" + inputId + "]").parent().next().children();
        var newarrayimage   = [];

        $.each(elementimages,function(idx, val)
        {
            const imagestoupload = $(this).css("background-image");
            const stringimageulr = imagestoupload.replace('url(', '').replace(')', '').replace(/\"/gi, "");
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
        $("#" + formId + " input[name=" + inputId + "]").val(newurl.toString());
        $(this).remove();
    });
</script>
<!--filemanager end-->