<?php

namespace App\Controllers;
use App\Business\ReportsBSN;

class ReportsController extends BaseController
{

    public function newServiceAction() {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $post = $this->request->getPost();
        $session = $this->session->get('user');
        if (isset($session['id'])) {
            $post['user_id'] = $session['id'];
        } else {
            $post['user_id'] = null;
        }

        $bsn = new ReportsBSN();

        $result = $bsn->newService($post);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => ['id' => $result]];

    }

    public function newAction() {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $post = $this->request->getPost();
        $session = $this->session->get('user');
        if (isset($session['id'])) {
            $post['user_id'] = $session['id'];
        } else {
            $post['user_id'] = null;
        }

        $bsn = new ReportsBSN();

        $result = $bsn->newReport($post);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => ['id' => $result]];

    }

    public function deleteAction($id) {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $post = $this->request->getPost();
        $session = $this->session->get('user');
        if (isset($session['id'])) {
            $post['user_id'] = $session['id'];
        } else {
            $post['user_id'] = null;
        }

        $post['id'] = $id;

        $bsn = new ReportsBSN();

        $result = $bsn->deleteReport($post);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => ['status' => $result]];
    }

}