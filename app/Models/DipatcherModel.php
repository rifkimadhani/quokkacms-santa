<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 10/4/2023
 * Time: 9:39 AM
 */

namespace App\Models;

use App\Libraries\Dispatcher;

class DipatcherModel extends BaseModel
{
    private $hostname = null; //dispatcher hostname
    private $port = null;

    /**
     * get hostname & port from tsetting
     * DipatcherModel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $setting = new SettingModel();
        $data = $setting->getDispatcherServer();

        $this->hostname = $data['value_string'];
        $this->port = (int) $data['value_int'];
    }

    public function send($channel, $message){
        try{
            $dispatcher = new Dispatcher();
            if ($dispatcher->connect($this->hostname, $this->port)){
                $dispatcher->send($channel, $message);
                $dispatcher->close();
            }
        }catch (\Exception $e){
            $this->loge($e->getMessage());
        }
    }

    /**
     * kirim ke channel 'device-XX'
     *
     * @param $stbId
     * @param $meesage
     */
    public function sendToStb($stbId, $message){
        $channel = "device-{$stbId}";
        $this->send($channel, $message);
    }

    /**
     * kirim message ke subscriber,
     * setiap subscriber bisa rent beberapa kamar
     *
     * @param $subscriberId
     * @param $message
     */
    public function sendToSubscriber($subscriberId, $message){
        $subscriber = new SubscriberModel();

        //cari list kamar yg di pakai subscriber
        $list = $subscriber->getRoom($subscriberId);

        $channel = '';
        foreach ($list as $item){
            $roomId = $item['room_id'];
            if (empty($channel)){
                $channel = "room-{$roomId}";
            } else {
                $channel .= ",room-{$roomId}";
            }
        }

        $this->send($channel, $message);
    }

    public function sendToRoom($roomId, $meesage){

    }

    public function sendToGroup($groupId, $meesage){

    }

    public function sendToAll($meesage){

    }
}