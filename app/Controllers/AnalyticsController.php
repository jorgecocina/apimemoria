<?php

namespace App\Controllers;

use App\Business\AnalyticsBSN;

class AnalyticsController extends BaseController
{
    var $response;

    public function getvisitsdayAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();

        $result = $bsn->visitsDay($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getvisitsmonthAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();

        $result = $bsn->visitsMoth($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getvisitsyearAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();

        $result = $bsn->visitsYear($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

}