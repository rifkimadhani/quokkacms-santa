<?php

require_once __DIR__ . "/../library/Log.php";

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 10/31/2017
 * Time: 5:50 PM
 */
class Mailgun
{
    const API = 'https://api.mailgun.net/v3/{DOMAIN}/messages';

    //sample utk mailgun yg lbh lengkap, mempergunakan curl
    function sendmailbymailgun($domain, $key, $emailFrom, $emailTo, $subject, $html, $text){
        $array_data = array(
            'from'=> $emailFrom .'<'.$emailFrom.'>',
            'to'=>$emailTo,
            'subject'=>$subject,
            'html'=>$html,
            'text'=>$html,
            'o:tracking'=>'yes',
            'o:tracking-clicks'=>'yes',
            'o:tracking-opens'=>'yes',
        );

        $url = str_replace('{DOMAIN}', $domain, self::API);

        $session = curl_init($url);
        curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($session, CURLOPT_USERPWD, 'api:'. $key);
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($session);
        curl_close($session);
        $results = json_decode($response, true);
        return $results;
}


    /**
     * @param $domain 'https://api.mailgun.net/v3/{DOMAIN}/messages
     * @param $key
     * @param $emailFrom
     * @param $emailTo
     * @param $subject
     * @param $body body dalam html
     * @return bool = true success, false = fail, error will be logged to LOG
     */
    public static function send($domain, $key, $emailFrom, $emailTo, $subject, $body)
    {

        $url = str_replace('{DOMAIN}', $domain, self::API);

        $authorization = base64_encode("api:" . $key);
        $header = "Content-type: application/x-www-form-urlencoded\r\n" .
            "authorization: Basic $authorization\r\n";

        $data = array(
            'from' => $emailFrom,
            'subject' => $subject,
            'to' => $emailTo,
            'html' => $body,
        );

        $options = array(
            'http' => array(
                'header' => $header,
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) { /* Handle error */
            Log::writeErrorLn("Sending email to $emailTo fail");
            Log::var_dump(error_get_last());
            return false;
        } else {
            return true;
        }

    }

    public static function sendWithCC($domain, $key, $emailFrom, $emailTo, $subject, $html,$CC,$BCC)
    {

        $url = str_replace('{DOMAIN}', $domain, self::API);
        $authorization = base64_encode("api:" . $key);

        $header = "Content-type: application/x-www-form-urlencoded\r\n" .
            "authorization: Basic $authorization\r\n";

        $data = array(
            'from' => $emailFrom,
            'subject' => $subject,
            'to' => $emailTo,
            'html' => $html,
            'cc' => $CC
        );

        $options = array(
            'http' => array(
                'header' => $header,
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
//        var_dump($url);

        if ($result === FALSE) { /* Handle error */
            Log::writeErrorLn("Sending email to $emailTo fail");
            Log::var_dump(error_get_last());
            return false;
        } else {
            return true;
        }

    }

}