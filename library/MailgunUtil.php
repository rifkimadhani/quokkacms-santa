<?php

require_once "../library/Log.php";

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 10/31/2017
 * Time: 5:50 PM
 */
class MailgunUtil
{

    /**
     * @param $url 'https://api.mailgun.net/v3/homeconnectapp.com/messages
     * @param $key
     * @param $emailFrom
     * @param $emailTo
     * @param $subject
     * @param $html email body dalam html
     * @return bool = true success, false = fail, error will be logged to LOG
     */
    public static function send($url, $key, $emailFrom, $emailTo, $subject, $html)
    {

//        $url = 'https://api.mailgun.net/v3/homeconnectapp.com/messages';
        //YXBpOmtleS03ZTcyZDczOWM4ZjE2Nzg0NzQyNzUyZTk1OTg4MDYzZA==

        $authorization = base64_encode("api:" . $key);


//        var_dump($url);
//        var_dump($authorization);


        $header = "Content-type: application/x-www-form-urlencoded\r\n" .
            "authorization: Basic $authorization\r\n";

        $data = array(
            'from' => $emailFrom,
            'subject' => $subject,
            'to' => $emailTo,
            'html' => $html,
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

}