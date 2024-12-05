<?php

/**
 * Created by PageBuilder.
 * Date: 2023-03-30 15:24:23
 */

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New facility', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete facility', 'CONFIRM DELETE');
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
    <table id="datalist" class="table table-bordered table-hover table-striped table-vcenter" style="width: max-content;">
        <thead>
            <tr>
                <?php foreach ($fieldList as $field) : ?>
                    <th><?= $field ?></th>
                <?php endforeach; ?>
                <th style="width: 5%;">Action</th>
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
    const primaryKey = "<?= $primaryKey ?>";

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
            autoWidth: true,
            pageLength: 100,
            order: [
                [4, 'asc']
            ],
            columnDefs: [{
                    //hide your cols here, enter index of col into targets array
                    targets: [0, 3, 5],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [1],
                    width: '20%'
                },
                {
                    targets: [2],
                    className: "text-center",
                    width: '20vw',
                    render: function(value) {
                        return renderImages(value);
                    }
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
        dataList.find('tbody').on('click', 'tr', function(event) {
            event.stopPropagation();
            const data = dataTable.row($(this)).data();

            //get pk from data
            const facilityId = data[0];

            const url = "<?= $baseUrl ?>/edit/" + facilityId;

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
        const facilityId = data[0];


        //please correct the index for name variable, sehingga message delete akan terlihat benar
        const name = data[1];

        showDialogDelete('formDelete', 'Are you sure to delete info Facility ' + name, function() {
            window.location.href = "<?= $baseUrl ?>/delete/" + facilityId;
        })
    }


    function renderImages(value) {
        if (value) {
            var urls = value.split(',');
            var images = '';

            for (var i = 0; i < urls.length; i++) {
                var url = urls[i].trim();
                if (url) {
                    url = url.replace('{BASE-HOST}', '<?= base_url(); ?>');
                    images += '<img src="' + url +
                        '" class="urlimage" style="margin:5px; max-width: 20vw; max-height: auto">';
                }
            }

            return images;
        }

        return ''; // render blank if there's no value
    }
</script>

<?= view('util/filemanager.php') ?>