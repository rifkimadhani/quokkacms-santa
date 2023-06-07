<?php
/**
 * Created by PageBuilder.
 * Date: 2023-06-07 15:53:32
 */

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New Emergency Category', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete Emergency Category', 'CONFIRM DELETE');
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
<?=view('util/filemanager.php')?>

<? //echo '<script>const baseUrl = "' . base_url() . '";</script>'; ?>


<script>
    var dataTable;
    const dataList = $('#datalist');
    const primaryKey = "<?=$primaryKey?>";
    const urlSsp = "<?= $baseUrl ?>/ssp";
    const lastCol = <?= count($fieldList) ?>;
    
    // Function to handle image load error
    function onImageError(img) {
        img.src = "<?= base_url(); ?>/assets/notfound.png";
    }

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
                        targets: [2],visible: false,searchable: false
                    },
                    {
                        targets:[3],className: "popupimage",
                        className: "text-center",
                        render: function(data) 
                        {        
                            if(data)
                            {
                                data = data.replace('{BASE-HOST}', '<?= base_url(); ?>');
                                return '<img src="'+data+'" width="100%" height="100%;" class="urlimage" onerror="onImageError(this)">';
                            }
                            return '<img src="<?= base_url("assets/notfound.png") ?>" width="100%" height="100%;" class="urlimage">';
                        },
                        width:100
                    },
                    {
                        //last_seen, create_date, update_date
                        targets:[4,5],render: function(data) 
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

        // handle click on row,
        //  1. ambil dialog yg sdh di isikan dgn data dari server
        //  2. kemudian dialog tsb akan di tampilkan
        //
        dataList.find('tbody').on( 'click', 'tr', function (event)
        {
            event.stopPropagation();
            const data = dataTable.row( $(this)).data();

            //get pk from data
            const emergencyCode = data[0];

            const url = "<?=$baseUrl?>/edit/" + emergencyCode;

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
        const emergencyCode = data[0];


        //please correct the index for name variable, sehingga message delete akan terlihat benar
        const name = data[1];

        showDialogDelete('formDelete', 'Are you sure to delete ' + name, function () {
            window.location.href = "<?=$baseUrl?>/delete/" + emergencyCode;
        })
    }
</script>