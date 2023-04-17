<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 4/17/2023
 * Time: 4:39 PM
 */

namespace App\Libraries;


class StringUtil
{
    static public function startsWith($haystack, $needle) {
        return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
    }

    static public function endsWith($haystack, $needle) {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }
}