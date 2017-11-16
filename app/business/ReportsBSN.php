<?php

namespace App\Business;

use App\Models\Services;
use App\Models\Reports;
use App\Models\PriceRanges;
use App\Helpers\Carbon\Carbon;

class ReportsBSN extends BaseBSN
{
    const NEW_SERVICE = 1;
    const UPDATE_SERVICE = 2;

    public function newService($param) {
        if (
                !isset($param['user_id'])
                || !isset($param['service_type_id'])
                || !isset($param['x_position'])
                || !isset($param['y_position'])
                || !isset($param['name'])
            ) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        $service = new Services();
        $report = new Reports();

        $report->x_position = $param['x_position'];
        $report->y_position = $param['y_position'];
        $report->report_types_id = self::NEW_SERVICE;
        $report->user_id = $param['user_id'];

        $service->x_position = $param['x_position'];
        $service->y_position = $param['y_position'];
        $service->service_types_id = $param['service_type_id'];
        $service->name = $param['name'];

        if (isset($param['price'])) {
            $priceRange = PriceRanges::findFirst(
                [
                    'conditions' => 'id = :price: and service_type_id = :service_type:',
                    'bind' => [
                        'price' => $param['price'],
                        'service_type' => $param['service_type_id']
                    ]
                ]);
            if (!$priceRange) {
                $this->error = self::ERROR_INVALID_PARAMETERS;
                return false;
            }
            $report->price = $param['price'];
            $service->price = $param['price'];
        }

        if (isset($param['quality'])) {
            $report->quality = $param['quality'];
            $service->quality = $param['quality'];
        }

        if (isset($param['active']) && $param['active']) {
            $report->active = 1;
            $service->confiability = 100;
        } else {
            $report->active = 0;
            $service->confiability = 0;
        }

        $this->db->begin();

        if (!$service->save()) {
            $this->db->rollback();
            $msg = '';
            foreach ($service->getMessages() as $message) {
                $msg = $msg . PHP_EOL . ' ' . $message;
            }
            $this->error[] = self::ERROR_DATABASE . $msg;
            return false;
        }

        $report->services_id = $service->id;
        if (!$report->save()) {
            $this->db->rollback();
            $msg = '';
            foreach ($report->getMessages() as $message) {
                $msg = $msg . PHP_EOL . ' ' . $message;
            }
            $this->error[] = self::ERROR_DATABASE . $msg;
            return false;
        }
        $this->db->commit();
        return $service->id;

    }

    public function newReport($param) {

        if (
            !isset($param['user_id'])
            || !isset($param['services_id'])
            || (
                !isset($param['x_position'])
                && !isset($param['price'])
                && !isset($param['quality'])
                && !isset($param['active'])
                && !isset($param['y_position'])
            )
        ) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        $service = Services::findFirstById($param['services_id']);
        if (!$service) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $conditions = [];
        if (isset($param['x_position'])) {
            $conditions[] = 'x_position is not null';
        }
        if (isset($param['price'])) {
            $conditions[] = 'price is not null';
        }
        if (isset($param['quality'])) {
            $conditions[] = 'quality is not null';
        }
        if (isset($param['active'])) {
            $conditions[] = 'active is not null';
        }
        if (isset($param['y_position'])) {
            $conditions[] = 'y_position is not null';
        }

        $search = [
            'conditions' => 'services_id = :service_id: and created_at >= :datetime: and user_id = :user_id: and (' . implode(' or ', $conditions) . ')',
            'bind' => [
                'datetime' => Carbon::now()->subSeconds($this->di->get('config')->votation_delay)->toDateTimeString(),
                'user_id' => $param['user_id'],
                'service_id' => $param['services_id']
            ]
        ];

        $search = Reports::find($search);
        if ($search->count() > 0) {
            $this->error[] = self::VOTE_ATEMP_ERROR;
            return false;
        }

        $report = new Reports();
        $report->user_id = $param['user_id'];
        $report->services_id = $service->id;
        $report->report_types_id = self::UPDATE_SERVICE;

        if (isset($param['x_position'])){
            $report->x_position = $param['x_position'];
        }

        if (isset($param['y_position'])){
            $report->y_position = $param['y_position'];
        }

        if (isset($param['price'])) {
            $priceRange = PriceRanges::findFirst(
                [
                    'conditions' => 'id = :price: and service_type_id = :service_type:',
                    'bind' => [
                        'price' => $param['price'],
                        'service_type' => $service->service_types_id
                    ]
                ]);
            if (!$priceRange) {
                $this->error = self::ERROR_INVALID_PARAMETERS;
                return false;
            }
            $report->price = $param['price'];
        }

        if (isset($param['quality'])){
            $report->quality = $param['quality']>5?5:$param['quality'];
        }

        if (isset($param['active'])){
            $report->active = (intval($param['active']) != 0 || $param['active'] == 'true');
        }

        $this->db->begin();
        if (!$report->save()) {
            $this->db->rollback();
            $msg = '';
            foreach ($report->getMessages() as $message) {
                $msg = $msg . PHP_EOL . ' ' . $message;
            }
            $this->error[] = self::ERROR_DATABASE . $msg;
            return false;
        }

        $serviceBsn = new ServicesBSN();
        if (!$serviceBsn->updateFromDB(['services_id' => $report->services_id])) {
            $this->db->rollback();
            $this->error = $serviceBsn->error;
            return false;
        }

        $this->db->commit();
        return $report->id;

    }

    public function deleteService($param) {

        if (!isset($param['id']) || !isset($param['user_id'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        $service = Services::findFirstById($param['id']);
        $reports = Reports::find([
            'conditions' => 'services_id = :id:',
            'bind' => [
                'id' => $param['id']
            ]
        ]);

        if (!$service || $reports->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        if ($reports->count() > 1) {
            $this->error[] = self::CNT_DEL_SERVICE_MLTPL_RPRTS;
            return false;
        }

        if ($reports[0]->user_id != $param['user_id']) {
            $this->error[] = self::CNT_DEL_SERVICE_NOT_OWNER;
            return false;
        }

        $now = Carbon::now ();
        $creation = Carbon::createFromFormat('Y-m-d H:i:s', $service->created_at);


        if ($creation->diffInSeconds($now) > $this->di->get('config')->max_edition_time) {
            $this->error[] = self::CNT_DEL_SERVICE_OUT_TIME;
            return false;
        }

        $this->db->begin();

        if (!$reports->delete()) {
            $msg = '';
            foreach ($reports->getMessages() as $message) {
                $msg = $msg . PHP_EOL . ' ' . $message;
            }
            $this->error[] = self::ERROR_DATABASE . $msg;
            $this->db->rollback();
            return false;
        }

        if (!$service->delete()) {
            $msg = '';
            foreach ($service->getMessages() as $message) {
                $msg = $msg . PHP_EOL . ' ' . $message;
            }
            $this->error[] = self::ERROR_DATABASE . $msg;
            $this->db->rollback();
            return false;
        }
        $this->db->commit();

        return true;

    }

    public function deleteReport($param) {

        if (!isset($param['id']) || !isset($param['user_id'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        $report = Reports::findFirstById($param['id']);


        if (!$report) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        if ($report->user_id != $param['user_id']) {
            $this->error[] = self::CNT_DEL_REPORT_NOT_OWNER;
            return false;
        }

        $now = Carbon::now ();
        $creation = Carbon::createFromFormat('Y-m-d H:i:s', $report->created_at);


        if ($creation->diffInSeconds($now) > $this->di->get('config')->max_edition_time) {
            $this->error[] = self::CNT_DEL_SERVICE_OUT_TIME;
            return false;
        }

        $this->db->begin();

        if (!$report->delete()) {
            $msg = '';
            foreach ($report->getMessages() as $message) {
                $msg = $msg . PHP_EOL . ' ' . $message;
            }
            $this->error[] = self::ERROR_DATABASE . $msg;
            $this->db->rollback();
            return false;
        }


        $serviceBsn = new ServicesBSN();
        if (!$serviceBsn->updateFromDB(['services_id' => $report->services_id])) {
            $this->db->rollback();
            $this->error = $serviceBsn->error;
            return false;
        }

        $this->db->commit();

        return true;

    }

}