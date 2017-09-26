<?php

namespace App\Business;

use App\Models\PriceRanges;
use Phalcon\Filter;

class PricerangesBSN extends BaseBSN
{

    public function get($param) {

        if (!isset($param['price_range_id'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (intval($param['price_range_id']) == 0) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        $price = PriceRanges::findFirstById($param['price_range_id']);

        if (!$price) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }


        return $price->toArray();

    }

    public function getList($param) {

        $query = [
            'conditions' => '1=1',
            'bind' => [

            ]
        ];

        if (isset($param['service_type_id'])) {
            $query['conditions'] = $query['conditions'] . ' and service_type_id = :svctp: ';
            $query['bind']['svctp'] = $param['service_type_id'];
        }

        $prices = PriceRanges::find($query);

        if ($prices->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }


        return $prices->toArray();

    }

}