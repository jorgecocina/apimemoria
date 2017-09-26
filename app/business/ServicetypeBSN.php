<?php

namespace App\Business;

use App\Models\ServiceTypes;
use Phalcon\Filter;

class ServicetypeBSN extends BaseBSN
{

    public function getServiceTypes($param = null)
    {

        $bind =
            [
                'conditions' => '1=1',
                'bind' => []
            ];

        if (isset($param['id']) or isset($param['name']))
        {

            if (isset($param['id']) and !empty($param['id']))
            {

                $bind['conditions'] = $bind['conditions'] . ' and id = :id:';
                $bind['bind']['id'] = $param['id'];

            }

            if (isset($param['name']) and !empty($param['mane']))
            {

                $bind['conditions'] = $bind['conditions'] . " and name like '%:name:%'";
                $bind['bind']['name'] = $param['name'];

            }
        }

        $arr = ServiceTypes::find($bind);

        if ($arr == false) {

            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;

        }

        return $arr->toArray();

    }

    public function createServiceType($param) {

        if (!isset($param['name'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        $filter = new Filter();
        $serviceType = new ServiceTypes();
        $serviceType->name = $filter->sanitize($param['name'],"string");

        if (!$serviceType->save()) {
            $err = self::ERROR_DATABASE;
            foreach ($serviceType->getMessages() as $message) {

                $err['message'] =$err['message'] . ' * ' . $message->getMessage();
            }
            $this->error[] = $err;

            return false;
        }

        return $serviceType->id;

    }

    public function editServiceType($param) {
        if (!isset($param['name']) || !isset($param['id'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        $filter = new Filter();
        $serviceType = ServiceTypes::findFirstById($param['id']);
        if (!$serviceType) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $serviceType->name = $filter->sanitize($param['name'],"string");
        if (!$serviceType->save()) {
            $err = self::ERROR_DATABASE;
            foreach ($serviceType->getMessages() as $message) {

                $err['message'] =$err['message'] . ' * ' . $message->getMessage();
            }
            $this->error[] = $err;

            return false;
        }

        return true;
    }

    public function deleteServiceType($param) {
        if (isset($param['id'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        $serviceType = ServiceTypes::findFirstById($param['id']);
        if (!$serviceType) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        if (!$serviceType->delete()) {
            $err = self::ERROR_DATABASE;
            foreach ($serviceType->getMessages() as $message) {

                $err['message'] =$err['message'] . ' * ' . $message->getMessage();
            }
            $this->error[] = $err;

            return false;
        }

        return true;
    }

}
