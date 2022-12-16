<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 7/28/2017
 * Time: 7:15 AM
 *
 * ConnectionUtil Class
 * Tujuan untuk memutuskan hubungan antara server dgn client, dan melanjutkan process di backgroun
 */
class ConnectionUtil
{
    /**
     * @param $extra, masukkan semua parameter yg akan di pergunakan ke dalam $extra
     * @param $callback, function call back yg akan di panggil
     * @param bool $ignoreUserAbout
     */
    public static function quickReply($callback, bool $ignoreUserAbort=true) : void {
        self::begin($ignoreUserAbort);
        $callback();
        self::end();
    }

    public static function begin(bool $ignoreUserAbort=true) : void {
        ob_end_clean();
        header("Connection: close\r\n");
        header("Content-Encoding: none\r\n");
        if ($ignoreUserAbort) ignore_user_abort(true); // optional
        ob_start();
    }

    public static function end() : void {
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();     // Strange behaviour, will not work
        flush();            // Unless both are called !
        if (ob_get_length()) ob_end_clean();
    }
}