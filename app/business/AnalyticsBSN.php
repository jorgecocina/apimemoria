<?php

namespace App\Business;

use App\Models\Movements;
use App\Models\Services;
use App\Models\UserTracks;

class AnalyticsBSN  extends BaseBSN
{
    private $conditions = null;

    public function visitsDay($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if (isset($param['exclude']) && !empty($param['exclude'])) {
            $exclude = explode(',', $param['exclude']);
            $cont = 0;
            foreach ($exclude as $ex) {
                $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri not like :ex'.$cont.':';
                $this->conditions['bind']['ex'.$cont] = '%'.$ex.'%';
                $cont+=1;
            }
        }

        if (isset($param['grouped']) && $param['grouped'] == 'users') {
            $columns = [
                'date',
                "user_id",
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                'count(*) as count'
            ];
            $group = ['date', 'user_id'];
            $order = 1;
        } else if (isset($param['grouped']) && $param['grouped'] == 'routes') {
            $columns = [
                'date',
                "concat(method, ': ', uri) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['date', 'route'];
            $order = 2;
        } else {
            $columns = [
                'date',
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['date'];
            $order = 3;
        }

        $query = Movements::query()
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {
            switch ($order) {
                case 1:
                    $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';
                    $result[$row->date][$user_id]['uri'] = explode(',', $row->route);
                    $result[$row->date][$user_id]['count'] = $row->count;
                    break;
                case 2:
                    $result[$row->date][$row->route]['user_id'] = explode(',', $row->user_id);
                    $result[$row->date][$row->route]['count'] = $row->count;
                    break;
                case 3:
                    $result[$row->date]['user_id'] = explode(',', $row->user_id);
                    $result[$row->date]['uri'] = explode(',', $row->route);
                    $result[$row->date]['count'] = $row->count;
                    break;
            }
        }

        return $result;

    }

    public function visitsMoth($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if (isset($param['exclude']) && !empty($param['exclude'])) {
            $exclude = explode(',', $param['exclude']);
            $cont = 0;
            foreach ($exclude as $ex) {
                $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri not like :ex'.$cont.':';
                $this->conditions['bind']['ex'.$cont] = '%'.$ex.'%';
                $cont+=1;
            }
        }

        if (isset($param['grouped']) && $param['grouped'] == 'users') {
            $columns = [
                'concat(year(date), \'-\', month(date)) as yearmonth',
                "user_id",
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                'count(*) as count'
            ];
            $group = ['yearmonth', 'user_id'];
            $order = 1;
        } else if (isset($param['grouped']) && $param['grouped'] == 'routes') {
            $columns = [
                'concat(year(date), \'-\', month(date)) as yearmonth',
                "concat(method, ': ', uri) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['yearmonth', 'route'];
            $order = 2;
        } else {
            $columns = [
                'concat(year(date), \'-\', month(date)) as yearmonth',
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['yearmonth'];
            $order = 3;
        }

        $query = Movements::query()
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {
            switch ($order) {
                case 1:
                    $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';
                    $result[$row->yearmonth][$user_id]['uri'] = explode(',', $row->route);
                    $result[$row->yearmonth][$user_id]['count'] = $row->count;
                    break;
                case 2:
                    $result[$row->yearmonth][$row->route]['user_id'] = explode(',', $row->user_id);
                    $result[$row->yearmonth][$row->route]['count'] = $row->count;
                    break;
                case 3:
                    $result[$row->yearmonth]['user_id'] = explode(',', $row->user_id);
                    $result[$row->yearmonth]['uri'] = explode(',', $row->route);
                    $result[$row->yearmonth]['count'] = $row->count;
                    break;
            }
        }

        return $result;

    }

    public function visitsYear($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if (isset($param['exclude']) && !empty($param['exclude'])) {
            $exclude = explode(',', $param['exclude']);
            $cont = 0;
            foreach ($exclude as $ex) {
                $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri not like :ex'.$cont.':';
                $this->conditions['bind']['ex'.$cont] = '%'.$ex.'%';
                $cont+=1;
            }
        }

        if (isset($param['grouped']) && $param['grouped'] == 'users') {
            $columns = [
                'year(date) as yearmonth',
                "user_id",
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                'count(*) as count'
            ];
            $group = ['yearmonth', 'user_id'];
            $order = 1;
        } else if (isset($param['grouped']) && $param['grouped'] == 'routes') {
            $columns = [
                'year(date) as yearmonth',
                "concat(method, ': ', uri) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['yearmonth', 'route'];
            $order = 2;
        } else {
            $columns = [
                'year(date) as yearmonth',
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['yearmonth'];
            $order = 3;
        }

        $query = Movements::query()
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {
            switch ($order) {
                case 1:
                    $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';
                    $result[$row->yearmonth][$user_id]['uri'] = explode(',', $row->route);
                    $result[$row->yearmonth][$user_id]['count'] = $row->count;
                    break;
                case 2:
                    $result[$row->yearmonth][$row->route]['user_id'] = explode(',', $row->user_id);
                    $result[$row->yearmonth][$row->route]['count'] = $row->count;
                    break;
                case 3:
                    $result[$row->yearmonth]['user_id'] = explode(',', $row->user_id);
                    $result[$row->yearmonth]['uri'] = explode(',', $row->route);
                    $result[$row->yearmonth]['count'] = $row->count;
                    break;
            }
        }

        return $result;

    }

    public function reportsQuantityDay($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if ($param['route'] == 'new') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/addnew';
        } elseif ($param['route'] == 'report') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/add';
        }

        $this->conditions['conditions'] = $this->conditions['conditions'] . ' and method = :method:';
        $this->conditions['bind']['method'] = 'post';

        if (isset($param['grouped']) && $param['grouped'] == 'users') {
            $columns = [
                'date',
                "user_id",
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                'count(*) as count'
            ];
            $group = ['date', 'user_id'];
            $order = 1;
        } else if (isset($param['grouped']) && $param['grouped'] == 'routes') {
            $columns = [
                'date',
                "concat(method, ': ', uri) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['date', 'route'];
            $order = 2;
        } else {
            $columns = [
                'date',
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['date'];
            $order = 3;
        }

        $query = Movements::query()
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {
            switch ($order) {
                case 1:
                    $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';
                    $result[$row->date][$user_id]['uri'] = explode(',', $row->route);
                    $result[$row->date][$user_id]['count'] = $row->count;
                    break;
                case 2:
                    $result[$row->date][$row->route]['user_id'] = explode(',', $row->user_id);
                    $result[$row->date][$row->route]['count'] = $row->count;
                    break;
                case 3:
                    $result[$row->date]['user_id'] = explode(',', $row->user_id);
                    $result[$row->date]['uri'] = explode(',', $row->route);
                    $result[$row->date]['count'] = $row->count;
                    break;
            }
        }

        return $result;

    }

    public function reportsQuantityMonth($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if ($param['route'] == 'new') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/addnew';
        } elseif ($param['route'] == 'report') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/add';
        }

        $this->conditions['conditions'] = $this->conditions['conditions'] . ' and method = :method:';
        $this->conditions['bind']['method'] = 'post';

        if (isset($param['grouped']) && $param['grouped'] == 'users') {
            $columns = [
                'concat(year(date), \'-\', month(date)) as yearmonth',
                "user_id",
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                'count(*) as count'
            ];
            $group = ['yearmonth', 'user_id'];
            $order = 1;
        } else if (isset($param['grouped']) && $param['grouped'] == 'routes') {
            $columns = [
                'concat(year(date), \'-\', month(date)) as yearmonth',
                "concat(method, ': ', uri) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['yearmonth', 'route'];
            $order = 2;
        } else {
            $columns = [
                'concat(year(date), \'-\', month(date)) as yearmonth',
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['yearmonth'];
            $order = 3;
        }

        $query = Movements::query()
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {
            switch ($order) {
                case 1:
                    $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';
                    $result[$row->yearmonth][$user_id]['uri'] = explode(',', $row->route);
                    $result[$row->yearmonth][$user_id]['count'] = $row->count;
                    break;
                case 2:
                    $result[$row->yearmonth][$row->route]['user_id'] = explode(',', $row->user_id);
                    $result[$row->yearmonth][$row->route]['count'] = $row->count;
                    break;
                case 3:
                    $result[$row->yearmonth]['user_id'] = explode(',', $row->user_id);
                    $result[$row->yearmonth]['uri'] = explode(',', $row->route);
                    $result[$row->yearmonth]['count'] = $row->count;
                    break;
            }
        }

        return $result;

    }

    public function reportsQuantityYear($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if ($param['route'] == 'new') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/addnew';
        } elseif ($param['route'] == 'report') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/add';
        }

        $this->conditions['conditions'] = $this->conditions['conditions'] . ' and method = :method:';
        $this->conditions['bind']['method'] = 'post';

        if (isset($param['grouped']) && $param['grouped'] == 'users') {
            $columns = [
                'year(date) as yearmonth',
                "user_id",
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                'count(*) as count'
            ];
            $group = ['yearmonth', 'user_id'];
            $order = 1;
        } else if (isset($param['grouped']) && $param['grouped'] == 'routes') {
            $columns = [
                'year(date) as yearmonth',
                "concat(method, ': ', uri) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['yearmonth', 'route'];
            $order = 2;
        } else {
            $columns = [
                'year(date) as yearmonth',
                "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
                "group_concat(distinct(if(user_id is null, '0',user_id))) as user_id",
                'count(*) as count'
            ];
            $group = ['yearmonth'];
            $order = 3;
        }

        $query = Movements::query()
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {
            switch ($order) {
                case 1:
                    $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';
                    $result[$row->yearmonth][$user_id]['uri'] = explode(',', $row->route);
                    $result[$row->yearmonth][$user_id]['count'] = $row->count;
                    break;
                case 2:
                    $result[$row->yearmonth][$row->route]['user_id'] = explode(',', $row->user_id);
                    $result[$row->yearmonth][$row->route]['count'] = $row->count;
                    break;
                case 3:
                    $result[$row->yearmonth]['user_id'] = explode(',', $row->user_id);
                    $result[$row->yearmonth]['uri'] = explode(',', $row->route);
                    $result[$row->yearmonth]['count'] = $row->count;
                    break;
            }
        }

        return $result;

    }

    public function reportsTimeDay($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if ($param['route'] == 'new') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/addnew';
        } elseif ($param['route'] == 'report') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/add';
        }

        $this->conditions['conditions'] = $this->conditions['conditions'] . ' and method = :method:';
        $this->conditions['bind']['method'] = 'post';

        $columns = [
            'date',
            'user_id',
            'status',
            "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
            'min(timestamp) as inicio',
            'max(timestamp) as fin',
            'max(timestamp) - min(timestamp) as seconds',
            'sum(if(type = 1, 1, 0)) as clicks',
            'sum(if(type = 2, 1, 0)) as hovers',
            'sum(if(type = 3, 1, 0)) as map_clicks'
        ];
        $group = ['movement_id'];

        $query = Movements::query()
            ->innerJoin('App\Models\UserTracks', 'user_tracks.movement_id = App\Models\Movements.id', 'user_tracks')
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {
            if (!isset($result[$row->date])) {
                $result[$row->date] = [];
            }
            $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';

            $result[$row->date][] = [
                'user_id' => $user_id,
                'route' => $row->route,
                'status' => $row->status,
                'seconds' => $row->seconds,
                'clicks' => $row->clicks,
                'hovers' => $row->hovers,
                'map_clicks' => $row->map_clicks,
            ];
        }

        return $result;

    }

    public function reportsTimeMonth($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if ($param['route'] == 'new') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/addnew';
        } elseif ($param['route'] == 'report') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/add';
        }

        $this->conditions['conditions'] = $this->conditions['conditions'] . ' and method = :method:';
        $this->conditions['bind']['method'] = 'post';

        $columns = [
            'concat(year(date), \'-\', month(date)) as yearmonth',
            'user_id',
            'status',
            "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
            'min(timestamp) as inicio',
            'max(timestamp) as fin',
            'max(timestamp) - min(timestamp) as seconds',
            'sum(if(type = 1, 1, 0)) as clicks',
            'sum(if(type = 2, 1, 0)) as hovers',
            'sum(if(type = 3, 1, 0)) as map_clicks'
        ];
        $group = ['movement_id'];

        $query = Movements::query()
            ->innerJoin('App\Models\UserTracks', 'user_tracks.movement_id = App\Models\Movements.id', 'user_tracks')
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {
            if (!isset($result[$row->yearmonth])) {
                $result[$row->yearmonth] = [];
            }
            $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';

            $result[$row->yearmonth][] = [
                'user_id' => $user_id,
                'route' => $row->route,
                'status' => $row->status,
                'seconds' => $row->seconds,
                'clicks' => $row->clicks,
                'hovers' => $row->hovers,
                'map_clicks' => $row->map_clicks,
            ];
        }

        return $result;

    }

    public function reportsTimeYear($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if ($param['route'] == 'new') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/addnew';
        } elseif ($param['route'] == 'report') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/add';
        }

        $this->conditions['conditions'] = $this->conditions['conditions'] . ' and method = :method:';
        $this->conditions['bind']['method'] = 'post';

        $columns = [
            'year(date) as year',
            'user_id',
            'status',
            "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
            'min(timestamp) as inicio',
            'max(timestamp) as fin',
            'max(timestamp) - min(timestamp) as seconds',
            'sum(if(type = 1, 1, 0)) as clicks',
            'sum(if(type = 2, 1, 0)) as hovers',
            'sum(if(type = 3, 1, 0)) as map_clicks'
        ];
        $group = ['movement_id'];

        $query = Movements::query()
            ->innerJoin('App\Models\UserTracks', 'user_tracks.movement_id = App\Models\Movements.id', 'user_tracks')
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {
            if (!isset($result[$row->year])) {
                $result[$row->year] = [];
            }
            $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';

            $result[$row->year][] = [
                'user_id' => $user_id,
                'route' => $row->route,
                'status' => $row->status,
                'seconds' => $row->seconds,
                'clicks' => $row->clicks,
                'hovers' => $row->hovers,
                'map_clicks' => $row->map_clicks,
            ];
        }

        return $result;

    }

    public function movementsPerActionDay($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if ($param['route'] == 'new') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/addnew';
        } elseif ($param['route'] == 'report') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/add';
        }

        $this->conditions['conditions'] = $this->conditions['conditions'] . ' and method = :method:';
        $this->conditions['bind']['method'] = 'post';

        $columns = [
            'date',
            'user_id',
            'status',
            "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
            'max(timestamp) - min(timestamp) as seconds',
            'sum(if(type = 1, 1, 0)) as clicks',
            'sum(if(type = 2, 1, 0)) as hovers',
            'sum(if(type = 3, 1, 0)) as map_clicks',
            'count(distinct App\Models\Movements.id) as count'
        ];
        if (isset($param['group']) && $param['group'] = 'user') {
            $group = ['date','user_id','uri'];
        } else {
            $group = ['date', 'uri'];
        }
        $query = Movements::query()
            ->innerJoin('App\Models\UserTracks', 'user_tracks.movement_id = App\Models\Movements.id', 'user_tracks')
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {

            if (isset($param['group']) && $param['group'] = 'user') {
                $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';
                if (!isset($result[$row->date][$user_id])) {
                    $result[$row->date][$user_id] = [];
                }
                $result[$row->date][$user_id][] = [
                    'route' => $row->route,
                    'seconds' => $row->seconds/$row->count,
                    'clicks' => $row->clicks/$row->count,
                    'hovers' => $row->hovers/$row->count,
                    'map_clicks' => $row->map_clicks/$row->count,
                ];
            } else {
                if (!isset($result[$row->date])) {
                    $result[$row->date] = [];
                }
                $result[$row->date][] = [
                    'route' => $row->route,
                    'seconds' => $row->seconds/$row->count,
                    'clicks' => $row->clicks/$row->count,
                    'hovers' => $row->hovers/$row->count,
                    'map_clicks' => $row->map_clicks/$row->count,
                ];
            }
        }

        return $result;

    }

    public function movementsPerActionMonth($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if ($param['route'] == 'new') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/addnew';
        } elseif ($param['route'] == 'report') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/add';
        }

        $this->conditions['conditions'] = $this->conditions['conditions'] . ' and method = :method:';
        $this->conditions['bind']['method'] = 'post';

        $columns = [
            'concat(year(date), \'-\', month(date)) as yearmonth',
            'user_id',
            'status',
            "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
            'max(timestamp) - min(timestamp) as seconds',
            'sum(if(type = 1, 1, 0)) as clicks',
            'sum(if(type = 2, 1, 0)) as hovers',
            'sum(if(type = 3, 1, 0)) as map_clicks',
            'count(distinct App\Models\Movements.id) as count'
        ];
        if (isset($param['group']) && $param['group'] = 'user') {
            $group = ['yearmonth','user_id','uri'];
        } else {
            $group = ['yearmonth', 'uri'];
        }
        $query = Movements::query()
            ->innerJoin('App\Models\UserTracks', 'user_tracks.movement_id = App\Models\Movements.id', 'user_tracks')
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {

            if (isset($param['group']) && $param['group'] = 'user') {
                $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';
                if (!isset($result[$row->yearmonth][$user_id])) {
                    $result[$row->yearmonth][$user_id] = [];
                }
                $result[$row->yearmonth][$user_id][] = [
                    'route' => $row->route,
                    'seconds' => $row->seconds/$row->count,
                    'clicks' => $row->clicks/$row->count,
                    'hovers' => $row->hovers/$row->count,
                    'map_clicks' => $row->map_clicks/$row->count,
                ];
            } else {
                if (!isset($result[$row->yearmonth])) {
                    $result[$row->yearmonth] = [];
                }
                $result[$row->yearmonth][] = [
                    'route' => $row->route,
                    'seconds' => $row->seconds/$row->count,
                    'clicks' => $row->clicks/$row->count,
                    'hovers' => $row->hovers/$row->count,
                    'map_clicks' => $row->map_clicks/$row->count,
                ];
            }
        }

        return $result;

    }

    public function movementsPerActionYear($param) {

        $this->conditions = [
            'conditions' => '',
            'bind' => [

            ]
        ];

        if (!isset($param['dates'])) {
            $this->error[] = self::MISSING_PARAMETERS;
            return false;
        }

        if (
            !preg_match('/^\d{4}-\d{1,2}-\d{1,2}(to\d{4}-\d{1,2}-\d{1,2}|(,\d{4}-\d{1,2}-\d{1,2})+){0,1}$/', $param['dates']) ||
            (isset($param['status']) && !preg_match('/^\d+(,\d+)*$/', $param['status']))
        ) {
            $this->error[] = self::ERROR_INVALID_PARAMETERS;
            return false;
        }

        if (strpos($param['dates'], 'to')) {
            $dates = explode('to', $param['dates']);
            $this->conditions['conditions'] = 'date >= :date0: and date <= :date1:';
            $this->conditions['bind']['date0'] = $dates[0];
            $this->conditions['bind']['date1'] = $dates[1];

        } else {
            $dates = explode(',', $param['dates']);
            $first = true;
            $cont = 0;
            $cndtns = '';
            foreach ($dates as $date) {
                if (!$first) {
                    $cndtns = $cndtns. ' or ';
                }
                $first = false;
                $cndtns = $cndtns . 'date = :date'.$cont.':';
                $this->conditions['bind']['dateini'.$cont] = $date;
                $cont+=1;
            }
            $this->conditions['conditions'] = '(' . $cndtns . ')';
        }

        if (isset($param['status'])) {
            $status = explode(',', $param['status']);
            $cndtns = [];
            $cont = 0;
            foreach ($status as $sts) {
                $cndtns[] = "status = :sts{$cont}:";
                $this->conditions['bind']['sts'.$cont] = $sts;
                $cont+=1;
            }
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and (' . implode(' or ', $cndtns) . ')';
        }

        if ($param['route'] == 'new') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/addnew';
        } elseif ($param['route'] == 'report') {
            $this->conditions['conditions'] = $this->conditions['conditions'] . ' and uri like :uris:';
            $this->conditions['bind']['uris'] = '%reports/add';
        }

        $this->conditions['conditions'] = $this->conditions['conditions'] . ' and method = :method:';
        $this->conditions['bind']['method'] = 'post';

        $columns = [
            'year(date) as yearmonth',
            'user_id',
            'status',
            "group_concat(distinct(if(uri is null, concat(method, ': ', '/'),concat(method, ': ', uri)))) as route",
            'max(timestamp) - min(timestamp) as seconds',
            'sum(if(type = 1, 1, 0)) as clicks',
            'sum(if(type = 2, 1, 0)) as hovers',
            'sum(if(type = 3, 1, 0)) as map_clicks',
            'count(distinct App\Models\Movements.id) as count'
        ];
        if (isset($param['group']) && $param['group'] = 'user') {
            $group = ['yearmonth','user_id','uri'];
        } else {
            $group = ['yearmonth', 'uri'];
        }
        $query = Movements::query()
            ->innerJoin('App\Models\UserTracks', 'user_tracks.movement_id = App\Models\Movements.id', 'user_tracks')
            ->columns($columns)
            ->groupBy($group)
            ->where($this->conditions['conditions'], $this->conditions['bind'])
            ->execute();

        if ($query->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        $result = [];
        foreach ($query as $row) {

            if (isset($param['group']) && $param['group'] = 'user') {
                $user_id = $row->user_id != null? 'user:' .$row->user_id: 'user:' .'0';
                if (!isset($result[$row->yearmonth][$user_id])) {
                    $result[$row->yearmonth][$user_id] = [];
                }
                $result[$row->yearmonth][$user_id][] = [
                    'route' => $row->route,
                    'seconds' => $row->seconds/$row->count,
                    'clicks' => $row->clicks/$row->count,
                    'hovers' => $row->hovers/$row->count,
                    'map_clicks' => $row->map_clicks/$row->count,
                ];
            } else {
                if (!isset($result[$row->yearmonth])) {
                    $result[$row->yearmonth] = [];
                }
                $result[$row->yearmonth][] = [
                    'route' => $row->route,
                    'seconds' => $row->seconds/$row->count,
                    'clicks' => $row->clicks/$row->count,
                    'hovers' => $row->hovers/$row->count,
                    'map_clicks' => $row->map_clicks/$row->count,
                ];
            }
        }

        return $result;

    }

    public function getServicesRanking($param = null) {

        $this->conditions = [
            'conditions' => [],
            'bind' => [

            ]
        ];

        $conditions = false;

        if (isset($param['service_type_id'])) {
            $this->conditions['conditions'][] = 'App\Models\Services.service_types_id = :service_type:';
            $this->conditions['bind']['service_type'] = $param['service_type_id'];
            $conditions = true;
        }

        $columns = [
            'App\Models\Services.id',
            'x_position',
            'y_position',
            'if(App\Models\Services.price is null, 0, price_level.level) prices',
            'if(App\Models\Services.quality is null, 0, quality) as qualities',
            'if(confiability is null, 0, confiability) as confiabilities',
            'App\Models\Services.name as service_name',
            'servicetypes.name as service_type_name',
            '(if(App\Models\Services.price is null,0,1/price_level.level) + if(quality is null, 0, 1/(6-quality)) + if(confiability is null, 0, confiability/100))/3 as ponderation'
        ];

        $result = Services::query()
            ->leftJoin('App\Models\PriceRanges', 'price_level.id = App\Models\Services.price', 'price_level')
            ->innerJoin('App\Models\ServiceTypes', 'servicetypes.id = App\Models\Services.service_types_id', 'servicetypes')
            ->columns($columns)
            ->orderBy('ponderation desc');

        if (isset($param['limit']) and intval($param['limit']) > 0) {
            $result = $result->limit(intval($param['limit']));
        }

        if ($conditions) {
            $this->conditions['conditions'] = implode(' and ', $this->conditions['conditions']);
            $result = $result->where($this->conditions['conditions'], $this->conditions['bind'])
                ->execute();
        } else {
            $result = $result->execute();
        }

        if ($result->count() == 0) {
            $this->error[] = self::ERROR_NO_RECORDS_FOUND;
            return false;
        }

        return $result->toArray();
    }

    public function setMovements($param) {

        if (!isset($param['movement_id']) || !isset($param['stack_click'])) {
            return false;
        }

        $this->db->begin();
        if (isset($param['stack_click'])) {
            if (!is_array($param['stack_click'])) {
                $param['stack_click'] = json_decode($param['stack_click'], true);
            }
            foreach ($param['stack_click'] as $mov) {
                $tempTrack = new UserTracks();
                $tempTrack->movement_id = $param['movement_id'];
                $tempTrack->pos_x = isset($mov['pos_x']) ? $mov['pos_x'] : 0;
                $tempTrack->pos_y = isset($mov['pos_y']) ? $mov['pos_y'] : 0;
                $tempTrack->classes = isset($mov['id']) ? $mov['id'] : '';
                if (isset($mov['timestamp'])) {
                    $tempTrack->timestamp = new \Phalcon\Db\RawValue("FROM_UNIXTIME({$mov['timestamp']})");
                } else {
                    $tempTrack->timestamp = null;
                }
                $tempTrack->type = 1;
                if (!$tempTrack->save()) {
                    $this->db->rollback();
                    return false;
                }
            }
        }

        if (isset($param['stack_over'])) {
            if (!is_array($param['stack_over'])) {
                $param['stack_over'] = json_decode($param['stack_over'], true);
            }
            foreach ($param['stack_over'] as $mov) {
                $tempTrack = new UserTracks();
                $tempTrack->movement_id = $param['movement_id'];
                $tempTrack->pos_x = isset($mov['pos_x']) ? $mov['pos_x'] : 0;
                $tempTrack->pos_y = isset($mov['pos_y']) ? $mov['pos_y'] : 0;
                $tempTrack->classes = isset($mov['id']) ? $mov['id'] : '';
                if (isset($mov['timestamp'])) {
                    $tempTrack->timestamp = new \Phalcon\Db\RawValue("FROM_UNIXTIME({$mov['timestamp']})");
                } else {
                    $tempTrack->timestamp = null;
                }

                $tempTrack->type = 2;
                if (!$tempTrack->save()) {
                    $this->db->rollback();
                    return false;
                }
            }
        }

        if (isset($param['stack_click_map'])) {
            if (!is_array($param['stack_click_map'])) {
                $param['stack_click_map'] = json_decode($param['stack_click_map'], true);
            }
            foreach ($param['stack_click_map'] as $mov) {
                $tempTrack = new UserTracks();
                $tempTrack->movement_id = $param['movement_id'];
                $tempTrack->pos_x = isset($mov['pos_x']) ? $mov['pos_x'] : 0;
                $tempTrack->pos_y = isset($mov['pos_y']) ? $mov['pos_y'] : 0;
                $tempTrack->classes = isset($mov['id']) ? $mov['id'] : '';
                if (isset($mov['timestamp'])) {
                    $tempTrack->timestamp = new \Phalcon\Db\RawValue("FROM_UNIXTIME({$mov['timestamp']})");
                } else {
                    $tempTrack->timestamp = null;
                }
                $tempTrack->type = 3;
                if (!$tempTrack->save()) {
                    $this->db->rollback();
                    return false;
                }
            }
        }

        $this->db->commit();
        return true;

    }

}