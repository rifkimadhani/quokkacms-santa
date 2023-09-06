<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-05 10:56:27
 */

use App\Libraries\Dialog;

$htmlAssoc = $form->renderPlainDialog('formAssoc');
$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New package', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete package', 'CONFIRM DELETE');
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

<?=$htmlAssoc?>
<?=$htmlEdit?>
<?=$htmlNew?>
<?=$htmlDelete?>

<?=view('util/scripts.php')?>


<script>
    const urlSsp = "<?= $baseUrl ?>/ssp";
    const lastCol = <?= count($fieldList) ?>;
    const dataList = $('#datalist');
    const defaultLiveTvPackage = <?=$defaultLiveTvPackage?>;
    var dataTable;

    //exec when ready
    $('document').ready(function () {
        initDataTable();
    });

    function initDataTable() {
        dataTable = dataList.DataTable(
            {
                ajax: urlSsp,
                responsive: true,
                scrollX: true,
                serverSide: true,
                pageLength: 10,
                order: [['0','desc']],
                columnDefs: [
                    {
                        //hide your cols here, enter index of col into targets array
                        targets: [],visible: false,searchable: false
                    },
                    {
                        //modify nama theme, shg include default sign
                        targets:[1], render: function(value, type, row)
                        {
                            const id = row[0];

                            //apabila theme ini adalah default theme maka berikan  tanda
                            if (id==defaultLiveTvPackage){
                                return value + " (default)"; //add tanda * sbg default theme
                            }
                            return value;
                        }
                    },
                    {
                        // action column
                        targets: lastCol,
                        className: "text-center",
                        defaultContent: '<a onclick="onClickTrash(event, this);" href="javascript:;"> <i class="fa fa-trash fa-2x"></i></a> <a onclick="onShowDialogAssoc(event, this);" href="javascript:;"> <i class="fa fa-dot-circle-o fa-2x"></i></a>'
                    }

                ]
            });

        // handle click on row,
        //  1. ambil dialog yg sdh di isikan dgn data dari server
        //  2. kemudian dialog tsb akan di tampilkan
        //
        dataList.find('tbody').on( 'click', 'tr', function (event)
        {
            event.stopPropagation();
            const data = dataTable.row( $(this)).data();

            //get pk from data
            const packageId = data[0];

            const url = "<?=$baseUrl?>/edit/" + packageId;

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
        const packageId = data[0];


        //please correct the index for name variable, sehingga message delete akan terlihat benar
        const name = data[1];

        showDialogDelete('formDelete', 'Are you sure to delete ' + name, function () {
            window.location.href = "<?=$baseUrl?>/delete/" + packageId;
        })
    }

    function onShowDialogAssoc(event, that) {
        event.stopPropagation();

        const data = dataTable.row( $(that).parents('tr') ).data();
        const packageId = data[0];

        const url = "<?=$baseUrl?>/assoc/" + packageId;

        console.log(url);

        $.ajax({url: url}).done(function(result)
        {
            $('.dialogformAssoc').html(result);
            $('.dialogformAssoc').modal();
        })
            .always(function()
            {
                jQuery('#overlay-loader-indicator').hide();
            });
    }
</script>