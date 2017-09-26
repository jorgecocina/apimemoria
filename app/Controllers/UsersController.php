<?php

namespace App\Controllers;
use App\Business\UsersBSN;
use App\Exceptions\Http401Exception;

class UsersController extends BaseController
{

    public function getByIdAction($id) {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }
        $user = $this->session->get('user');

        if ($user['id'] != $id && $user['role'] != 1) {
            throw new Http401Exception('No tienes permiso para actualizar a otro usuario.');
        }

        $post['id'] = $id;

        $bsn = new UsersBSN();
        $user = $bsn->getById($post);
        if (!$user)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $user];
    }

    public function addNewAction() {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $post = $this->request->getPost();
        $bsn = new UsersBSN();

        $result = $bsn->create($post);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => ['id' => $result]];
    }

    public function updateAction($id)
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $user = $this->session->get('user');
        if (isset($user['id']) && $user['id'] != $id && $user['role'] != 1) {
            throw new Http401Exception('No tienes permiso para actualizar a otro usuario.');
        }


        $post = $this->request->getJsonRawBody(true);
        if (empty($post)) {
            return array('status' => STATUS_ERROR, 'data' => ['description' => [self::BAD_JSON_REQUEST]]);
        }
        $post['id'] = $id;
        $bsn = new UsersBSN();

        $result = $bsn->update($post);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => ['status' => $result]];
    }

    public function loginAction() {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $post = $this->request->getPost();
        $bsn = new UsersBSN();

        $result = $bsn->login($post);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

}