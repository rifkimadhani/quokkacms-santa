<?php

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('NEW MESSAGE', 'formNew', "{$baseUrl}/insert");
$htmlGroupNew = $formGroup->renderDialog('NEW MESSAGE TO GROUP', 'formGroupNew', "{$baseUrl}/insertGroup");
$htmlRoomNew = $formRoom->renderDialog('NEW MESSAGE TO ROOM', 'formRoomNew', "{$baseUrl}/insertRoom");
$htmlDelete = Dialog::renderDelete('DELETE', 'formDelete');
?>
<div class="block-content block-content-full border-b clearfix" style="padding-top:0px">
    <a class="btn btn-secondary showNewModal" href="javascript:;" role="button" onclick="showDialog('.dialogformNew')">
        <i class="fa fa-user text-primary mr-5 "></i> Message to Guest
    </a>
    <a class="btn btn-secondary showNewModal" href="javascript:;" role="button" onclick="showDialog('.dialogformGroupNew')">
        <i class="fa fa-users text-primary mr-5 "></i> Message to Group
    </a>
    <a class="btn btn-secondary showNewModal" href="javascript:;" role="button" onclick="showDialog('.dialogformRoomNew')">
        <i class="fa fa-users text-primary mr-5 "></i> Message to Room
    </a>
    <div class="float-right">
        <a class="btn btn-secondary" href="javascript:;" role="button" onclick="showHistory()" data-toggle="tooltip" data-placement="left" title="Show History">
            <i class="fa fa-history text-primary"></i>
        </a>
        <a class="btn btn-secondary showOptionsModal" href="javascript:;" role="button" data-target="#modal-checkbox">
            Options <i class="fa fa-th-large text-primary ml-5"></i>
        </a>
    </div>
</div>
<div class="block-content block-content-full table-responsive">
    <table id="datalist" class="table table-bordered table-hover table-striped table-vcenter">
        <thead>
            <tr>
                <?php foreach ($fieldList as $field) : ?>
                    <th><?= $field ?></th>
                <?php endforeach; ?>
                <th style="width: 5%;">Action</th>
            </tr>
        </thead>
    </table>
</div>

<?= $htmlEdit ?>
<?= $htmlNew ?>
<?= $htmlGroupNew ?>
<?= $htmlRoomNew ?>
<?= $htmlDelete ?>


<script>
    var dataTable;
    const primaryKey = "<?= $primaryKey ?>";
    const urlSsp = "<?= $baseUrl ?>/ssp";
    const lastCol = <?= count($fieldList) ?>;

    //exec when ready
    $('document').ready(function() {
        initDataTable();

        // installClick();
        // initDialog();
    });

    function initDataTable() {
        dataTable = $('#datalist')
            .DataTable({
                ajax: urlSsp,
                serverSide: true,
                responsive: true,
                "scrollX": true,
                pageLength: 100,
                order: [
                    ['0', 'desc']
                ],
                columnDefs: [{
                        targets: [0, 1, 3, 6, 7, 10, 12],
                        visible: false,
                        searchable: false
                    },
                    {
                        targets: [12, 13],
                        render: function(data) {
                            if (data) {
                                return datetostring.datetimetoindonesia(data)
                            }
                            return '';
                        }
                    },
                    {
                        //action column
                        targets: lastCol,
                        className: "text-center",
                        defaultContent: '<a onclick="onClickTrash(event, this);" href="javascript:;"> <i class="fa fa-trash fa-2x"></i></a>'
                    }

                ]
            });

        var roomoptions = {
            placeholder: "Pilih Room Disini",
            allowClear: true,
            tags: true,
            "language": {
                "noResults": function() {
                    return "No Room Found. Please Type Room Name To Add New Room";
                }
            },
            escapeMarkup: function(markup) {
                return markup;
            }
        }
        //handle click on row
        //
        $('#datalist tbody').on('click', 'tr', function(event) {
            event.stopPropagation();
            const data = dataTable.row($(this)).data();
            const value = data[0];
            const url = "<?= $baseUrl ?>/edit/" + value;

            //show hourglass
            jQuery('#overlay-loader-indicator').show();

            $.ajax({
                    url: url
                }).done(function(result) {
                    $('.dialogformEdit').html(result);
                    $('.dialogformEdit').modal();
                })
                .always(function() {
                    jQuery('#overlay-loader-indicator').hide();
                });
        });

        initDataTableOptions(dataTable);

    }

    function onClickTrash(event, that) {
        event.stopPropagation();

        const data = dataTable.row($(that).parents('tr')).data();
        const id = data[0];

        showDialogDelete('formDelete', 'Are you sure to delete message #' + id, function() {
            window.location.href = "<?= $baseUrl ?>/delete/" + id;
        })
    }

    function showHistory(event, that) {
        window.location.href = "<?= $baseUrl ?>/history";
    }
</script>
<?= view('util/filemanager.php') ?>
<?= view('util/scripts.php') ?>