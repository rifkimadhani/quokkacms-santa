<?php
/**
 * Created by PageBuilder.
 * Date: __TODAY__
 */

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New __TITLE__', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete __TITLE__', 'CONFIRM DELETE');
?>

<div class="block-content block-content-full border-b clearfix" style="padding-top:0px">
    <a class="btn btn-secondary showNewModal" href="javascript:;" role="button" onclick="showDialog('.dialogformNew')">
        <i class="fa fa-plus text-primary mr-5 "></i> Create
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

<?=$htmlEdit?>
<?=$htmlNew?>
<?=$htmlDelete?>

<?=view('util/scripts.php')?>

<!--activekan script ini apabila, dialog memerllukan filemanager-->
<?//= view('util/filemanager.php') ?>

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
                responsive: true,
                scrollX: true,
                pageLength: 100,
                order: [['0','desc']],
                columnDefs: [
                    {
                        //hide your cols here, enter index of col into targets array
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

        // handle click on row,
        //  1. ambil dialog yg sdh di isikan dgn data dari server
        //  2. kemudian dialog tsb akan di tampilkan
        //
        $('#datalist tbody').on( 'click', 'tr', function (event)
        {
            event.stopPropagation();
            const data = dataTable.row( $(this)).data();

            //get pk from data
__pk_value__
            const url = "<?=$baseUrl?>/edit/" + __url_edit__;

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

        initDataTableOptions(dataTable);
    }

    //
    //
    function onClickTrash(event, that) {
        event.stopPropagation();

        const data = dataTable.row( $(that).parents('tr') ).data();
__pk_value__

        //please correct the index for name variable, sehingga message delete akan terlihat benar
        const name = data[0];

        showDialogDelete('formDelete', 'Are you sure to delete ' + name, function () {
            window.location.href = "<?=$baseUrl?>/delete/" + __url_edit__;
        })
    }
</script>