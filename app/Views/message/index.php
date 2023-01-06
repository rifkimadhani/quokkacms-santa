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

</script>
<?= view('util/filemanager') ?>
<?= view('util/scripts') ?>