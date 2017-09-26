<?php

namespace App\Controllers;
use App\Business\ServicetypeBSN;
use App\Models\ServiceTypes;

/**
 * Operaciones con service_types: CRUD
 */
class ServicetypesController extends BaseController
{
    var $response;

    public function addAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $post = $this->request->getPost();
        $bsn = new ServicetypeBSN();

        $result = $bsn->createServiceType($post);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => ['id' => $result]];
    }

    public function  getListAction()
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new ServicetypeBSN();

        $result = $bsn->getServiceTypes();

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];

    }

    public function  getAction($id)
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new ServicetypeBSN();

        $result = $bsn->getServiceTypes(['id' => $id]);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result[0]];

    }

    public function updateAction($id)
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $post = $this->request->getJsonRawBody(true);

        if (empty($post)) {
            return array('status' => STATUS_ERROR, 'data' => ['description' => [self::BAD_JSON_REQUEST]]);
        }
        $post['id'] = $id;
        $bsn = new ServicetypeBSN();

        $result = $bsn->editServiceType($post);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => ['status' => $result]];

    }

    public function deleteAction($id)
    {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }
        $post['id'] = $id;
        $bsn = new ServicetypeBSN();

        $result = $bsn->deleteServiceType($post);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => ['status' => $result]];
    }

}