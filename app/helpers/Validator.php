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
        return filter_var($email, FILTER_VALIDATE_EMAIL);
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