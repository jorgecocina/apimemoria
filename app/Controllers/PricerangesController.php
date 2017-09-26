<?php

namespace App\Controllers;
use App\Business\PricerangesBSN;
use App\Models\PriceRanges;

/**
 * Operaciones con service_types: CRUD
 */
class PricerangesController extends BaseController
{
    var $response;

    public function getAction($id) {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new PricerangesBSN();

        $result = $bsn->get(['price_range_id' => $id]);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];
    }

    public function getListAction($id = null) {
        if ($this->session->get('error')) {
            return $this->session->get('error_response');
        }

        $bsn = new PricerangesBSN();
        if (is_null($id)) {
            $param = null;
        } else {
            $param = [
                'service_type_id' => $id
            ];
        }
        $result = $bsn->getList($param);

        if (!$result)
        {

            return array('status' => STATUS_ERROR, 'data' => ['description' => $bsn->error]);

        }

        return ['status' => STATUS_OK, 'data' => $result];

    }

}