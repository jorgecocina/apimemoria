<?php

namespace App\Business;
use App\Models\PriceRanges;
use App\Models\Services;
use App\Models\Reports;

class ServicesBSN extends BaseBSN
{
    const DEFAULT_DEGREES = 0.1;

    public function updateFromDB($param) {
        if (!$param['services_id']) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }
        $service = Services::findFirstById($param['services_id']);
        $reports = Reports::find([
            'services_id = :id:',
            'bind'  => [
                'id' => $param['services_id']
            ]
        ]);
        $total = $reports->count();
        if (!$service || $total == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $active = 0;
        $activeCount = 0;
        $quality = 0;
        $qualityCount = 0;
        $price = [];
        foreach ($reports as $rprt) {
            if (!empty($rprt->active)) {
                $active += $rprt->active;
                $activeCount += 1;
            }
            if (!empty($rprt->quality)) {
                $quality += $rprt->quality;
                $qualityCount += 1;
            }
            if (!empty($rprt->price)) {
                if (isset($price[$rprt->price])) {
                    $price[$rprt->price]+=1;
                } else {
                    $price[$rprt->price] = 1;
                }
            }
        }
        if ($total > 1) {
            $service->confirmed = 1;
        }

        if ($activeCount > 0) {
            $service->confiability = ($active * 100)/$activeCount;
        }
        if ($qualityCount > 0) {
            $service->quality = $quality/$qualityCount;
        }
        if (count($price) > 0) {
            $temp = 0;
            foreach ($price as $id => $cant) {
                if ($cant > $temp) {
                    $temp = $cant;
                    $service->price = $id;
                }
            }
        }

        if (!$service->save()) {
            $msg = '';
            foreach ($service->getMessages() as $message) {
                $msg = $msg . PHP_EOL . ' ' . $message;
            }
            $this->error[] = self::ERROR_DATABASE . $msg;
            return false;
        }

        return true;

    }

    public function getList($param) {

        if (
                !isset($param['position_x'])
                || !isset($param['position_y'])
            ) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
                $param['position_x'] != floatval($param['position_x'])
                || $param['position_y'] != floatval($param['position_y'])
                || (isset($param['radius']) && $param['radius'] != floatval($param['radius']))
                || (isset($param['service_type']) && $param['service_type'] != intval($param['service_type']))
            ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (!isset($param['radius'])) {
            $param['radius'] = self::DEFAULT_DEGREES;
        } else {
            $param['radius'] = $param['radius']/111.2;
        }

        $query = [
            'conditions' => 'pow(x_position - :xpos:,2) + pow(y_position - :ypos:,2) <= pow(:rad:,2) ',
            'bind'  => [
                'xpos' => $param['position_x'],
                'ypos' => $param['position_y'],
                'rad' => $param['radius']
            ]
        ];


        if (isset($param['service_type'])) {
            $query['conditions'] = $query['conditions'] . ' and service_types_id = :type:';
            $query['bind']['type'] = $param['service_type'];
        }

        if (isset($param['name'])) {
            $query['conditions'] = $query['conditions'] . " and name like :name:";
            $query['bind']['name'] = '%'.$param['name'].'%';
        }

        $result = Services::find($query);

        if (!$result->count()) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = $result->toArray();
        foreach ($result as $k => $v) {
            if (intval($v['price']) > 0) {
                $price = PriceRanges::findFirstById($v['price']);

                if (!$price) {
                    $this->error[] = self::ERROR_NO_RECORDS_FOUND;
                    return false;
                }
                $result[$k]['price'] = $price->price;
            }
        }

        return $result;

    }

    public function get($param) {
        if (!isset($param['service_id'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if ($param['service_id'] != floatval($param['service_id'])) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        $result = Services::findFirstById($param['service_id']);
        if (!$result) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }
        $result = $result->toArray();
        if (intval($result['price']) > 0) {
            $price = PriceRanges::findFirstById($result['price']);

            if (!$price) {
                $this->error[] = self::ERROR_NO_RECORDS_FOUND;
                return false;
            }
            $result['price'] = $price->price;
        }

        return $result;
    }

}