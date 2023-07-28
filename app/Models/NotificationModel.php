<?php

namespace App\Models;

use App\Models\STBDevicesModel;
use App\Libraries\Notification;

class NotificationModel extends BaseModel
{
    const PORT = 8002;

    /**
     * Send theme update to all / subscriber
     *
     * @param $subscriberId
     */
    static public function sendThemeUpdateToSubscriber($subscriberId)
    {
        $json = json_encode( ['type'=>'theme'] );
        self::sendToSubscriber($subscriberId, $json);
    }

    static public function sendThemeUpdateToAll()
    {
        $json = json_encode( ['type'=>'theme'] );
        self::sendToAll($json);
    }

    /**
     * Send checkout/in notification
     * @param $subscriberId
     */
    static public function sendCheckinToSubscriber($subscriberId)
    {
        $json = json_encode( ['type'=>'guest_checkin'] );
        self::sendToSubscriber($subscriberId, $json);
    }

    static public function sendCheckoutToSubscriber($subscriberId)
    {
        $json = json_encode( ['type'=>'guest_checkout'] );
        self::sendToSubscriber($subscriberId, $json);
    }

    static public function sendStateToSubscriber($subscriberId)
    {
        $json = json_encode( ['type'=>'guest_state'] );
        self::sendToSubscriber($subscriberId, $json);
    }

    /**
     * Send emergency warning to all stb
     *
     * @param $on
     */
    static public function sendEmergencyWarningToAll($on)
    {
        if ($on)
        {
            $json = json_encode( ['type'=>'emergency_warning_1'] );
        }
        else 
        {
            $json = json_encode( ['type'=>'emergency_warning_0'] );
        }
        self::sendToAll($json);
    }

    /**
     * Kirim tourist info update ke all
     */
    static public function sendTouristInfoUpdateToAll()
    {
        $json = json_encode( ['type'=>'locality'] );
        self::sendToAll($json);
    }

    static public function sendServiceUpdateToAll()
    {
        $json = json_encode( ['type'=>'facility'] );
        self::sendToAll($json);
    }

    /**
     * Update background music ke all
     */
    static public function sendBackgroundMusicUpdateToAll()
    {
        $json = json_encode( ['type'=>'background_music'] );
        self::sendToAll($json);
    }

    /**
     * Send livetv update ke subscriber / all
     *
     * @param $subscriberId
     */
    static public function sendLivetvUpdateToSubscriber($subscriberId)
    {
        $json = json_encode( ['type'=>'livetv'] );
        self::sendToSubscriber($subscriberId, $json);
    }

    static public function sendLivetvUpdateToRoom($roomId)
    {
        $json = json_encode( ['type'=>'livetv'] );
        self::sendToRoom($roomId, $json);
    }

    static public function sendLivetvUpdateToAll()
    {
        $json = json_encode( ['type'=>'livetv'] );
        self::sendToAll($json);
    }

    /**
     * Send Advertisement update ke all
     */
    static public function sendAdsUpdateToAll()
    {
        $json = json_encode( ['type'=>'ads'] );
        self::sendToAll($json);
    }

    /**
     * Send message notification
     *
     * @param $roomId
     */
    static public function sendMessageToRoom($roomId)
    {
        $json = json_encode( ['type'=>'message'] );
        self::sendToRoom($roomId, $json);
    }

    static public function sendMessageToSubscriber($subscriberId)
    {
        $json = json_encode( ['type'=>'message'] );
        self::sendToSubscriber($subscriberId, $json);
    }

    static public function sendMessageToAll()
    {
        $json = json_encode( ['type'=>'message'] );
        self::sendToAll($json);
    }

    /**
     *  Send game update to all
     */
    static public function sendGameUpdateToAll()
    {
        $json = json_encode( ['type'=>'game'] );
        self::sendToAll($json);
    }


    /**
     * Kirim paket ke 1 stb
     *
     * @param $stbId
     * @param $json
     */
    public function sendToStb($stbId, $json)
    {
        $model = new STBDevicesModel();
        $stb = $model->getIPSTBDevicesBySTBID($stbId);

        $ip = $stb['ip_address'];

        if(isset($ip))
        {
            $r = $this->notificationutil->send($ip, NotificationModel::PORT, $json);
        }
    }

    public function sendToRoom($roomId, $json)
    {
        $model = new STBDevicesModel();
        $ar = $model->getIPSTBDevicesByRoomID($roomId);

        set_time_limit(0);

        foreach ($ar as $item)
        {
            $ip = $item['ip_address'];
            if(isset($ip))
            {
                $r = $this->notificationutil->send($ip, NotificationModel::PORT, $json);
            }
        }

        set_time_limit(30);
    }

    static public function sendToSubscriber($subscriberId, $json)
    {
        $model = new STBDevicesModel();
        $ar = $model->getIPSTBDevicesBySubscriberID($subscriberId);
        set_time_limit(0);
        foreach ($ar as $item)
        {
            $ip = $item['ip_address'];
            if(isset($ip))
            {
                $r = Notification::send($ip, NotificationModel::PORT, $json);
            }
        }
        set_time_limit(30);
    }

    static public function sendToAll($json)
    {
        $model = new STBDevicesModel();
        $ar = $model->getActiveIp();
        set_time_limit(0);
        foreach ($ar as $item)
        {
            $ip = $item['ip_address'];
            if(isset($ip))
            {
                $r = Notification::send($ip, NotificationModel::PORT, $json);
            }
        }
        set_time_limit(30);
    }
}