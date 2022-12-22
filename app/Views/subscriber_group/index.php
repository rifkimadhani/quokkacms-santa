<?php
use App\Models\SubscriberGroupForm;
use App\Models\SubscriberGroupModel;
use App\Libraries\Dialog;

//$router = service('router');
//$controllerName = $router->controllerName();

$group = new SubscriberGroupModel();
$data = $group->get(1);

$metadata = new SubscriberGroupForm();

$urlAction = base_url('/subscribergroup/insert');

$htmlEdit = $metadata->renderPlainDialog('formEdit');
$htmlNew = $metadata->renderDialog('New group', 'formNew', $urlAction);

$actionDelete = base_url('/subscribergroup/delete');
$htmlDelete = Dialog::renderDelete('DELETE group', 'formDelete', $actionDelete);
?>

<?=$htmlEdit?>
<?=$htmlNew?>
<?=$htmlDelete?>

<div class="box">
    <div class="box-header">
        <div class="row">
            <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 col-4">
                    <a href="javascript:;" role="button" class="btn btn-primary showNewModal" onclick="showDialog('.dialogformNew')">
                        CREATE Group
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


<script>
    var dataTable;
    const urlSsp = "<?= base_url('subscribergroup/ssp') ?>";
    const lastCol = <?= count($fieldList) ?>;

    //exec when ready
    $('document').ready(function () {
        initDataTable();
        initDialog();
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
                        //action column
                        targets: lastCol,
                        className: "center",
                        defaultContent: '<a class="showDeleteModal" onclick="onTrashClick(event, this);" href="javascript:;"> <i class="fa fa-trash fa-2x"></i></a>'
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
            const url = "<?=base_url('/subscribergroup/edit')?>/" + value;

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

    //attach submit pada form
    function initDialog() {
        $('#formEdit').on('submit', function() {

            //ambila value dari child of form
            const v = $('#password').val();
            console.log(v);

            return true;
        });

//        $('#formNew').on('submit', function() {
//
//            const v = $('#formNew #password').val();
//            console.log(v);
//
//            return false;
//        });
    }

    //
    //
    function onTrashClick(event, that) {
        event.stopPropagation();

        const data = dataTable.row( $(that).parents('tr') ).data();
        const groupId = data[0];
        const name = data[1];

        showDialogDelete('formDelete', 'Are you sure to delete group ' + name, function () {
            const url = "<?=base_url('/subscribergroup/delete') ?>/" + groupId;
//            console.log(url);
            window.location.href = url;
        })
    }

    //
    //
    function showDialog(dialogName) {
        $(dialogName).modal();
    }
    function hideDialog(dialogName) {
        $(dialogName).modal('hide');
    }

    function showDialogDelete(formId, message, callback) {
        $('.dialog'+formId).modal();
        $('.dialog'+formId + ' #message'+formId).text(message);
        $('.dialog'+formId + ' #btnDelete'+formId).on('click', function() {
            callback();
        });
    }


</script>
