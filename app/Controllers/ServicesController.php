<?php

namespace App\Controllers;
use App\Business\ServicesBSN;
use App\Business\ReportsBSN;

class ServicesController extends BaseController
{

    public function getListPRTAction($x, $y, $rad = null, $type = null) {

        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new ServicesBSN();
        $param = [
            'position_x' => $x,
            'position_y' => $y,
        ];

        if ($rad!=null) {
            $param['radius'] = $rad;
        }
        if ($type!=null) {
            $param['service_type'] = $type;
        }

        $result = $bsn->getList($param);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];

    }

    public function getListAction() {



        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new ServicesBSN();

        $params = $this->request->get();

        $result = $bsn->getList($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];

    }

    public function getListPTRAction($x, $y, $type = null, $rad = null) {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new ServicesBSN();
        $param = [
            'position_x' => $x,
            'position_y' => $y,
        ];

        if ($rad!=null) {
            $param['radius'] = $rad;
        }
        if ($type!=null) {
            $param['service_type'] = $type;
        }

        $result = $bsn->getList($param);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getListRPTAction($rad, $x, $y, $type = null) {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new ServicesBSN();
        $param = [
            'position_x' => $x,
            'position_y' => $y,
        ];

        if ($rad!=null) {
            $param['radius'] = $rad;
        } else {
            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn::MISSING_PARAMETERS]);
        }
        if ($type!=null) {
            $param['service_type'] = $type;
        }

        $result = $bsn->getList($param);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getListRTPAction($rad, $type, $x, $y) {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new ServicesBSN();
        $param = [
            'position_x' => $x,
            'position_y' => $y,
        ];

        if ($rad!=null) {
            $param['radius'] = $rad;
        } else {
            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn::MISSING_PARAMETERS]);
        }
        if ($type!=null) {
            $param['service_type'] = $type;
        } else {
            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn::MISSING_PARAMETERS]);
        }

        $result = $bsn->getList($param);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getListTPRAction($type, $x, $y, $rad = null) {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new ServicesBSN();
        $param = [
            'position_x' => $x,
            'position_y' => $y,
        ];

        if ($rad!=null) {
            $param['radius'] = $rad;
        }

        if ($type!=null) {
            $param['service_type'] = $type;
        } else {
            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn::MISSING_PARAMETERS]);
        }

        $result = $bsn->getList($param);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getListTRPAction($type, $rad, $x, $y) {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new ServicesBSN();
        $param = [
            'position_x' => $x,
            'position_y' => $y,
        ];

        if ($rad!=null) {
            $param['radius'] = $rad;
        } else {
            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn::MISSING_PARAMETERS]);
        }
        if ($type!=null) {
            $param['service_type'] = $type;
        } else {
            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn::MISSING_PARAMETERS]);
        }

        $result = $bsn->getList($param);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getAction($id) {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new ServicesBSN();

        if (empty($id)) {
            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn::MISSING_PARAMETERS]);
        }

        $result = $bsn->get(['service_id' => $id]);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
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

        $result = $bsn->deleteService($post);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => ['status' => $result]];
    }
}