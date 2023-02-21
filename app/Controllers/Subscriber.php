<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 8:42 AM
 */

namespace App\Controllers;

use App\Models\RoomModel;
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

    public function ssp()
    {
        $model = new SubscriberModel();

        header('Content-Type: application/json');
        echo json_encode($model->getSsp());
    }

    public function sspRoom($subscriberId)
    {
        $model = new SubscriberRoomModel();

        header('Content-Type: application/json');
        echo json_encode($model->getssp($subscriberId));
    }

    public function insert(){

        log_message('error', json_encode($_POST));

        $rooms = $_POST['room_id'];

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

        $group = new SubscriberGroupModel();
        $groupData = $group->getAllActiveForSelect();

        //utk form edit, room tdk bisa di rubah2 lagi, shg tdk di perlukan
        $form = new SubscriberForm([], $groupData);
        unset($form->room_id); //di remove krm room tdk bisa di rubah2 setelah di create

        return view('layout/template', compact('mainview','primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'subscriberId', 'subscriberData', 'form'));
    }

//    public function edit($messageId)
//    {
//        $model = new MessageModel();
//        $data = $model->get($messageId);
//
//        //ambil image dari table
//        $media = new MessageMediaModel();
//        $ar = $media->getAll($messageId);
//
//        //rubah array ke string
//        $urlImage = '';
//        foreach ($ar as $row){
//            if (strlen($urlImage)==0){
//                $urlImage = $row['url_image'];
//            } else {
//                $urlImage .= ',' . $row['url_image'];
//            }
//        }
//
//        //convert {BASE-HOST} --> URL
//        $urlImage = str_replace('{BASE-HOST}', $this->baseHost, $urlImage);
//
//        $data['url_image'] = $urlImage; //simpan hasil conversi ke dalam url_image
//
//        //cari subscriber
//        $subscriber = new SubscriberModel();
//        $subscriberData = $subscriber->getCheckinForSelect();
//
//        $subscriberId = $data['subscriber_id'];
//
//        //cari apakah subscriber pada message ada di daftar subscriber ??
//        $found = false;
//        foreach ($subscriberData as $item){
//            if ($item['id']==$subscriberId){
//                $found = true;
//                break;
//            }
//        }
//
//        //apabila subscriber tdk ada, ambil dari db dan tambahkan ke dalam dafar subscriber
//        if ($found==false){
//            //cari pada db
//            $subscriberCurrent = $subscriber->get($subscriberId);
//
//            //tambahkan subscriber ke dalam daftar
//            $value = $subscriberCurrent['name'] . ' ' . $subscriberCurrent['last_name'];
//            $subscriberData[] = ['id'=>$subscriberId, 'value'=>$value];
//        }
//
//        $form = new MessageForm($subscriberData);
//
//        $urlAction = $this->baseUrl . '/update';
//        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
//    }

    public function update(){
        $id = $_POST['subscriber_id'];

        $model = new SubscriberModel();
        $r = $model->modify($id, $_POST);

        if ($r>0){
            $this->setSuccessMessage('UPDATE success');
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
