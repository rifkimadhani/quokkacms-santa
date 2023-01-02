<?php
use App\Models\MessageForm;
use App\Models\RoomModel;
use App\Models\SubscriberModel;
use App\Libraries\Dialog;

$room = new RoomModel();
$roomData = $room->getForSelect();

$subscriber = new SubscriberModel();
$subscriberData = $subscriber->getCheckinForSelect();

$form = new MessageForm($subscriberData, $roomData);

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('NEW', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('DELETE', 'formDelete');
?>
<div class="box">
    <div class="box">
    <div class="box-header">
        <div class="row">
            <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 col-4">
                    <a href="javascript:;" role="button" class="btn btn-primary showNewModal" onclick="showDialog('.dialogformNew')">
                        CREATE
                    </a>
            </div>
            <div class="col-xl-8 col-lg-6 col-md-5 col-sm-4 col-8">
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-5 col-12 text-right">
                <a href="javascript:;" role="button" class="btn btn-success showOptionsModal">
                    OPTIONS
                </a>
            </div>
        </div>
    </div>
    <div class="box-body table-responsive padding">
            <table id="datalist" class="table table-bordered table-hover table-striped" style="width:100%">
                <thead>
                <tr>
                    <?php foreach ($fieldList as $field): ?>
                        <th><?=$field?></th>
                    <?php endforeach;?>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
    </div>
</div>
</div>

<?=$htmlEdit?>
<?=$htmlNew?>
<?=$htmlDelete?>

<script>
    var dataTable;
    const primaryKey = "<?=$primaryKey?>";
    const urlSsp = "<?= $baseUrl ?>/ssp";
    const lastCol = <?= count($fieldList) ?>;

    //exec when ready
    $('document').ready(function () {
        initDataTable();

//        installClick();
//        initDialog();
    });

    function initDataTable() {
        dataTable = $('#datalist')
            .DataTable(
            {
                ajax: urlSsp,
                pageLength: 100,
                order: [['0','desc']],
                columnDefs: [
                    {
                        targets: [1, 3, 4, 5],visible: false,searchable: false
                    },
                    {
                        //action column
                        targets: lastCol,
                        className: "center",
                        defaultContent: '<a onclick="onClickTrash(event, this);" href="javascript:;"> <i class="fa fa-trash fa-2x"></i></a>'
                    }

                ]
            });

        //handle click on row
        //
        $('#datalist tbody').on( 'click', 'tr', function (event)
        {
            event.stopPropagation();
            const data = dataTable.row( $(this)).data();
            const value = data[0];
            const url = "<?=$baseUrl?>/edit/" + value;

            //show hourglass
            jQuery('#overlay-loader-indicator').show();

            $.ajax({url: url}).done(function(result)
            {
                $('.dialogformEdit').html(result);
                $('.dialogformEdit').modal();
            })
            .always(function()
            {
                jQuery('#overlay-loader-indicator').hide();
            });
        });

    }

    //attach submit pada form
//    function initDialog() {
//        $('#formEdit').on('submit', function() {
//
//            //ambila value dari child of form
//            const v = $('#password').val();
//            console.log(v);
//
//            return true;
//        });
//
//        $('#formNew').on('submit', function() {
//
//            const v = $('#formNew #password').val();
//            console.log(v);
//
//            return false;
//        });
//    }

    //
    //
    function onClickTrash(event, that) {
        event.stopPropagation();

        const data = dataTable.row( $(that).parents('tr') ).data();
        const id = data[0];

        showDialogDelete('formDelete', 'Are you sure to delete message #' + id, function () {
            window.location.href = "<?=$baseUrl?>/delete/" + id;
        })
    }

    //
    //
    function showDialog(dialogName) {
        $(dialogName).modal();
    }
//    function hideDialog(dialogName) {
//        $(dialogName).modal('hide');
//    }

    function showDialogDelete(formId, message, callback) {
        $('.dialog'+formId).modal();
        $('.dialog'+formId + ' #message'+formId).text(message);
        $('.dialog'+formId + ' #btnDelete'+formId).on('click', function() {
            callback();
        });
    }
</script>

<!--filemanager-->
<div class="modal fade" id="modal-galery" style="min-width:1200px" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:1000px;margin-left:-200px;">
            <div class="modal-body">
                <iframe src="filemanager/filemanager/dialog.php" width="100%" height="600px;" style="border: 0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>

//    function installClick() {
//        $('#formNew_url_image').on('click', function () {
//            showFilemanager();
//        })
//    }

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

    //handle klik remove image
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


