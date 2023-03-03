<?php
/**
 * Created by PageBuilder.
 * Date: 2023-03-01 10:20:34
 */

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New Application', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete Application', 'CONFIRM DELETE');
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
    var dataTable;
    const urlSsp = "<?= $baseUrl ?>/ssp";
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
                pageLength: 100,
                order: [['0','desc']],
                columnDefs: [
                    {
                        targets: [0],visible: false,searchable: false
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
        // $('#datalist tbody').on( 'click', 'tr', function (event)
        // {
        //     event.stopPropagation();
        //     const data = dataTable.row( $(this)).data();
        //     const value = data[0];
        //     const url = "<?//=$baseUrl?>/edit/" + value;

        //     // show hourglass
        //     jQuery('#overlay-loader-indicator').show();

        //     $.ajax({url: url}).done(function(result)
        //     {
        //         $('.dialogformEdit').html(result);
        //         $('.dialogformEdit').modal();
        //     })
        //     .always(function()
        //     {
        //         jQuery('#overlay-loader-indicator').hide();
        //     });
        // });

        initDataTableOptions(dataTable);
    }

    //
    //
    function onClickTrash(event, that) {
        event.stopPropagation();

        const data = dataTable.row( $(that).parents('tr') ).data();
        const id = data[0];
        const name = data[1];

        showDialogDelete('formDelete', 'Are you sure to delete ' + name, function () {
            window.location.href = "<?=$baseUrl?>/delete/" + id;
        })
    }

    $('.dialogformNew input[type=file]').change(function ()
    {
      var x = $('.dialogformNew  input[name=apkfile]')[0];
      if ('files' in x)
      {
        if (x.files.length > 0)
        {
          var filename    = x.files[0].name;
          var filesize    = Math.floor( x.files[0].size/(1024*1000));
          var extension   = filename.substr( (filename.lastIndexOf('.') +1) );
          if(extension == 'apk')
          {
            $('.dialogformNew  input[name="filename"]').val(filename);
            $('.dialogformNew  input[name="filesize"]').val(filesize + " MB");
          }
          else
          {
            alert("File Not Support.Please Choose APK File");
            $('.dialogformNew  input[name="apkfile"]').val('');
          }
        }
      }
    });
</script>