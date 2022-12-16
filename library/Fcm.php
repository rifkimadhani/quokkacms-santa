<?php
/**
 * Created by PhpStorm.
 * User: user01
 * Date: 21/06/2017
 * Time: 13:50
 */
class Fcm{
    function __construct(){
      //  require_once '../library/Log.php';
       // require_once '../v1/ErrorAPI.php';
       // require_once '../model/ModelSession.php';
       // require_once '../model/ModelDiscovery.php';
       // require_once '../library/DateUtil.php';
       // require_once '../library/Security.php';
    }

    public function sendToGroup($message,$groupname){
        $fields = array(
                        'registration_ids'  => $groupname,
                        'data'              => $message
                        );
        return $this->sendPushNotification($fields);
    }

    public function sendToUser($message,$token){
        $fields = array(
                        'to' => $token,
                        'data' => $message
                        );
        return $this->sendPushNotification($fields);
    }

    public function subscribe($token,$groupname){
        $fields = array(
                        'to' => $token,
                        'data' => 'Someone has joined in '.$groupname
                        );
        return $this->sendPushNotification($fields);
    }

    public function unsubscribe($token,$groupname){
        $fields = array(
            'to' => $token,
            'data' => 'Someone has left the '.$groupname
        );
        return $this->sendPushNotification($fields);
    }

    private function sendPushNotification($fields){
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
                         'Authorization: key=' . 'AAAAG3u1YcY:APA91bFjbKIYMN1qN9gnQEL03odMNIRPokoPUAZkPSrc_LlIlKBWixh_WL6wPuAoQXEpwsuJVjgQgP4peXpcNQ9fOadaI8O582mHWwyJpVAOSaW_nreJoYo8a0p3kUzQ-d28nY-EtQeF',
                         'Content-Type: application/json'
                        );
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if($result == false){
            die('Curl failed: '. curl_errno($ch));
        }
        curl_close($ch);
        return $result;
    }

}


//action atau function yang dipilih
if(isset($_GET['action'])){
    $action = $_GET['action'];
}elseif(isset($_POST['action'])){
    $action = $_POST['action'];
}else{
    $action = null;
}
//get token
if(isset($_GET['token'])){
    $sessionId = $_GET['token'];
}elseif(isset($_POST['token'])){
    $sessionId = $_POST['token'];
}else{
    $sessionId = null;
}

switch($action){
    case 'sentToGroup':
        sentToGroup();
        break;
    case 'sentToUser':
        sentToUser();
        break;
    case  'subscribe':
        subscribe();
        break;
    case 'unSubscribe':
        unSubscribe();
        break;
}



?>