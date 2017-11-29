<?php

namespace App\Helpers;


class Validator
{

    public static function is_int($num, $extas = null) {
        if (isset($extas['min']) && intval($num) <= $extas['min']) {
            return false;
        }

        if (isset($extas['max']) && intval($num) >= $extas['max']) {
            return false;
        }

        return $num == intval($num);
    }

    public static function is_email($email) {
        return preg_match('#\w(\w|\d|_\-)*@\w(\w|\d|_|\-)+(\.\w)+#',$email, $arr) && $arr[0] == $email;
    }

    public static function is_number($num, $extas = null) {
        if (isset($extas['min']) && floatval($num) < $extas['min']) {
            return false;
        }

        if (isset($extas['max']) && floatval($num) > $extas['max']) {
            return false;
        }

        return $num == floatval($num);
    }

}