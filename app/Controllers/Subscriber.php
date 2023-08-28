<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 8:42 AM
 */

namespace App\Controllers;

use App\Models\BillingModel;
use App\Models\NotificationModel;
use App\Models\RoomModel;
use App\Models\SettingModel;
use App\Models\SubscriberModel;
use App\Models\SubscriberRoomModel;
use App\Models\SubscriberGroupModel;
use App\Models\SubscriberForm;

class Subscriber extends BaseController
{
    public function index()
    {
        $pageTitle = 'Guest';

        $baseUrl = $this->baseUrl;
        $mainview = "subscriber/index";
        $primaryKey = 'subscriber_id';

        //ambil record room2 yg vacant
        $room = new RoomModel();
        $roomData = $room->getVacantForSelect();

        //ambil semua liat group yg active
        $group = new SubscriberGroupModel();
        $groupData = $group->getAllActiveForSelect();

        //ambil field list
        $subscriber = new SubscriberModel();
        $fieldList = $subscriber->getFieldList();

        //form ini akan di render saat di view
        $form = new SubscriberForm($roomData, $groupData);

        return view('layout/template', compact('mainview','primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function history()
    {
        $pageTitle = 'Guest (history)';

        $baseUrl = $this->baseUrl;
        $mainview = "subscriber/history";
        $primaryKey = 'subscriber_id';

        //ambil record room2 yg vacant
        $room = new RoomModel();
        $roomData = $room->getVacantForSelect();

        //ambil semua liat group yg active
        $group = new SubscriberGroupModel();
        $groupData = $group->getAllActiveForSelect();

        //ambil field list
        $subscriber = new SubscriberModel();
        $fieldList = $subscriber->getFieldList();

        //form ini akan di render saat di view
        $form = new SubscriberForm($roomData, $groupData);

        return view('layout/template', compact('mainview','primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new SubscriberModel();

        $this->response->setContentType("application/json");
        echo json_encode($model->getSsp(true));
    }

    public function sspHistory()
    {
        $model = new SubscriberModel();

        $this->response->setContentType("application/json");
        echo json_encode($model->getSsp(false));
    }

    public function sspRoom($subscriberId)
    {
        $model = new SubscriberRoomModel();
        $billing = new BillingModel();

        $this->response->setContentType("application/json");

        $ssp = $model->getssp($subscriberId);
        $data = &$ssp['data'];

        //inject billing
        foreach ($data as &$row){
            $roomId = $row[1]; //index 1 = roomId
            $roomService = $billing->getSummaryRoomService($subscriberId, $roomId);
            $vod = $billing->getSummaryVod($subscriberId, $roomId);

            $row[] = number_format($roomService + $vod);
        }

        echo json_encode($ssp);
    }

    public function insert(){

//        log_message('error', json_encode($_POST));

//        $rooms = $_POST['room_id'];

        $model = new SubscriberModel();
        $count = $model->checkin($_POST);

        if ($count==0){
            $this->setErrorMessage('Add guest fail, room already occupied');
        } else {
            $this->setSuccessMessage('Success');
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * utk edit detail subscriber, tapi tdk bisa add/remove room yg sdh di pilih saat create
     * utk checkout bisa
     *
     * @param $subscriberId
     * @return string
     */
    public function detail($subscriberId){
        $baseUrl = $this->baseUrl;
        $mainview = "subscriber/detail";
        $primaryKey = 'subscriber_id';

        $model = new SubscriberModel();
        $subscriberData= $model->get($subscriberId);

        $pageTitle = $subscriberData['salutation'] . ' ' . $subscriberData['name'] . ' ' . $subscriberData['last_name'];

        $room = new SubscriberRoomModel();
        $fieldList = $room->getFieldList();
        $fieldList[] = 'total'; //tambahkan 1 col, total utk billing

        $group = new SubscriberGroupModel();
        $groupData = $group->getAllActiveForSelect();

        //utk form edit, room tdk bisa di rubah2 lagi, shg tdk di perlukan
        $form = new SubscriberForm([], $groupData);
        unset($form->room_id); //di remove krm room tdk bisa di rubah2 setelah di create

        //room yg di pakai oleh subscriber
        $rooms = $room->getAllBySubscriber($subscriberId);
        $billing = new BillingModel();

        $grandTotal = 0;
        foreach ($rooms as $item){
            $roomId = $item['room_id'];
            $roomService = $billing->getSummaryRoomService($subscriberId, $roomId);
            $vod = $billing->getSummaryVod($subscriberId, $roomId);
            $grandTotal += $roomService + $vod;
        }

        $setting = new SettingModel();

        $currency = $setting->getCurrency();

        return view('layout/template', compact('mainview','primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'subscriberId', 'subscriberData', 'form', 'grandTotal', 'currency'));
    }

    /**
     * return dialog detail billing
     * @param $roomId
     * @return string
     */
    public function ajaxBilling($subscriberId, $roomId){

        $billing = new BillingModel();

        $htmlRoomService = $this->genBillingRoomService($billing, $subscriberId, $roomId);
        $htmlVod = $this->genBillingVod($billing, $subscriberId, $roomId);

        //dialog template
        return <<< HTML
        <div class="modal-dialog modal-lg modal-dialog-popout" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Billing</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
                        <form id="formEdit" action="" method="post" enctype="multipart/form-data">
            <div class="block-content">
                {$htmlRoomService}
                <br/>
                {$htmlVod}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                <!--<button type="submit" class="btn btn-alt-primary">-->
                    <!--<i class="fa fa-check"></i> Submit-->
                <!--</button>-->
            </div>
        </form>
            </div>
        </div>
HTML;
    }

    /**
     * Generate html billing room service utk dialog billing
     *
     * @param BillingModel $billing
     * @param $subscriberId
     * @param $roomId
     * @return string
     */
    protected function genBillingRoomService(BillingModel $billing, $subscriberId, $roomId){
        $orders = $billing->getRoomService($subscriberId, $roomId);

        $htmlTable = '';


        $this->varDump($orders);



        $lastOrderCode = '';
        $orderLen = sizeof($orders);
        if ($orderLen>0){
            //get code of the last order
            $lastOrderCode  = $orders[$orderLen-1]['order_code'];
        }

        foreach ($orders as $order){

            $orderCode = $order['order_code'];

            $htmlTable .= <<< HTML
                        <tr><th colspan="3" style="border-top-width: 2px;">Order #{$orderCode}</th></tr>
HTML;

            $items = $billing->getRoomServiceItem($orderCode);

            $htmlRow = '';
            foreach ($items as $item){
                $menu = $item['menu_name'];
                $qty = $item['qty'];
                $price = $item['price'];
                $subtotal = $qty * $price;

                $format = number_format($subtotal);

                $htmlRow .= <<< HTML
                        <tr>
                            <td width="10px" style="border-right: hidden;"></td>
                            <td>{$menu}<br/>{$qty} x {$price}</td>
                            <td valign="top" style="text-align: right; width: 25%;">{$format}</td>
                        </tr>
HTML;
            }

            $purchaseAmount = $order['purchase_amount'];
            $serviceCharge = $order['service_charge'];
            $tax = $order['tax'];
            $total = $purchaseAmount + $serviceCharge + $tax;

            $serviceCharge = number_format($serviceCharge);
            $tax = number_format($tax);
            $format = number_format($total);

            //bagian ini khusus ukk handle border paling bawah
            //apabila order terakhir, maka bottom = off
            if ($orderCode==$lastOrderCode){
                $borderBottom = 'border-bottom: hidden;';
            } else {
                $borderBottom = '';
            }

            $htmlTable .= <<< HTML
                        {$htmlRow}
                        <tr>
                            <td width="10px" style="border-right: hidden;"></td>
                            <td>Service charge</td>
                            <td valign="top" style="text-align: right;">{$serviceCharge}</td>
                        </tr>
                        <tr>
                            <td width="10px" style="border-right: hidden;"></td>
                            <td>Tax</td>
                            <td valign="top" style="text-align: right;">{$tax}</td>
                        </tr>
                        <tr><th colspan="3" style="text-align: right; border-bottom-width: 2px;">Total {$format}</th></tr>
                        <tr style="border-left: hidden; border-right:hidden; {$borderBottom}"><th colspan="3" style="text-align: right;">&nbsp;</th></tr>
HTML;
        }

        if (strlen($htmlTable)==0) return '';

        return <<< HTML
            <div>
                <table border="1" width="100%" style="border-width: 2px 2px 0px 2px;">
                    <tbody>
                        {$htmlTable}
                    </tbody>
                </table>
            </div>
HTML;
    }

    protected function genBillingVod(BillingModel $billing, $subscriberId, $roomId){

        $grandTotal = 0;
        $htmlRow = '';
        $vods= $billing->getVod($subscriberId, $roomId);
        foreach ($vods as $item){
            $title = $item['title'];
            $purchaseAmount = $item['purchase_amount'];
            $tax = $item['tax'];
            $total = $purchaseAmount + $tax;
            $grandTotal += $total;

            $format = number_format($total);

            $htmlRow .= <<< HTML
                        <tr>
                            <td width="10px" style="border-right: hidden;"></td>
                            <td >{$title}</td>
                            <td valign="top" style="text-align: right;">{$format}</td>
                        </tr>
HTML;
        }

        $grandTotal = number_format($grandTotal);

        //add grand total
        $htmlRow .= <<< HTML
                        <tr>
                            <th colspan="3" style="text-align: right;">Total {$grandTotal}</th>
                        </th>
HTML;

        return <<< HTML
            <div>
                <table border="1" width="100%" style="border-width: 2px;">
                    <tbody>
                        <tr><th colspan="2">VOD Title</th><th style=" width: 25%;">Price</th></tr>
                        {$htmlRow}
                    </tbody>
                </table>
            </div>
HTML;
    }

    public function update(){
        $id = $_POST['subscriber_id'];

        $model = new SubscriberModel();
        $r = $model->modify($id, $_POST);

        if ($r>0){
            $this->setSuccessMessage('UPDATE success');
            NotificationModel::sendStateToSubscriber($id);
        } else {
            $this->setErrorMessage('UPDATE fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * checkout all room
     *
     * @param $subscriberId
     * @return $this
     */
    public function checkout($subscriberId){
        $room = new RoomModel();
        $rooms = $room->getBySubscriber($subscriberId);

        $model = new SubscriberModel();
        $r = $model->checkout($subscriberId, $rooms);

        if ($r>0){
            $this->setSuccessMessage('DELETE success');
        } else {
            $this->setErrorMessage('DELETE fail');
        }

        return redirect()->to($this->baseUrl);
    }

    public function checkoutRoom($subscriberId, $roomId){

        $model = new SubscriberModel();
        $r = $model->checkout($subscriberId, [ ['room_id'=>$roomId] ]);

        if ($r>0){
            $this->setSuccessMessage('DELETE success');
        } else {
            $this->setErrorMessage('DELETE fail');
        }

        return redirect()->to($this->baseUrl);
    }

}
