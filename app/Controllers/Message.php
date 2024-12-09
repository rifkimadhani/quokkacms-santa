<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 8:42 AM
 */

namespace App\Controllers;

//use App\Models\DipatcherModel;
use App\Models\MessageForm;
use App\Models\MessageFormGroup;
use App\Models\MessageFormRoom;

use App\Models\MessageMediaModel;
use App\Models\MessageModel;
use App\Models\NotificationModel;
use App\Models\SubscriberModel;
use App\Models\RoomModel;
use App\Models\SubscriberGroupModel;
use App\Models\SubscriberRoomModel;

class Message extends BaseController
{
    public function index()
    {
        $baseUrl = $this->baseUrl;

        $mainview = "message/index";
        $primaryKey = 'message_id';
        $pageTitle = 'Messages';

        $group = new MessageModel();
        $fieldList = $group->getFieldList();

        // $room = new RoomModel();
        // $roomData = $room->getForSelect();

        // $roomData = $group->getRoomForSelect();

        $subscriber = new SubscriberModel();
        $subscriberData = $subscriber->getCheckinForSelect();

        $form = new MessageForm($subscriberData);

        $groupModel = new SubscriberGroupModel();
        $groupData = $groupModel->getAllActiveForSelect();
        $formGroup = new MessageFormGroup($groupData);

        $room = new RoomModel();
        $listRoom = $room->getForSelect();
        $formRoom = new MessageFormRoom($listRoom);

        return view('layout/template', compact('mainview','primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form', 'formGroup', 'formRoom'));
    }

    public function history()
    {
        $baseUrl = $this->baseUrl;

        $mainview = "message/history";
        $primaryKey = 'message_id';
        $pageTitle = 'Messages History';

        $group = new MessageModel();
        $fieldList = $group->getFieldList();

        // $room = new RoomModel();
        // $roomData = $room->getForSelect();

        // $roomData = $group->getRoomForSelect();

        $subscriber = new SubscriberModel();
        $subscriberData = $subscriber->getCheckinForSelect();

        $form = new MessageForm($subscriberData);

        $groupModel = new SubscriberGroupModel();
        $groupData = $groupModel->getAllActiveForSelect();
        $formGroup = new MessageFormGroup($groupData);


        return view('layout/template', compact('mainview', 'primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form', 'formGroup'));
    }

    public function ssp()
    {
        $model = new MessageModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        echo json_encode($data);
    }

    /**
     * Retrieves SSP history data (checkout's subsciber) from the `MessageModel` and converts it to
     * JSON format before sending it as a response.
     */
    public function sspHistory()
    {
        $model = new MessageModel();

        header('Content-Type: application/json');

        $data = $model->getSspHistory();

        echo json_encode($data);
    }

    public function insert(){
        $r = $this->insertData($_POST);

        if ($r>0){
            $this->setSuccessMessage('Messages sent');
        } else {
            $this->setErrorMessage('Message fail to sent');
        }

        return redirect()->to($this->baseUrl);
    }

    public function insertGroup(){
        $groupId = $_POST['group_id'];

        //cari semua subscriber dari group yg di maksud
        $model = new MessageModel();
        $listSubs = $model->getSubscribersByGroup($groupId);

        $data = $_POST;

        //kirim message ke setiap subscriber
        $r = 0;
        foreach ($listSubs as $item){
            $data['subscriber_id'] = $item['subscriber_id'];
            $r += $this->insertData($data);
        }

        if ($r>0){
            $this->setSuccessMessage('Messages sent');
        } else {
            $this->setErrorMessage('Message fail to sent');
        }

        return redirect()->to($this->baseUrl);
    }

    public function insertRoom(){
        $data = $_POST;
        $r = $this->insertData($data);

        if ($r>0){
            $this->setSuccessMessage('Messages sent');
        } else {
            $this->setErrorMessage('Message fail to sent');
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * Support utk kirim
     * 1. to subscriber
     * 2. to room (subscriber==null)
     *
     * @param $data
     * @return int
     */
    public function insertData(&$data){
        $subscriberId = $data['subscriber_id'] ?? null;
        $urlImage = $data['url_image'];


        $this->varDump($data);


        //convert url -> {BASE-HOST}
        $urlImage = str_replace($this->baseHost, '{BASE-HOST}', $urlImage);

        $model = new MessageModel();
        $media = new MessageMediaModel();

        //apabila ada subscriber maka cari room yg di pakai subscriber
        //apabila tdk ada maka buat listroom dari room_id
        if (isset($subscriberId)){
            $this->loge("subscriberId={$subscriberId}");
            //ambil roomId dari subscriber
            $room = new RoomModel();
            $listRoom = $room->getBySubscriber($subscriberId);
        } else {
            $this->loge("subscriberId=null");
            $data['subscriber_id'] = null;
            $roomId = $data['room_id'];
            $listRoom[] = ['room_id'=>$roomId];
        }

        //create message utk setiap room,
        //apabila subscriber sewa 2 room, maka ada 1 message utk setiap room
        $r = 0;
        foreach ($listRoom as $item){
            $roomId = $item['room_id'];
            $this->loge("roomId={$roomId}");

            //tambahkan room_id
            $data['room_id'] = $roomId;

            //insert message
            $messageId = $model->add($data);

            //insert gambar
            $media->write($messageId, $urlImage);

            $r++;
        }

        //kirim lewat dispatcher
//        $disp = new DipatcherModel();
//        $disp->sendToSubscriber($subscriberId, json_encode( ['type'=>'message'] ));

        return $r;
    }



    public function insertGroup_old(){
        $from = $_POST['from'];
        $groupId = $_POST['group_id'];
        $title = $_POST['title'];
        $message = $_POST['message'];
        $urlImage = $_POST['url_image'];  
        
        $model = new MessageModel();

        // get all the active subscriber_id based on group_id
        $subscriberIds = $model->getSubscribersByGroup($groupId);

        $media = new MessageMediaModel();
        
        //convert url -> {BASE-HOST}
        $urlImage = str_replace($this->baseHost, '{BASE-HOST}', $urlImage);

        $rooms = '';
        $subsRoom = new SubscriberRoomModel();

        // looping to push to db and built channel name
        foreach ($subscriberIds as $subscriberId){
            $messageId = $model->add([
                'from' => $from,
                'title' => $title,
                'message' => $message,
                'group_id' => $groupId,
                'subscriber_id' => $subscriberId
            ]);
            $media->write($messageId, $urlImage);

            $listRoom = $subsRoom->getRoom($subscriberId);
            foreach ($listRoom as $item){
                $roomId = $item['room_id'];
                if (empty($rooms)){
                    $rooms = "room-{$roomId}";
                } else {
                    $rooms .= ",room-{$roomId}";
                }
            }

            //kirim notifikasi ke stb
//            NotificationModel::sendMessageToSubscriber($subscriberId);
        }

        //kirim event dgn dispatcher
//        $disp = new DipatcherModel();
//        $disp->send($rooms, json_encode( ['type'=>'message'] ));

        if ($messageId>0){
            $this->setSuccessMessage('Messages sent');
        } else {
            $this->setErrorMessage('Messages fail to sent ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function edit($messageId)
    {
        $model = new MessageModel();
        $data = $model->get($messageId);

        //ambil image dari table
        $media = new MessageMediaModel();
        $ar = $media->getAll($messageId);

        //rubah array ke string
        $urlImage = '';
        foreach ($ar as $row){
            if (strlen($urlImage)==0){
                $urlImage = $row['url_image'];
            } else {
                $urlImage .= ',' . $row['url_image'];
            }
        }

        //convert {BASE-HOST} --> URL
        $urlImage = str_replace('{BASE-HOST}', $this->baseHost, $urlImage);

        $data['url_image'] = $urlImage; //simpan hasil conversi ke dalam url_image

        //cari subscriber
        $subscriber = new SubscriberModel();
        $subscriberData = $subscriber->getCheckinForSelect();

        $subscriberId = $data['subscriber_id'];

        //cari apakah subscriber pada message ada di daftar subscriber ??
        $found = false;
        foreach ($subscriberData as $item){
            if ($item['id']==$subscriberId){
                $found = true;
                break;
            }
        }

        //apabila subscriber tdk ada, ambil dari db dan tambahkan ke dalam dafar subscriber
        if ($found==false){
            //cari pada db
            $subscriberCurrent = $subscriber->get($subscriberId);

            //tambahkan subscriber ke dalam daftar
            $value = $subscriberCurrent['name'] . ' ' . $subscriberCurrent['last_name'];
            $subscriberData[] = ['id'=>$subscriberId, 'value'=>$value];
        }

        $roomData = $model->getRoomForSelect();
        $form = new MessageForm($subscriberData, $roomData);

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $id = $_POST['message_id'];
        $subscriberId = $_POST['subscriber_id'];
        // $roomId = $_POST['room_id'];
        $urlImage = $_POST['url_image'];

        //convert URL --> {BASE-HOST}
        $urlImage = str_replace($this->baseHost, '{BASE-HOST}', $urlImage);

        // // room_id tidak dipakai lagi
        // $_POST['room_id'] = null;

        $model = new MessageModel();

        $r = $model->modify($id, $_POST);

        $media = new MessageMediaModel();
        $media->write($id, $urlImage);

        if ($r>0){
            $this->setSuccessMessage('UPDATE success');
        } else {
            $this->setErrorMessage('UPDATE failed ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($messageId){
        $model = new MessageModel();
        $r = $model->remove($messageId);

        if ($r){
            $this->setSuccessMessage('DELETE success');
        } else {
            $this->setErrorMessage('DELETE fail');
        }

        return redirect()->to($this->baseUrl);
    }

    /*
    * Update & Delate History Messages
    *
    */
    public function editHistory($messageId)
    {
        $model = new MessageModel();
        $data = $model->get($messageId);

        //ambil image dari table
        $media = new MessageMediaModel();
        $ar = $media->getAll($messageId);

        //rubah array ke string
        $urlImage = '';
        foreach ($ar as $row) {
            if (strlen($urlImage) == 0) {
                $urlImage = $row['url_image'];
            } else {
                $urlImage .= ',' . $row['url_image'];
            }
        }

        //convert {BASE-HOST} --> URL
        $urlImage = str_replace('{BASE-HOST}', $this->baseHost, $urlImage);

        $data['url_image'] = $urlImage; //simpan hasil conversi ke dalam url_image

        //cari subscriber
        $subscriber = new SubscriberModel();
        $subscriberData = $subscriber->getCheckinForSelect();

        $subscriberId = $data['subscriber_id'];

        //cari apakah subscriber pada message ada di daftar subscriber ??
        $found = false;
        foreach ($subscriberData as $item) {
            if ($item['id'] == $subscriberId) {
                $found = true;
                break;
            }
        }

        //apabila subscriber tdk ada, ambil dari db dan tambahkan ke dalam dafar subscriber
        if ($found == false) {
            //cari pada db
            $subscriberCurrent = $subscriber->get($subscriberId);

            //tambahkan subscriber ke dalam daftar
            $value = $subscriberCurrent['name'] . ' ' . $subscriberCurrent['last_name'];
            $subscriberData[] = ['id' => $subscriberId, 'value' => $value];
        }

        $roomData = $model->getRoomForSelect();
        $form = new MessageForm($subscriberData, $roomData);

        $urlAction = $this->baseUrl . '/history/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function updateHistory()
    {

        $errorMessage = "The Data on Message History cannot be Modified.";
        $this->setErrorMessage($errorMessage);

        return redirect()->to($this->baseUrl . '/history');
    }

    public function deleteHistory($messageId)
    {
        $model = new MessageModel();
        $r = $model->remove($messageId);

        if ($r) {
            $this->setSuccessMessage('DELETE success');
        } else {
            $this->setErrorMessage('DELETE fail');
        }

        return redirect()->to($this->baseUrl . '/history');
    }

    /**
     * melakukan conversi data ke asalnya, misalnya utk url balik dari BASE-HOST -> http://
     * @param $data
     */
    protected function sspDataConversion(&$data){
        foreach($data['data'] as &$row){

        }
    }
}