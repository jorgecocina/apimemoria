<?php


namespace App\Business;

use App\Models\Users;
use App\Helpers\Validator;

class UsersBSN extends BaseBSN
{

    const ROLE_NORMAL = 2;

    public function getById($param) {
        if (!isset($param['id'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (intval($param['id']) == 0) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }


        $user = Users::findFirstById($param['id']);
        if (!$user) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        return $user->toArray();
    }

    public function create($param) {
        if (
            !isset($param['username'])
            || !isset($param['email'])
            || !isset($param['public_name'])
            || !isset($param['password'])
        ) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !Validator::is_email($param['email'])
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (Users::findFirst(['email = :email:', 'bind' => ['email' => $param['email']]])) {
            $this->error[] = self::EMAIL_ALREADY_EXISTS;
            return false;
        }

        if (Users::findFirst(['username = :username:', 'bind' => ['username' => $param['username']]])) {
            $this->error[] = self::USERNAME_ALREADY_EXISTS;
            return false;
        }

        $user = new Users();
        $user->username = $param['username'];
        $user->email = $param['email'];
        $user->public_name = $param['public_name'];
        $user->password = $param['password'];
        $user->roles_id = self::ROLE_NORMAL;

        if (!$user->save()) {
            $err = self::ERROR_DATABASE;
            foreach ($user->getMessages() as $message) {

                $err['message'] =$err['message'] . ' * ' . $message->getMessage();
            }
            $this->error[] = $err;

            return false;
        }

        return $user->id;

    }

    public function update($param) {
        if (
            !isset($param['id'])
            || (!isset($param['username'])
            && !isset($param['email'])
            && !isset($param['public_name'])
            && !isset($param['password']))
        ) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (isset($param['email']) && !Validator::is_email($param['email'])) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        $user = Users::findFirstById($param['id']);
        if (!$user) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        if (isset($param['email']) && Users::findFirst(['email = :email:', 'bind' => ['email' => $param['email']]])) {
            $this->error[] = self::EMAIL_ALREADY_EXISTS;
            return false;
        }

        if (isset($param['username']) && Users::findFirst(['username = :username:', 'bind' => ['username' => $param['username']]])) {
            $this->error[] = self::USERNAME_ALREADY_EXISTS;
            return false;
        }
        if (isset($param['username'])) {
            $user->username = $param['username'];
        }
        if (isset($param['email'])) {
            $user->email = $param['email'];
        }
        if (isset($param['public_name'])) {
            $user->public_name = $param['public_name'];
        }
        if (isset($param['password'])) {
            $user->password = $param['password'];
        }

        if (!$user->save()) {
            $err = self::ERROR_DATABASE;
            foreach ($user->getMessages() as $message) {

                $err['message'] =$err['message'] . ' * ' . $message->getMessage();
            }
            $this->error[] = $err;

            return false;
        }

        return true;
    }

    public function login($param) {

        if (!isset($param['username']) || !isset($param['password'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        $user = Users::findFirst(['username = :username:', 'bind' => ['username' => $param['username']]]);

        if (!$user) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }
        $auth = $this->di->get('AuthToken');
        $token = $auth->login($param['username'], $param['password']);
        if (!$token) {
            $this->error = self::LOGIN_VALIDATION_ERROR;
        }
        return ['token' => $token, 'id' => $user->id];
    }

}