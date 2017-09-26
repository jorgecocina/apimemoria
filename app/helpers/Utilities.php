<?php
/**
 * Created by PhpStorm.
 * User: yuroa
 * Date: 31-07-2017
 * Time: 19:16
 */

namespace App\Helpers;


class Utilities
{
    public static function generateRandomString($length = 3) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ*+.:;,?¿!|#';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}