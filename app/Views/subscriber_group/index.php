<?php
use App\Models\SubscriberGroupForm;
use App\Libraries\Dialog;

$form = new SubscriberGroupForm();

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New Group', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete Group', 'CONFIRM DELETE');
?>

<?=$htmlEdit?>
<?=$htmlNew?>
<?=$htmlDelete?>
<!--<section class="content-header">-->
<!--    <h1>Guest group</h1>-->
<!--</section>-->

    <div class="block-content block-content-full border-b clearfix" style="padding-top:0px">
    <div>
    </div>
        <a class="btn btn-secondary showNewModal" href="javascript:;" role="button" onclick="showDialog('.dialogformNew')">
            <i class="fa fa-plus text-primary mr-5 "></i> Create Group
        </a>
        <div class="btn-group float-right">
            <a class="btn btn-secondary showOptionsModal" href="javascript:;" role="button" data-target="#modal-checkbox">
                Options <i class="fa fa-th-large text-primary ml-5"></i>
            </a>
        </div>
    </div>
    <div class="block-content block-content-full table-responsive">
        <table id="datalist" class="table table-bordered table-hover table-striped table-vcenter">
            <thead>
                <tr> 
                    <?php foreach ($fieldList as $field): ?>
                        <th><?=$field?></th>
                    <?php endforeach;?>
                    <th style="width: 5%;">Action</th>
                </tr>
            </thead>
        </table>
    </div>


<script>
    var dataTable;
    const urlSsp = "<?= $baseUrl ?>/ssp";
    const lastCol = <?= count($fieldList) ?>;

    //exec when ready
    $('document').ready(function () {
        initDataTable();
        // initDialog();
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
                        targets: [],visible: false,searchable: false
                    },
                    {
                        // action column
                        targets: lastCol,
                        className: "text-center",
                        defaultContent: '<a onclick="onClickTrash(event, this);" href="javascript:;"> <i class="fa fa-trash fa-2x"></i></a>'
                    }

                ]
            });

        // handle click on row
        $('#datalist tbody').on( 'click', 'tr', function (event)
        {
            event.stopPropagation();
            const data = dataTable.row( $(this)).data();
            const value = data[0];
            const url = "<?=$baseUrl?>/edit/" + value;

            // show hourglass
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

        // Show and Hide table column 
        // trigger: button Options
        var arraycolumndisplay = dataTable.columns().visible();
        var arraycolumnname = dataTable.columns().header().toArray().map((x) => x.innerText);
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
            var column = dataTable.column($(this).attr('data-column'));
            column.visible(!column.visible());
        });

        jQuery('.showOptionsModal').click(function () {
            jQuery('#modal-checkbox').modal();
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
        const groupId = data[0];
        const name = data[1];

        showDialogDelete('formDelete', 'Are you sure to delete group ' + name, function () {
            window.location.href = "<?=$baseUrl?>/delete/" + groupId;
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