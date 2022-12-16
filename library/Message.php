<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 7/22/2017
 * Time: 5:45 PM
 */

require_once 'HTTPRequest.php';
define("APIKEY", "AAAAG3u1YcY:APA91bFjbKIYMN1qN9gnQEL03odMNIRPokoPUAZkPSrc_LlIlKBWixh_WL6wPuAoQXEpwsuJVjgQgP4peXpcNQ9fOadaI8O582mHWwyJpVAOSaW_nreJoYo8a0p3kUzQ-d28nY-EtQeF");

class FirebaseMessage
{
    const TAG = "Message";
    const URL_ADDDEVICETOTOPIC = "https://iid.googleapis.com/iid/v1:batchAdd";
    const URL_REMOVEDEVICETOTOPIC = "https://iid.googleapis.com/iid/v1:batchRemove";
    const URL_SENDTOTOPIC = "https://fcm.googleapis.com/fcm/send";

    public static function subscribeToTopic(string $apiKey, string $deviceToken, string $topic)
    {
        $tokens = array("{$deviceToken}");
        $data = array("to" => "/topics/{$topic}", "registration_tokens" => $tokens);

        $result = HTTPRequest::post(self::URL_ADDDEVICETOTOPIC,
            array("Authorization:key={$apiKey}",
                "Content-Type:application/json"), json_encode($data));

        return $result;
    }

    public static function unsubscribeFromTopic(string $apiKey, string $deviceToken, string $topic)
    {
        $tokens = array("{$deviceToken}");
        $data = array("to" => "/topics/{$topic}", "registration_tokens" => $tokens);

        $result = HTTPRequest::post(self::URL_REMOVEDEVICETOTOPIC,
            array("Authorization:key={$apiKey}",
                "Content-Type:application/json"), json_encode($data));

        return $result;
    }

    public static function sendToTopic(string $apiKey, string $topic, string $message)
    {
        $data = array("message" => $message);
        $notification = array("body" => $message);
        $payload = array("to" => "/topics/{$topic}", "data" => $data);

        if (isset($notification)){
            $payload["notification"] = $notification;
        }

        $str = json_encode($payload);
        $result = HTTPRequest::post(self::URL_SENDTOTOPIC,
            array("Authorization:key={$apiKey}",
                "Content-Type:application/json"), $str);

        return $result;
    }
}