<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 14/01/2022
 * Time: 13:59
 */
//defined('BASEPATH') OR exit('No direct script access allowed');

$orderCode = $data['order_code'];
$status = $data['status'];
$orderDate = $data['order_date'];
$statusCheckin = $data['status_checkin'];
$kitchen = $data['kitchen_name'];
$note = $data['notes'];

$guestName = "{$data['salutation']} {$data['first_name']} {$data['last_name']} [{$data['status_checkin']}]";
$location = "{$data['name']} -- {$data['location']}";

$btnName = '';
$buttonStyle = '';

switch ($status){
    case 'NEW':
        $btnName = 'PROCESS';
        break;
    case 'PROCESS':
        $btnName = 'DELIVER TO GUEST';
        break;
    case 'ENROUTE':
        $btnName = 'FINISH';
        break;

    case 'DELIVERED':
    case 'CANCEL':
        $buttonStyle = 'display: none;'; //hide button
        break;
}

$order = '';

foreach ($detail as $item){

    $name = $item['menu_name'];
    $qty = $item['qty'];

    $html = <<< HTML

    <tr>
        <td>{$name}</td>
        <td align="center">{$qty}</td>
    </tr>
HTML;

    $order .= $html;
}
?>

<div class="modal-dialog modal-lg flipInX animated" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">ORDER <?=$orderCode?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="editForm" class="p-3" action="<?= $urlPost ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="order_code" value="<?=$orderCode?>" readonly>
                <div class="row">
<!--                                        status-->
                    <div class="col-md-3">
                        <label class='col-form-label'><b>Status</b></label>
                        <input type=text value='<?=$status?>' style='' class=form-control readonly>
                    </div>

<!--                                        order code-->
                    <div class="col-md-3">
                        <label class='col-form-label'><b>Order code</b></label>
                        <input type=text value='<?=$orderCode?>' style='' class=form-control readonly>
                    </div>

<!--                                        payment-->
                    <div class="col-md-3">
                        <label class='col-form-label'><b>Kitchen</b></label>
                        <input type=text value='<?=$kitchen?>' style='' class=form-control readonly>
                    </div>

<!--                                        date-->
                    <div class="col-md-3">
                        <label class='col-form-label'><b>Create</b></label><input type=text value='<?=$orderDate?>' style='' class=form-control readonly>
                    </div>

                </div>

<!--                penerima-->
                <div class="row">
                    <div class="col-md-6">
                        <label class='col-form-label'><b>Guest</b></label>
                        <input type=text value='<?=$guestName?>' class=form-control name='subscriber_name' readonly>
                    </div>

<!--                    room-->
                    <div class="col-md-6">
                        <label class='col-form-label'><b>Room</b></label>
                        <input type=text value='<?=$location?>' class=form-control name='subscriber_name' readonly>
                    </div>
                </div>

<!--                daftar order-->
                <div class="box-body table-responsive padding">
                    <table id="datalist" class="table table-bordered table-hover" >
                        <thead>
                        <tr>
                            <th><b>Name</b></th>
                            <th><b>Qty</b></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?=$order?>
                        </tbody>
                    </table>
                </div>

<!--                note-->
                <div class="form-group">
                    <label class='col-form-label'><b>Note</b></label>
                    <input type=text value='<?=$note?>' style='' class=form-control name='note' readonly>
                </div>

<!--                tombol-->
                <div class="form-group">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary pull-right" style="<?=$buttonStyle?>"><?=$btnName?></button>
                    </div>
            </form>
        </div>

    </div>
</div>





