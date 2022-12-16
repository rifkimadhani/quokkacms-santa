<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/29/2018
 * Time: 9:44 AM
 */

require_once __DIR__ . '/Log.php';

class Zenziva
{
    const TAG = "Zenziva";
    public const API_SEND_SMS = 'https://alpha.zenziva.net/apps/smsapi.php';
    public const API_SEND_SMS_MASKING = 'https://alpha.zenziva.net/apps/smsapi.php?userkey={userkey}&passkey={passkey}&nohp={nohp}&tipe=otp&pesan={pesan}';

    public static function send(string $userkey, string $passsKey, string $msisdn, string $pesan){
        //https://alpha.zenziva.net/apps/smsapi.php?userkey=a4kjys&passkey=12345678&nohp=082120502570&tipe=otp&pesan=Test

        //buat array utk paramter yg di pakai pada api
        $parameter = [
            '{userkey}'=>$userkey,
            '{passkey}'=>$passsKey,
            '{nohp}'=>$msisdn,
            '{pesan}'=>urlencode($pesan)
        ];

        $url = strtr(Zenziva::API_SEND_SMS_MASKING, $parameter);

        $xml = file_get_contents($url);

        $response = new SimpleXMLElement($xml);
        $status = (string) $response->message->status;
        $text = (string) $response->message->text;

        return ['status'=>$status, 'text'=>$text];
    }


}