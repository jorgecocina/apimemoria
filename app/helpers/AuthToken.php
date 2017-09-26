<?php

namespace App\Helpers;
use App\Models\Users;
use Carbon\Carbon;
use App\Helpers\Utilities;

class AuthToken
{
    private $hashkey;
    private $expiration;
    private $protected;
    private $security;

    public function __construct($conf, $sec)
    {
        $this->hashkey = $conf['jwt']['hashkey'];
        $this->expiration = $conf['jwt']['tokenExpiration'];
        $this->protected = $conf['jwt']['protected'];
        $this->security = $sec;
    }

    public function login($username, $password) {

        $user = Users::findFirst(['username = :uname:', 'bind' => ['uname' => $username]]);

        if (!$user) {
            return false;
        }
        $token = false;
        //ver que hacer con el security
        if ($this->security->checkHash($password, $user->password)) {
            $token = $this->generateToken($username, $password, $user->id, $user->roles_id);
        }
        return $token;

    }

    private function generateToken($username, $password, $id, $role) {

        $date =  new \DateTime(); //\Carbon\Carbon::now()->addSeconds($this->expiration);
        $date->add(new \DateInterval('PT'.$this->expiration.'S'));
        return $this->encode($username . '&' . $date->format('Y-m-d H:i:s') . '&' . $password . '&' . $id . '&' . $role);
    }

    private function encode($str) {
        return base64_encode(Utilities::generateRandomString(3) . mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->hashkey), $str, MCRYPT_MODE_CBC, md5(md5($this->hashkey))) . Utilities::generateRandomString(3));
    }

    private function decode($str) {
        $str = base64_decode($str);
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->hashkey), substr($str, 3, strlen($str)-6), MCRYPT_MODE_CBC, md5(md5($this->hashkey))), "\0");
    }

    public function checkToken($token) {
        if (!$this->protected) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        $token = explode('&', $this->decode($token));
        if (count($token) != 5) {
            return false;
        }

        $user = Users::findFirst(['username = :uname:', 'bind' => ['uname' => $token[0]]]);

        if (!$user) {
            return false;
        }

        if (!$this->security->checkHash($token[2], $user->password)) {
            return false;
        }

        $expiration = floatval(str_replace([' ', ':', '-'], ['', '', ''], $token[1]) );
        $now = floatval(str_replace([' ', ':', '-'], ['', '', ''], (new \DateTime() )->format('Y-m-d H:i:s') ) );

        if ($expiration < $now) {
            return false;
        }

        return true;

    }

    public function getInfo($token) {
        $decoded = explode('&', $this->decode($token));

        return [
            'role' => $decoded['4'],
            'id' => $decoded['3'],
            'username' => $decoded[0]
        ];
    }

}