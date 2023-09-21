<?php

/**
 * Created by PageBuilder.
 * Date: 2023-06-06 12:33:41
 */

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New setting', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete setting', 'CONFIRM DELETE');
?>

<div class="block-content block-content-full border-b clearfix" style="padding-top:0px">
    <a class="btn btn-secondary showNewModal" href="javascript:;" role="button" onclick="showSimple()">
        <i class="fa fa-gear text-primary mr-5 "></i> Simple Settings
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
                <?php foreach ($fieldList as $field) : ?>
                    <th><?= $field ?></th>
                <?php endforeach; ?>
                <!-- <th style="width: 5%;">Action</th> -->
            </tr>
        </thead>
    </table>
</div>

<?= $htmlEdit ?>
<?= $htmlNew ?>
<?= $htmlDelete ?>

<?= view('util/scripts.php') ?>


<script>
    const urlSsp = "<?= $baseUrl ?>/ssp";
    const lastCol = <?= count($fieldList) ?>;
    const dataList = $('#datalist');
    var dataTable;

    //exec when ready
    $('document').ready(function() {
        initDataTable();
    });

    function initDataTable() {
        dataTable = dataList.DataTable({
            ajax: urlSsp,
            serverSide: true,
            responsive: true,
            scrollX: true,
            pageLength: 100,
            order: [
                ['0', 'asc']
            ],
            columnDefs: [{
                    //hide your cols here, enter index of col into targets array
                    targets: [6],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [1],
                    width: 100
                },
                {
                    targets: [5, 6],
                    render: function(data) {
                        if (data) {
                            return datetostring.datetimetoindonesia(data)
                        }
                        return '';
                    }
                }

            ]
        });

        // handle click on row,
        //  1. ambil dialog yg sdh di isikan dgn data dari server
        //  2. kemudian dialog tsb akan di tampilkan
        //
        dataList.find('tbody').on('click', 'tr', function(event) {
            event.stopPropagation();
            const data = dataTable.row($(this)).data();

            //get pk from data
            const settingId = data[0];

            const url = "<?= $baseUrl ?>/edit/" + settingId;

            // show hourglass
            jQuery('#overlay-loader-indicator').show();

            $.ajax({
                    url: url
                }).done(function(result) {
                    $('.dialogformEdit').html(result);
                    $('.dialogformEdit').modal();
                })
                .always(function() {
                    jQuery('#overlay-loader-indicator').hide();
                });
        });

        initDataTableOptions(dataTable);
    }

    //
    //
    function onClickTrash(event, that) {
        event.stopPropagation();

        const data = dataTable.row($(that).parents('tr')).data();
        const settingId = data[0];


        //     //please correct the index for name variable, sehingga message delete akan terlihat benar
        const name = data[0];

        showDialogDelete('formDelete', 'Are you sure to delete ' + name, function() {
            window.location.href = "<?= $baseUrl ?>/delete/" + settingId;
        })
    }

    function showSimple(event, that) {
        window.location.href = "<?= $baseUrl ?>/simple";
    }
</script>