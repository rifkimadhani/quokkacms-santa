<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/23/2022
 * Time: 8:42 AM
 */

namespace App\Controllers;

use App\Models\MessageForm;

use App\Models\MessageModel;
use App\Models\RoomModel;
use App\Models\SubscriberModel;
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

        return view('template', compact('mainview','primaryKey', 'fieldList', 'pageTitle', 'baseUrl'));
    }

    public function ssp()
    {
        $group = new MessageModel();

        header('Content-Type: application/json');
        echo json_encode($group->getSsp());
    }

    public function insert(){
        $name = $_POST['name'];
        $status = $_POST['status'];

        $model = new SubscriberGroupModel();
        $r = $model->add(['name'=>$name, 'status'=>$status]);

        return redirect()->to($this->className);
    }

    public function edit($messageId)
    {
        $model = new MessageModel();
        $data = $model->get($messageId);

        $room = new RoomModel();
        $roomData = $room->getForSelect();

        $subscriber = new SubscriberModel();
        $subscriberData = $subscriber->getForSelect();

        $form = new MessageForm($subscriberData, $roomData);

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $id = $_POST['message_id'];

        $model = new MessageModel();
        $r = $model->modify($id, $_POST);

        if ($r>0){
            $this->setSuccessMessage('UPDATE success');
        } else {
            $this->setErrorMessage('UPDATE fail ' . $model->errMessage);
        }

        return redirect()->to($this->className);
    }

    public function delete($messageId){
        $model = new MessageModel();
        $r = $model->remove($messageId);

        if ($r){
            $this->setSuccessMessage('DELETE success');
        } else {
            $this->setErrorMessage('DELETE fail');
        }

        return redirect()->to($this->className);
    }

}
