<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 14/01/2022
 * Time: 13:59
 */
//defined('BASEPATH') OR exit('No direct script access allowed');

define('STATUS_NEW', 'NEW');
define('STATUS_ACK', 'ACK');
define('STATUS_CANCEL', 'CANCEL');
define('STATUS_FINISH', 'FINISH');

$order = json_decode($data['data']);

$type = $data['type'];
$status = $data['status'];
$object = json_decode($data['data']);
$buttonStyle = ''; //di pakai utk hide tombol submit

switch (strtoupper($type)){
    case 'REQUEST-ITEM':
        $output = genRequestItem($object);
        break;
    case 'CALL-TAXI':
        $output = genCallTaxi($object);
        break;
    default:
        $output = '';
        break;
}

switch (strtoupper($status)){
    case STATUS_NEW:
        $buttonSubmit = 'Acknowledge';
        break;

    case STATUS_ACK:
        $buttonSubmit = 'Finish';
        break;

    case STATUS_FINISH:
    case STATUS_CANCEL:
        $buttonStyle = 'display: none;';
        $buttonSubmit = '';
        break;
}


/** buat html utk request item
{
    "note": "fix ac",
    "list": [
        {
            "name": "Pillow",
            "qty": "1"
        },
        {
            "name": "Dental kit",
            "qty": "1"
        }
    ]
}
**/
function genRequestItem($data){

    $note = $data->note;
    $list = $data->list;
    $order = '';

    foreach ($list as $item) {

        $name = $item->name;
        $qty = $item->qty;

        $html = <<< HTML

    <tr>
        <td>{$name}</td>
        <td align="center">{$qty}</td>
    </tr>
HTML;
        $order .= $html;
    }

    return <<< HTML
                <div class="form-group">
                    <label class='col-form-label'><b>Note</b></label>
                    <input type=text value='{$note}' style='' class='form-control' name='note' readonly>
                </div>

                <div class="form-group">
                    <label class='col-form-label'><b>Request</b></label>
                    <table border="1" width="50%">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                        </tr>
                        </thead>
                        <tbody>
                        {$order}
                        </tbody>
                    </table>
                </div>
HTML;

}

//buat html utk call taxi
function genCallTaxi($data){

    $date = $data->date;
    $dest = $data->destination;

    return <<< HTML
                <div class="row">

                    <div class="col-md-3">
                        <label class='col-form-label'><b>Date</b></label>
                        <input type=text value='{$date}' style='' class='form-control'>
                    </div>

                    <!-- destionation -->
                    <div class="col-md-9">
                        <label class='col-form-label'><b>Destination </b></label>
                        <input type=text value='{$dest}' style='' class='form-control'>
                    </div>
                </div>
HTML;
}
?>


<div class="modal-dialog modal-lg modal-dialog-popout" role="document">
    <div class="modal-content">
        <div class="block block-themed block-transparent mb-0">
            <div class="block-header bg-primary-dark">
                <h3 class="block-title"id="exampleModalLabel">Process Service</h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                        <i class="si si-close"></i>
                    </button>
                </div>
            </div>

            <form id="editForm" action="<?= $urlPost ?>" method="post" enctype="multipart/form-data">

                <div class="block-content mb-4">
                    
                        <input type="hidden" name="task_id" value="<?=$data['task_id']?>">

                        <div class="row ">

                            <!-- status-->
                            <div class="col-md-3">
                                <label class='col-form-label'><b>Status</b></label>
                                <input type=text value='<?=$data['status']?>' style='' class='form-control' readonly>
                            </div>

                            <!-- task id-->
                            <div class="col-md-3">
                                <label class='col-form-label'><b>Task Id</b></label>
                                <input type=text value='<?=$data['task_id']?>' style='' class='form-control' readonly>
                            </div>

                            <div class="col-md-3">
                                <label class='col-form-label'><b>Type</b></label>
                                <input type=text value='<?=$type?>' style='' class='form-control' readonly>
                            </div>

                            <!-- date-->
                            <div class="col-md-3">
                                <label class='col-form-label'><b>Create</b></label>
                                <input type=text value='<?=$data['update_date']?>' style='' class='form-control' readonly>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class='col-form-label'><b>Guest name</b></label>
                                <input type=text value='<?=$data['salutation']?> <?=$data['name']?> <?=$data['last_name']?> [<?=$data['status_checkin']?>]' class='form-control' name='subscriber_name' readonly>
                            </div>

                            <div class="col-md-6">
                                <label class='col-form-label'><b>Room</b></label>
                                <input type=text value='<?=$data['room_name']?>' class='form-control' name='subscriber_name' readonly>
                            </div>
                        </div>

                        <?=$output?>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-alt-secondary pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-alt-primary pull-right" style="<?=$buttonStyle?>"><i class="fa fa-check"></i> <?=$buttonSubmit?></button>
                </div>
            </form>
        </div>
    </div>
</div>





