<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 7/22/2017
 * Time: 6:03 PM
 */
class HTTPRequest
{
    public static function post(string $url, array $header, string $data){
//        $data = array('field1' => 'value', 'field2' => 'value');
        $options = array(
            'http' => array(
//                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST'
//                'content' => http_build_query($data),
            )
        );

        if (isset($header)){
            $headerStr = implode("\r\n", $header) . "\r\n";
            $options['http']['header'] = $headerStr;
        }
        if (isset($data)){
            $options['http']['content'] = $data;
        }

//        var_dump($options);

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

//        var_dump($result);

        return $result;
    }


}