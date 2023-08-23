<?php
use App\Models\SubscriberGroupForm;
use App\Libraries\Dialog;

$form = new SubscriberGroupForm();

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New Group', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete Group', 'CONFIRM DELETE');
?>

<div class="block-content block-content-full border-b clearfix" style="padding-top:0px">
    <a class="btn btn-secondary showNewModal" href="javascript:;" role="button" onclick="showActive()">
        <i class="fa fa-backward text-primary mr-5 "></i> Back
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

<script>
    var dataTable;
    const urlSsp = "<?= $baseUrl ?>/ssp_history";
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
                serverSide: true,
                pageLength: 50,
                order: [['1','asc']],
                columnDefs: [
                    {
                        targets: [],visible: false,searchable: false
                    },
                    {
                        targets:[3,4],render: function(data) 
                        {
                        if(data)
                        {
                            return datetostring.datetimetoindonesia(data)
                        }
                        return '';
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

        initDataTableOptions(dataTable);
    }

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
    function showActive() {
        window.location.href = "<?=$baseUrl?>";
    }
</script>