<?php
use App\Libraries\Dialog;

$htmlEdit = $form->renderBody('Edit', 'formEdit', "{$baseUrl}/update", $subscriberData);
//$htmlNew = $form->renderDialog('NEW', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Checkout', 'Checkout');

function renderTable($subscriberId, $fields){

    //build head
    $htmlHead = '';
    foreach ($fields as $field){
        $htmlHead = "<th>{$field}</th>";
    }

    return <<< HTML
            <table id="datalist" class="table table-bordered table-hover table-striped" style="width:100%">
                <thead>
                <tr>
                    <?=$htmlHead?>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
HTML;
}
?>
<!-- <div class="block-content"> -->
    <!-- <div class="box-header"> -->
        <!-- <div class="row"> -->
            <!-- <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 col-4"> -->
                    <!-- <a href="javascript:;" role="button" class="btn btn-danger showNewModal" onclick="onCheckoutAll()"> -->
                        <!-- CHECKOUT -->
                    <!-- </a> -->
            <!-- </div> -->
            <!-- <div class="col-xl-8 col-lg-6 col-md-5 col-sm-4 col-8"> -->
            <!-- </div> -->
            <!-- <div class="col-xl-2 col-lg-3 col-md-4 col-sm-5 col-12 text-right"> -->
                <!-- <a href="javascript:;" role="button" class="btn btn-success showOptionsModal"> -->
                   <!-- OPTIONS -->
                <!-- </a> -->
            <!-- </div> -->
        <!-- </div> -->
    <!-- </div> -->
    <div class="block-content block-content-full border-b clearfix" style="padding-top:0px">
        <a class="btn btn-danger showNewModal" href="javascript:;" role="button" onclick="onCheckoutAll()">
            CHECKOUT
        </a>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <?=$htmlEdit?>
        </div>
        <div class="col-lg-6">
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
        </div>
    </div>
<!-- </div> -->

<?//=$htmlNew?>
<?=$htmlDelete?>
<?= view('util/scripts') ?>

<script>
    var dataTable;
    const primaryKey = "<?=$primaryKey?>";
    const urlSsp = "<?= $baseUrl ?>/sspRoom/<?=$subscriberId?>";
    const lastCol = <?= count($fieldList) ?>;

    //exec when ready
    $('document').ready(function () {
        initDataTable();

        //detail subscriber tdk memakai multi select
//        initSelectMultiple();
    });

    function initDataTable() {
        dataTable = $('#datalist')
            .DataTable(
            {
                ajax: urlSsp,
                pageLength: 100,
                oLanguage: {
                    sLengthMenu: ""
                },
                searching: false,
                order: [['0','desc']],
                columnDefs: [
                    {
                        targets: [0],visible: false,searchable: false
                    },
                    {
                        targets:[lastCol], render: function(data, type, row, meta)
                        {
                            data = row[3];

                            //apabila data null, maka room sdh checkout
                            if (data==null) return '';

                            return '<a onclick="onCheckoutRoom(event, this);" href="javascript:;"><span class="label label-danger">CHECKOUT</span></a>'
                        }
                    }
                ]
            });
    }

    //hanya checkout room saja,
    // room yg terakhir di checkout akan otomatis mencheckout subscriber juga
    //
    function onCheckoutRoom(event, that) {
        event.stopPropagation();

        const data = dataTable.row( $(that).parents('tr') ).data();
        const id = data[1];
        const name = data[2];

        showDialogDelete('formDelete', 'Are you sure checkout room #' + name, function () {
            window.location.href = "<?=$baseUrl?>/checkout_room/<?=$subscriberId?>/" + id;
        })
    }

    //checkout semua room
    //
    function onCheckoutAll() {
        showDialogDelete('formDelete', 'Are you sure checkout', function () {
            window.location.href = "<?=$baseUrl?>/checkout/<?=$subscriberId?>";
        })
    }

</script>