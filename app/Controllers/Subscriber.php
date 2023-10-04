<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 8:42 AM
 */

namespace App\Controllers;

use App\Models\BillingModel;
use App\Models\DipatcherModel;
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
        <div class="modal-dialog modal-dialog-popout" role="document">
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
                        <tr>
                            <th colspan="3" style="border-top: hidden;"><h4 class="font-w300 mb-2">Order #{$orderCode}</h4></th>
                        </tr>
                        <tr>
                            <td width="10px" style="border-right: hidden;"></td>
                            <td><b>Item</b></td>
                            <td style="text-align: right;"><b>Price</b></td>
                        </tr>
HTML;

            $items = $billing->getRoomServiceItem($orderCode);

            //curency sign
            $setting = new SettingModel();
            $currencySign = $setting->getCurrencySign();

            $htmlRow = '';
            foreach ($items as $item){
                $menu = $item['menu_name'];
                $qty = $item['qty'];
                $price = $item['price'];
                $subtotal = $qty * $price;

                $priceFormat = number_format($price);
                $format = number_format($subtotal);

                $htmlRow .= <<< HTML
                        <tr style="border-bottom: hidden;">
                            <td width="10px" style="border-right: hidden;"></td>
                            <td>{$menu}<br/>{$qty} x {$priceFormat}</td>
                            <td valign="top" style="text-align: right;">{$currencySign} {$format}</td>
                        </tr>
HTML;
            }

            $purchaseAmount = $order['purchase_amount'];
            $serviceCharge = $order['service_charge'];
            $tax = $order['tax'];
            $taxPercent = $order['percent_tax'];
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
                        <tr></tr>
                        <tr style="padding-top: 10px; border-bottom: hidden;">
                            <td colspan="2" style="text-align: right;">Service charge</td>
                            <td valign="top" style="text-align: right;">{$currencySign} {$serviceCharge}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: right;">Tax ({$taxPercent}%)</td>
                            <td valign="top" style="text-align: right;">{$currencySign} {$tax}</td>
                        </tr>
                        <tr style="border-bottom: hidden;">
                            <th colspan="2" style="text-align: right;"><span class="font-size-xl ">Total</span></th>
                            <th style="text-align: right;"><span class="font-size-xl ">{$currencySign} {$format}</span></th>
                        </tr>
                        <tr style="border-left: hidden; border-right:hidden; {$borderBottom}">
                            <th colspan="3" style="text-align: right;">&nbsp;</th>
                        </tr>
HTML;
        }

        if (strlen($htmlTable)==0) return '';

        return <<< HTML
            <div>
                <table class="table table-sm table-vtop" width="100%" style="border-width: 5px 5px 0px 5px;">
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
        
        //curency sign
        $setting = new SettingModel();
        $currencySign = $setting->getCurrencySign();

        foreach ($vods as $item){
            $title = $item['title'];
            $purchaseAmount = $item['purchase_amount'];
            $tax = $item['tax'];
            $total = $purchaseAmount + $tax;
            $grandTotal += $total;

            $format = number_format($total);

            $htmlRow .= <<< HTML
                        <tr style="border-bottom: hidden;">
                            <td width="10px" style="border-right: hidden;"></td>
                            <td>{$title}</td>
                            <td valign="top" style="text-align: right;">{$currencySign} {$format}</td>
                        </tr>
HTML;
        }

        $grandTotal = number_format($grandTotal);

        //add grand total
        $htmlRow .= <<< HTML
                        <tr></tr>
                        <tr style="border-bottom: hidden;">
                            <th colspan="2" style="text-align: right; padding-bottom: 2em;"><span class="font-size-xl ">Total</span></th>
                            <th style="text-align: right; padding-bottom: 2em;"><span class="font-size-xl ">{$currencySign} {$grandTotal}</span></th>
                        </tr>
HTML;

        return <<< HTML
            <div>
                <table class="table table-sm table-vtop" width="100%" style="border-width: 5px 5px 0px 5px;">
                    <tbody>
                        <tr>
                            <th colspan="3" style="border-top: hidden;"><h4 class="font-w300 mb-2">Purchased VODs</h4></th>
                        </tr>
                        <tr>
                            <td></td>
                            <td><b>VOD Title</b></td>
                            <td style="text-align:right; width: 30%"><b>Price</b></td>
                        </tr>
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
//            NotificationModel::sendStateToSubscriber($id);

            //send update with dispatcher
            $disp = new DipatcherModel();
            $disp->sendToSubscriber($id, json_encode( ['type'=>'guest_state'] ));


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
