<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 8:42 AM
 */

namespace App\Controllers;

use App\Models\MessageForm;

use App\Models\MessageMediaModel;
use App\Models\MessageModel;
use App\Models\SubscriberModel;
use App\Models\RoomModel;

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

        $room = new RoomModel();
        $roomData = $room->getForSelect();

        $subscriber = new SubscriberModel();
        $subscriberData = $subscriber->getCheckinForSelect();

        $form = new MessageForm($subscriberData, $roomData);


        return view('layout/template', compact('mainview','primaryKey', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $group = new MessageModel();

        $this->response->setContentType("application/json");
        echo json_encode($group->getSsp());
    }

    public function insert(){
//        $subscriberId = $_POST['subscriber_id'];
//        $title = $_POST['title'];
//        $message = $_POST['message'];
//        $status = $_POST['status'];
        $urlImage = $_POST['url_image'];

        $model = new MessageModel();
        $messageId = $model->add($_POST);

        $media = new MessageMediaModel();

        //convert url -> {BASE-HOST}
        $urlImage = str_replace($this->baseHost, '{BASE-HOST}', $urlImage);

        $media->write($messageId, $urlImage);

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

        $form = new MessageForm($subscriberData);

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $id = $_POST['message_id'];
        $urlImage = $_POST['url_image'];

        //convert URL --> {BASE-HOST}
        $urlImage = str_replace($this->baseHost, '{BASE-HOST}', $urlImage);

        // room_id tidak dipakai lagi
        $_POST['room_id'] = null;

        $model = new MessageModel();
        $r = $model->modify($id, $_POST);


        $media = new MessageMediaModel();
        $r = $media->write($id, $urlImage);

        if ($r>0){
            $this->setSuccessMessage('UPDATE success');
        } else {
            $this->setErrorMessage('UPDATE fail ' . $model->errMessage);
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

}
