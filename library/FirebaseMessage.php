<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 7/22/2017
 * Time: 5:45 PM
 */

require_once 'HTTPRequest.php';

/**
 * Class FirebaseMessage
 *
 * @link https://developers.google.com/instance-id/reference/server
 *
 */
class FirebaseMessage
{
    const TAG = "FirebaseMessage";
    const URL_ADDDEVICETOTOPIC = "https://iid.googleapis.com/iid/v1:batchAdd";
    const URL_REMOVEDEVICETOTOPIC = "https://iid.googleapis.com/iid/v1:batchRemove";
    const URL_SENDTOTOPIC = "https://fcm.googleapis.com/fcm/send";

    public static function subscribeToTopic(string $apiKey, string $topic, string $deviceToken)
    {
        $tokens = array("{$deviceToken}");
        $data = array("to" => "/topics/{$topic}", "registration_tokens" => $tokens);

        $result = HTTPRequest::post(self::URL_ADDDEVICETOTOPIC,
            array("Authorization:key={$apiKey}",
                "Content-Type:application/json"), json_encode($data));

        return $result;
    }

    public static function unsubscribeFromTopic(string $apiKey, string $topic, string $deviceToken)
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
        include_once "../library/Log.php";
        Log::writeLn("sendToTopic {$topic} -> {$message}");

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

    public static function sendDataToTopic(string $apiKey, string $topic, $data)
    {
        include_once "../library/Log.php";
        Log::writeLn("sendDataToTopic {$topic}");

        $payload = array("to" => "/topics/{$topic}", "data" => $data);

        $str = json_encode($payload);
        $result = HTTPRequest::post(self::URL_SENDTOTOPIC,
            array("Authorization:key={$apiKey}",
                "Content-Type:application/json"), $str);

        return $result;
    }

    public static function sendDataToTargets(string $apiKey, $data, array $tokens)
    {
        include_once __DIR__ . "/../library/Log.php";
        Log::writeLn("sendDataToTarget");

        $payload = array(
            "registration_ids" => $tokens,
            "data" => $data);

        $str = json_encode($payload);
        $strlen = strlen($str);
        $result = HTTPRequest::post(self::URL_SENDTOTOPIC,
            array("Authorization:key={$apiKey}",
                "Content-Length={$strlen}",
                "Content-Type:application/json"), $str);

        return $result;
    }




}