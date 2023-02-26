<?php
use App\Libraries\Dialog;

$htmlEdit = '';//$form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('NEW', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Checkout', 'Checkout');
?>

<div class="block-content block-content-full border-b clearfix" style="padding-top:0px">
    <a class="btn btn-secondary showNewModal" href="javascript:;" role="button" onclick="showDialog('.dialogformNew')">
        <i class="fa fa-plus text-primary mr-5 "></i> Add Guest
    </a>
    <div class="btn-group float-right">
        <a class="btn btn-secondary showOptionsModal" href="javascript:;" role="button" data-target="#modal-checkbox">
            Options <i class="fa fa-th-large text-primary ml-5"></i>
        </a>
    </div>
</div>
<div class="block-content block-content-full table-responsive">
    <table id="datalist" class="table table-bordered table-hover table-striped table-vcenter" style="width: 100%;">
        <thead>
            <tr> 
                <?php foreach ($fieldList as $field): ?>
                    <th><?=$field?></th>
                <?php endforeach;?>
                <th style="width: 5%;">Action</th>
            </tr>
        </thead>
        <tbody>
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

    //exec when ready
    $('document').ready(function () {
        initDataTable();

        //ini di pakai utk multiselect saja
        initSelectMultiple();
    });

    function initDataTable() {
        dataTable = $('#datalist')
            .DataTable(
            {
                ajax: urlSsp,
                responsive: true,
                "scrollX":true,
                pageLength: 100,
                order: [['0','desc']],
                columnDefs: [
                    {
                        //hide theme, package
                        targets: [2,3],visible: false,searchable: false
                    },
                    {
                        //action column
                        orderable: false,
                        targets: lastCol,
                        className: "text-center",
                        defaultContent: '<a onclick="onClickCheckout(event, this);" href="javascript:;"><span class="label label-danger">CHECKOUT</span></a>'
                    },
                    {
                        //rooms
                        targets:[4], render: function(data)
                        {
                            const room = data.split(",");
                            var html = '';
                            room.forEach(function(value){
                                html += "<button class='btn btn-block btn-primary btn-xs'>"+value+"</button>";
                            });
                            return html;
                        }
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

            window.location.href = "<?=$baseUrl?>/detail/" + value;
        });

        initDataTableOptions(dataTable);

    }

    //
    //
    function onClickCheckout(event, that) {
        event.stopPropagation();

        const data = dataTable.row( $(that).parents('tr') ).data();
        const id = data[0];
        const name = data[1];

        showDialogDelete('formDelete', 'Are you sure to checkout ' + name + ' ?', function () {
            window.location.href = "<?=$baseUrl?>/checkout/" + id;
        })
    }

</script>

