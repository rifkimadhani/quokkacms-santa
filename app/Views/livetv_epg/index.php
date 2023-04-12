<?php
/**
 * Created by PageBuilder.
 * Date: 2023-04-06 09:47:23
 */

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New livetv_epg', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete livetv_epg', 'CONFIRM DELETE');
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
<div class="block-content block-content-full">
    <!-- <a href="javascript:;" class="btn btn-primary float-right showExportModal" role="button" data-target="#modal-export"> -->
    <a href="javascript:;" class="btn btn-primary float-right" role="button" onclick="$('#modal-export').modal('show')" >
        Export to XML <i class="fa fa-code text-light ml-5"></i>
    </a>
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

<!-- Modal Options -->
<!-- A modal for Export Options */ -->
<div class="modal fade" id="modal-export" tabindex="-1" role="dialog" aria-labelledby="modal-export-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <form action="<?= $baseUrl ?>/export" method="post">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Export Options</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="form-group row">
                            <label class="col-12" for="livetv-id">Channel</label>
                            <div class="col-md-12">
                                <select class="form-control" id="livetv-id" name="livetv_id">
                                    <?php foreach ($livetvData as $item): ?>
                                        <option value="<?= $item['value']; ?>"><?= $item['value']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="start-date">Date Range</label>
                            <div class="col-lg-12">
                                <div class="input-group">
                                    <input type="datetime-local" class="form-control" id="start-date" name="start_date">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">to</span>
                                    </div>
                                    <input type="datetime-local" class="form-control" id="end-date" name="end_date" placeholder="End Date" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="offset">Offset (hours)</label>
                            <div class="col-lg-12">
                                <input type="number" class="form-control" id="offset" name="offset" min="-12" max="12" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-alt-success" >
                        <i class="fa fa-check"></i> Export
                    </button>
                </div>
            </form>       
        </div>
    </div>
</div>



<?=$htmlEdit?>
<?=$htmlNew?>
<?=$htmlDelete?>

<?=view('util/scripts.php')?>


<script>
    const urlSsp = "<?= $baseUrl ?>/ssp";
    const lastCol = <?= count($fieldList) ?>;
    const dataList = $('#datalist');
    const primaryKey = "<?=$primaryKey?>";
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
                pageLength: 100,
                order: [['0','desc']],
                columnDefs: [
                    {
                        //hide your cols here, enter index of col into targets array
                        targets: [1,8,9,10],visible: false,searchable: false
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
        dataList.find('tbody').on( 'click', 'tr', function (event)
        {
            event.stopPropagation();
            const data = dataTable.row( $(this)).data();

            //get pk from data
            const epgId = data[0];

            const url = "<?=$baseUrl?>/edit/" + epgId;

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

    function onClickTrash(event, that) {
        event.stopPropagation();

        const data = dataTable.row( $(that).parents('tr') ).data();
            const epgId = data[0];


        //please correct the index for name variable, sehingga message delete akan terlihat benar
        const name = data[0];

        showDialogDelete('formDelete', 'Are you sure to delete ' + name, function () {
            window.location.href = "<?=$baseUrl?>/delete/" + epgId;
        })
    }
       
</script>