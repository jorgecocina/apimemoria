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

    public function getevaluationsquantitydayAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'report';

        $result = $bsn->reportsQuantityDay($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getevaluationsquantitymonthAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'report';

        $result = $bsn->reportsQuantityMonth($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getevaluationsquantityyearAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'report';

        $result = $bsn->reportsQuantityYear($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getevaluationstimedayAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'report';

        $result = $bsn->reportsTimeDay($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getevaluationstimemonthAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'report';

        $result = $bsn->reportsTimeMonth($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getevaluationstimeyearAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'report';

        $result = $bsn->reportsTimeYear($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getnewsquantitydayAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'new';

        $result = $bsn->reportsQuantityDay($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getnewsquantitymonthAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'new';

        $result = $bsn->reportsQuantityMonth($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getnewsquantityyearAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'new';

        $result = $bsn->reportsQuantityYear($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getnewstimedayAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'new';

        $result = $bsn->reportsTimeDay($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getnewstimemonthAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'new';

        $result = $bsn->reportsTimeMonth($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getnewstimeyearAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'new';

        $result = $bsn->reportsTimeYear($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getevaluationmovementsdayAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'report';

        $result = $bsn->movementsPerActionDay($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getevaluationmovementsmonthAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'report';

        $result = $bsn->movementsPerActionMonth($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getevaluationmovementsyearAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'report';

        $result = $bsn->movementsPerActionMonth($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getnewsmovementsdayAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'new';

        $result = $bsn->movementsPerActionDay($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getnewsmovementsmonthAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'new';

        $result = $bsn->movementsPerActionMonth($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getnewsmovementsyearAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();
        $params['route'] = 'new';

        $result = $bsn->movementsPerActionMonth($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getservicerankingAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new AnalyticsBSN();

        $params = $this->request->get();

        $result = $bsn->getServicesRanking($params);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

}