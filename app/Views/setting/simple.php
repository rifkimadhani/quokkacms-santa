<?php

/**
 * Created by PageBuilder.
 * Date: 2023-06-06 12:33:41
 */

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
?>

<div class="block-content block-content-full border-b clearfix" style="padding-top:0px">
    <div class="btn-group float-right">
        <a class="btn btn-secondary" href="javascript:;" role="button" onclick="onClickBack()">
            Advanced Settings <i class="fa fa-gears text-primary ml-5"></i>
        </a>
    </div>
</div>
<div class="block-content block-content-full">
    <table id="datalist" class="table table-borderless table-vcenter" width="100%">
        <thead style="display: none;">
            <tr>
                <?php foreach ($fieldList as $field) : ?>
                    <th class="d-none d-sm-table-cell" style="width: 100%;"><?= $field; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
    </table>
</div>

<div class="block-content block-content-full">
    <a href="https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
" target="_blank" style="padding-left: 10px;"><i class="si si-info"></i> List Time Zone</a>
</div>

<?= $htmlEdit ?>

<?= view('util/scripts.php') ?>


<script>
    const urlSsp = "<?= $baseUrl ?>/sspSimple";
    const dataList = $('#datalist');
    var dataTable;

    //exec when ready
    $('document').ready(function() {
        initDataTable();

        // showDialog('.dialogformNew');
    });

    function initDataTable() {
        dataTable = dataList.DataTable({
            ajax: urlSsp,
            serverSide: true,
            responsive: true,
            scrollX: true,
            searching: false,
            paging: false,
            info: false,
            order: [
                ['0', 'asc']
            ],
            columnDefs: [{
                    //hide your cols here, enter index of col into targets array
                    targets: [0],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [1],
                    className: "font-w700 text-uppercase"
                },
                {
                    targets: [2],
                    className: "border border-primary"
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

            const url = "<?= $baseUrl ?>/editSetting/" + settingId;

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
    }

    function onClickBack() {
        event.stopPropagation();
        window.location.href = "<?= $baseUrl ?>";
    }
</script>