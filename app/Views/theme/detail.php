<?php

/**
 * Created by PageBuilder.
 * Date: 2023-04-04 14:18:55
 */

use App\Libraries\Dialog;

$htmlEdit = $form->renderPlainDialog('formEdit');
$htmlNew = $form->renderDialog('New theme', 'formNew', "{$baseUrl}/insert");
$htmlDelete = Dialog::renderDelete('Delete theme', 'CONFIRM DELETE');
?>

<div class="block-content block-content-full border-b clearfix" style="padding-top:0px">
    <a class="btn btn-secondary" href="javascript:" role="button" onclick="onClickBack()">
        <i class="fa fa-backward text-primary mr-5 "></i> Back
    </a>
    <!--    <a class="btn btn-secondary" href="javascript:" role="button" onclick="onClickNotify()" title="Notify all stb to update theme">-->
    <!--        <i class=""></i> Notify-->
    <!--    </a>-->
    <div class="btn-group float-right">
        <a class="btn btn-secondary showOptionsModal" href="javascript:;" role="button" data-target="#modal-checkbox">
            Options <i class="fa fa-th-large text-primary ml-5"></i>
        </a>
    </div>
</div>
<div class="block-content block-content-full table-responsive">
    <table id="datalist" class="table table-bordered table-hover table-striped table-vcenter" style="width: max-content;">
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


<!-- <div class="row row-deck">
<? //php foreach ($themeElementData as $items): 
?>
    <? //php
    // replace "{BASE-HOST}" to actual URL
    //$imageUrl = str_replace("{BASE-HOST}", base_url(), $items['url_image']);
    ?>
    <div class="col-sm-4">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title"><//?= $items['element_name'];?></h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option">
                        <i class="si si-wrench"></i>
                    </button>
                </div>
            </div>
            <div class="block-content">
                <img src="<//?= $imageUrl;?>" style="max-width: 300px;" alt="">
            </div>
        </div>
    </div>
<? //php endforeach;
?>
</div> -->

<?= $htmlEdit ?>
<?= $htmlNew ?>
<?= $htmlDelete ?>

<?= view('util/scripts.php') ?>
<?= view('util/filemanager.php') ?>

<script>
    const urlSsp = "<?= $baseUrl ?>/ssp/<?= $themeId ?>";
    const lastCol = <?= count($fieldList) ?>;
    const dataList = $('#datalist');
    const primaryKey = "<?= $primaryKey ?>";
    var dataTable;

    //exec when ready
    $('document').ready(function() {
        initDataTable();
    });

    function initDataTable() {
        dataTable = dataList.DataTable({
            ajax: urlSsp,
            responsive: true,
            scrollX: true,
            autoWidth: true,
            pageLength: 100,
            order: [
                ['0', 'desc']
            ],
            columnDefs: [{
                    //hide your cols here, enter index of col into targets array
                    targets: [0, 2, 5, 9],
                    visible: false,
                    searchable: false
                    // targets: [0,2,5],visible: false,searchable: false
                },
                {
                    targets: [6],
                    className: "text-center",
                    render: function(value, type, row) {
                        // console.log(row);
                        const elementType = row[4];

                        switch (elementType) {
                            case 'IMAGE':
                                return renderImage(value);

                            case 'COLOR':
                                return renderColor(row[9]);

                            case 'VIDEO':
                            case 'STREAM':
                                return renderVideo(value);

                            default:
                                return 'UNDEFINED TYPE'
                        }

                        // if(value)
                        // {
                        //     value = value.replace('{BASE-HOST}', '<//?= base_url(); ?>//');

                        //     return '<img src="'+value+'" width="100%" height="100%;" class="urlimage">';
                        // }
                        // return '<img src="<//?= base_url("assets/notfound.png") ?>//" width="50px" height="50px;" class="urlimage">';
                    },
                    width: '15vw'
                },
                {
                    // action column
                    targets: lastCol,
                    className: "text-center",
                    defaultContent: '---' //cannot delete theme
                }

            ]
        });

        // handle click on row,
        //  1. ambil dialog yg sdh di isikan dgn data dari server
        //  2. kemudian dialog tsb akan di tampilkan
        //
        dataList.find('tbody').on('click', 'tr', function(event) {
            event.stopPropagation();
            const data = dataTable.row($(this)).data();

            //get pk from data
            const themeId = data[0];
            const elementId = data[2];
            const elementType = data[4];

            const url = "<?= $baseUrl ?>/edit/" + themeId + '/' + elementId;

            // show hourglass
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

    function onClickBack() {
        event.stopPropagation();
        window.location.href = "<?= $baseUrl ?>";
    }
    //    function onClickNotify() {
    //        event.stopPropagation();
    //        window.location.href = "<//?=$baseUrl?>/notify/<//?=$themeId?> //";
    //    }

    function renderImage(value) {
        if (value) {
            value = value.replace('{BASE-HOST}', '<?= base_url(); ?>');
            return '<img src="' + value + '" class="urlimage" style="max-width: 100%; height: auto"">';
        }
        return ''; //render blank apabila tdk ada value
    }

    function renderColor(value) {
        if (value) {
            var html;
            html = "<div style='background: #" + argbToRgba(value) + ";'><br/><br/><br/></div>";
            html += "<input type='text' value='" + value +
                "' style='width: 100%; border: hidden; text-align: center; position: relative;'>";
            return html;
        }
        return 'COLOR NOT DEFINED';
    }

    function renderVideo(value) {
        if (value) {
            value = value.replace('{BASE-HOST}', '<?= base_url(); ?>');
            var html;
            html = "<video width='300' controls><source src='" + value + "' type='video/mp4'></video>";
            html += "<input type='text' value='" + value + "' style='width: 100%; border: hidden; text-align: center;'>";
            return html;
        }
        return 'URL NOT DEFINED';
    }

    function argbToRgba(hexColor) {
        const length = hexColor.length;
        if (length !== 8) return hexColor;

        const alpha = hexColor.substring(0, 2);
        const color = hexColor.substring(2, 8);

        return color + alpha;
    }

    function rgbaToArgb(hexColor) {
        const length = hexColor.length;
        if (length !== 8) return hexColor;

        const alpha = hexColor.substring(6, 8);
        const color = hexColor.substring(0, 6);

        return alpha + color;
    }
</script>