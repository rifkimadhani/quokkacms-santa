<?php
/**
 * Created by PageBuilder.
 * Date: 2023-08-03 13:32:56
 */

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New room', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete room', 'CONFIRM DELETE');
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
    const urlSsp = "<?= $baseUrl ?>/ssp";
    const lastCol = <?= count($fieldList) ?>;
    const dataList = $('#datalist');
    const primaryKey = "<?=$primaryKey?>";
    var dataTable;

    //exec when ready
    $('document').ready(function () {
        initDataTable();
        initSelectMultiple();
    });

    function initDataTable() {
        dataTable = dataList.DataTable(
            {
                ajax: urlSsp,
                responsive: true,
                scrollX: true,
                pageLength: 100,
                order: [['3','asc']],
                columnDefs: [
                    {
                        //hide your cols here, enter index of col into targets array
                        targets: [0,10],visible: false,searchable: false
                    },
                    {
                        targets: [1],
                        title: "ROOM"
                    },
                    {
                        //stb
                        targets: [9],
                        className: "text-center",
                        render: function(data) {
                            if (data === null) {
                                return '';
                            }

                            const STBs = data.split(",");
                            var html = '';

                            STBs.forEach(function(value) {
                                const [name, state] = value.split('|');
                                const badgeClass = state === '1' ? 'badge-success' : 'badge-danger';

                                html += `<span class='badge badge-pill ${badgeClass}'>${name}</span>&nbsp;`;
                            });

                            return html;
                        }
                    },
                    {
                        targets:[10,11],render: function(data) 
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
        
        var stboptions = {placeholder: "Choose STB here",allowClear: true,tags:true,"language": {"noResults": function(){return "No STB Found. Please Type STB Name To Add New STB.";}},escapeMarkup: function (markup) {return markup;}}
        
        // handle click on row,
        //  1. ambil dialog yg sdh di isikan dgn data dari server
        //  2. kemudian dialog tsb akan di tampilkan
        //
        dataList.find('tbody').on( 'click', 'tr', function (event)
        {
            initSelectMultiple();
            event.stopPropagation();
            const data = dataTable.row( $(this)).data();

            //get pk from data
            const roomId = data[0];

            const url = "<?=$baseUrl?>/edit/" + roomId;

            // show hourglass
            jQuery('#overlay-loader-indicator').show();

            $.ajax({url: url}).done(function(result)
            {
                $('.dialogformEdit').html(result);
                $('.dialogformEdit').modal();

                // for multiple select STB
                var selectElement = $('.dialogformEdit').find('.js-example-basic-multiple');
                if (selectElement.length) {
                    selectElement.select2(stboptions)
                        .on('select2:unselecting', function() 
                    {
                        $(this).data('unselecting', true);
                    })
                    .on('select2:opening', function(e) 
                    {
                        if ($(this).data('unselecting')) 
                        {
                        $(this).removeData('unselecting');
                        e.preventDefault();
                        }
                    });
                }
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
            const roomId = data[0];


        //please correct the index for name variable, sehingga message delete akan terlihat benar
        const name = data[1];

        showDialogDelete('formDelete', 'Are you sure to delete Room ' + name +'?', function () {
            window.location.href = "<?=$baseUrl?>/delete/" + roomId;
        })
    }
</script>