<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 7/26/2023
 * Time: 3:27 PM
 */

namespace App\Libraries;

class Notification
{
    /**
     * Send 1 packet to ip & port
     * s
     * @param $destIp
     * @param $destPort
     * @param $message
     */
    static public function send($ip, $port, $message)
    {
        // Create a TCP socket
        $socket = null;

        try{
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

            // Connect to the target IP and port
            socket_connect($socket, $ip, $port);

            // Send the TCP packet
            socket_write($socket, $message, strlen($message));
        } catch (\Exception $e) {
            log_message('error',"ip = $ip");
            log_message('error',"$e");
        } finally{
            // Close the socket
            if ($socket!=null) socket_close($socket);
        }

    }

    static public function send_old($destIp, $destPort, $message)
    {
//        $reply = "";
        $fp = fsockopen($destIp, $destPort, $errno, $errstr, 2);
        if (!$fp)
        {
            return "$errstr ($errno)<br />\n";
            log_message('error',"$errstr ($errno)<br />\n");
        }
        else
        {
            fwrite($fp, $message);
            fclose($fp);
        }

//        return $reply;
    }

    /**
     * Kirim message to many ip
     *
     * @param $arDestIp
     * @param $destPort
     * @param $message
     */
    static public function sendMany($arDestIp, $destPort, $message)
    {

        foreach ($arDestIp as $destIp)
        {
            self::send($destIp, $destPort, $message);
        }

    }
}