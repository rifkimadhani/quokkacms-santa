<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-08 12:15:46
 */

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New kitchenmenugroup', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete kitchenmenugroup', 'CONFIRM DELETE');

function convertToJavaScriptArray($phpArray) {
    $jsArray = '[';
    $elements = [];

    foreach ($phpArray as $element) {
        $properties = [];

        foreach ($element as $key => $value) {
            $properties[] = '' . $key . ': "' . $value . '"';
        }

        $elements[] = '{' . implode(', ', $properties) . '}';
    }

    $jsArray .= implode(', ', $elements);
    $jsArray .= ']';

    return $jsArray;
}
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

<?=$htmlEdit?>
<?=$htmlNew?>
<?=$htmlDelete?>

<?=view('util/scripts.php')?>


<script>
    const arKitchen = <?= convertToJavaScriptArray($data) ?>;
    const urlSsp = "<?= $baseUrl ?>/ssp";
    const lastCol = <?= count($fieldList) ?>;
    const dataList = $('#datalist');
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
                        targets: [],visible: false,searchable: false
                    },
                    {
                        //kitchen_id ==> kitchen name
                        targets: 1, render: function ( data, type, row, meta ) {
//                            console.log(data);
                            return findValueById(arKitchen, data.toString());
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
        dataList.find('tbody').on( 'click', 'tr', function (event)
        {
            event.stopPropagation();
            const data = dataTable.row( $(this)).data();

            //get pk from data
            const menuGroupId = data[0];

            const url = "<?=$baseUrl?>/edit/" + menuGroupId;

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
            const menuGroupId = data[0];


        //please correct the index for name variable, sehingga message delete akan terlihat benar
        const name = data[0];

        showDialogDelete('formDelete', 'Are you sure to delete ' + name, function () {
            window.location.href = "<?=$baseUrl?>/delete/" + menuGroupId;
        })
    }

    /**
     *
     * @param array
     * @param id harus dalam bentuk string
     * @returns {null}
     */
    function findValueById(array, id) {
        const foundItem = array.find(item => item.id === id);
        return foundItem ? foundItem.value : null;
    }

</script>