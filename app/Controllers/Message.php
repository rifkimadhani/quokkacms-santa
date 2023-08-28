<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 8:42 AM
 */

namespace App\Controllers;

use App\Models\MessageForm;
use App\Models\MessageFormGroup;

use App\Models\MessageMediaModel;
use App\Models\MessageModel;
use App\Models\NotificationModel;
use App\Models\SubscriberModel;
use App\Models\RoomModel;
use App\Models\SubscriberGroupModel;

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

        $roomData = $group->getRoomForSelect();

        $subscriber = new SubscriberModel();
        $subscriberData = $subscriber->getCheckinForSelect();

        $form = new MessageForm($subscriberData, $roomData);

        $groupModel = new SubscriberGroupModel();
        $groupData = $groupModel->getAllActiveForSelect();
        $formGroup = new MessageFormGroup($groupData);


        return view('layout/template', compact('mainview','primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form', 'formGroup'));
    }

    public function ssp()
    {
        $model = new MessageModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $subscriberId = $_POST['subscriber_id'];
        $roomId = $_POST['room_id'];
        $urlImage = $_POST['url_image'];

        // check if room_id based on subscriber_id
        $model = new MessageModel();
        $validRooms = $model->getSubscriberRoom($subscriberId);
        $isValidRoom = false;

        foreach ($validRooms as $room) {
            if ($room['id'] == $roomId) {
                $isValidRoom = true;
                break;
            }
        }
    
        if (!$isValidRoom) {
            $this->setErrorMessage('Invalid Room selected.');
            return redirect()->to($this->baseUrl);
        }
    
        $messageId = $model->add($_POST);

        $media = new MessageMediaModel();

        //convert url -> {BASE-HOST}
        $urlImage = str_replace($this->baseHost, '{BASE-HOST}', $urlImage);

        $media->write($messageId, $urlImage);

        //kirim notifikasi ke stb
        NotificationModel::sendMessageToSubscriber($subscriberId);

        if ($messageId>0){
            $this->setSuccessMessage('Messages sent');
        } else {
            $this->setErrorMessage('Message fail to sent ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function insertGroup(){
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
        
        // looping to push to db and send notification
        foreach ($subscriberIds as $subscriberId){
            $messageId = $model->add([
                'from' => $from,
                'title' => $title,
                'message' => $message,
                'group_id' => $groupId,
                'subscriber_id' => $subscriberId
            ]);
            $media->write($messageId, $urlImage);

            //kirim notifikasi ke stb
            NotificationModel::sendMessageToSubscriber($subscriberId);
        }

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
        $roomId = $_POST['room_id'];
        $urlImage = $_POST['url_image'];

        //convert URL --> {BASE-HOST}
        $urlImage = str_replace($this->baseHost, '{BASE-HOST}', $urlImage);

        // // room_id tidak dipakai lagi
        // $_POST['room_id'] = null;

        $model = new MessageModel();
        $validRooms = $model->getSubscriberRoom($subscriberId);
        $isValidRoom = false;

        foreach ($validRooms as $room) {
            if ($room['id'] == $roomId) {
                $isValidRoom = true;
                break;
            }
        }
    
        if (!$isValidRoom) {
            $this->setErrorMessage('Invalid room selected');
            return redirect()->to($this->baseUrl);
        }

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

    /**
     * melakukan conversi data ke asalnya, misalnya utk url balik dari BASE-HOST -> http://
     * @param $data
     */
    protected function sspDataConversion(&$data){
        return;

        foreach($data['data'] as &$row){

        }
    }

}
