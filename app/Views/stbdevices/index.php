<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-06 09:43:25
 */

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
                                    $htmlNew = $form->renderDialog('New Device', 'formNew', "{$baseUrl}/insert");
                                    $htmlDelete = Dialog::renderDelete('Delete Device', 'CONFIRM DELETE');
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
        <tbody>
            <?php foreach ($deviceList as $device): ?>
                <tr>
                    <?php foreach ($fieldList as $field): ?>
                        <td><?=$device[$field]?></td>
                    <?php endforeach;?>
                    <td style="width: 5%;">Action</td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>

<?=$htmlEdit?>
<?=$htmlNew?>
<?=$htmlDelete?>

<?=view('util/scripts.php')?>


<script>
    var dataTable;
    const primaryKey = "<?=$primaryKey?>";
    const urlSsp = "<?= $baseUrl ?>/ssp";
    const lastCol = <?= count($fieldList) ?>;
    const dataList = $('#datalist');
    
    //exec when ready
    $('document').ready(function () {
        initDataTable();
    });

    function initDataTable() {
        dataTable = dataList.DataTable(
            {
                ajax: urlSsp,
                serverSide: true,
                responsive: true,
                scrollX: true,
                pageLength: 50,
                order: [['0','desc']],
                columnDefs: [
                    {
                        //hide your cols here, enter index of col into targets array
                        targets: [3,6,11,12,13],visible: false,searchable: false
                    },
                    {
                        // status
                        targets: [5],
                        render: function (value, type, row) {
                            if (value===1){
                                return '<span class="badge badge-pill badge-success">UP</span>';
                            }else {
                                return '<span class="badge badge-pill badge-danger">DOWN</span>';
                            }
                        }
                    },
                    {
                        //last_seen, create_date, update_date
                        targets:[11,12,13],render: function(data) 
                        {
                            if(data)
                            {
                                return datetostring.datetimetoindonesia(data)
                            }
                            return '';
                        }
                    },
                    {
                        //text-center
                        targets: [7,8,9,10],
                        className: "text-center"
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
            const stbId = data[0];

            const url = "<?=$baseUrl?>/edit/" + stbId;

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
        const stbId = data[0];


        //please correct the index for name variable, sehingga message delete akan terlihat benar
        const name = data[2];

        showDialogDelete('formDelete', 'Are you sure to delete ' + name, function () {
            window.location.href = "<?=$baseUrl?>/delete/" + stbId;
        })
    }

    function calculateTimeDifferenceInSeconds(timestamp, currentTimestamp) {
        if (!timestamp) {
            return Number.MAX_SAFE_INTEGER; // Return a very large value to indicate "null"
        }

        const now = currentTimestamp || new Date().getTime(); // Current timestamp
        const lastSeenTimestamp = new Date(timestamp).getTime(); // Convert input timestamp to a UNIX timestamp
        const timeDifference = Math.floor((now - lastSeenTimestamp) / 1000); // Calculate time difference in seconds

        return timeDifference;
    }
</script>