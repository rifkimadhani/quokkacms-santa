<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 9/20/2023
 * Time: 4:21 PM
 */

namespace App\Libraries;

class Dispatcher
{
    protected $socket;

    public function connect(string $hostname, int $port){

        try{
            // Create a TCP/IP socket
            $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

            if ($this->socket === false) {
                log_message('error', "Dispatcher::connect - " . socket_strerror(socket_last_error()) . "\n");
                return false;
            }

            // Connect to the remote host
            $result = socket_connect($this->socket, $hostname, $port);

            if ($result === false) {
                log_message('error', "Dispatcher::connect - " . socket_strerror(socket_last_error($this->socket)) . "\n");
                return false;
            }
            return true;

        }catch (\Exception $e){
            log_message('error', "Dispatcher::connect - unable to connect dispatcher server - " . socket_strerror(socket_last_error($this->socket)) . "\n");
            return false;
        }
    }

    /**
     * @param string $channels multiple channel saperate with comma
     * @param string $message
     * @return int
     */
    function send(string $channels, string $message){

        try{
            $data = "DATA$channels\n$message\00";

            // Send the data
            $r = socket_write($this->socket, $data, strlen($data));

            if ($r === false) {
                log_message('error', "Dispatcher::send - " . socket_strerror(socket_last_error($this->socket)) . "\n");
                return 0;
            }

            return $r;

        }catch (\Exception $e){
            log_message('error', "Dispatcher::send - " . socket_strerror(socket_last_error($this->socket)) . "\n");
            return 0;
        }
    }

    function close(){
        try{
            socket_close($this->socket);
        }catch (\Exception $e){

        }
    }

//    function sendTcpPacket($host, $port, $data) {
//        // Create a TCP/IP socket
//        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//
//        if ($socket === false) {
//            echo "socket_create() failed: " . socket_strerror(socket_last_error()) . "\n";
//            return false;
//        }
//
//        // Connect to the remote host
//        $result = socket_connect($socket, $host, $port);
//
//        if ($result === false) {
//            echo "socket_connect() failed: " . socket_strerror(socket_last_error($socket)) . "\n";
//            return false;
//        }
//
//        // Send the data
//        $bytesSent = socket_write($socket, $data, strlen($data));
//
//        if ($bytesSent === false) {
//            echo "socket_write() failed: " . socket_strerror(socket_last_error($socket)) . "\n";
//            return false;
//        }
//
//        // Close the socket
//        socket_close($socket);
//
//        return true;
//    }
//
//// Usage example
//$host = "example.com";
//$port = 80;
//$data = "GET / HTTP/1.1\r\nHost: example.com\r\n\r\n";
//
//if (sendTcpPacket($host, $port, $data)) {
//echo "TCP packet sent successfully.\n";
//} else {
//    echo "Failed to send TCP packet.\n";
//}

}

?>
