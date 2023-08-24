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
