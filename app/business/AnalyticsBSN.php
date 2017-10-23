<?php

namespace App\Business;

use App\Models\Movements;
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

    public function setMovements($param) {

        if (!isset($param['movement_id']) || !isset($param['stack_click'])) {
            return false;
        }

        $this->db->begin();
        if (isset($param['stack_click'])) {
            $param['stack_click'] = json_decode($param['stack_click'], true);
            foreach ($param['stack_click'] as $mov) {
                $tempTrack = new UserTracks();
                $tempTrack->movement_id = $param['movement_id'];
                $tempTrack->pos_x = isset($mov['pos_x']) ? $mov['pos_x'] : 0;
                $tempTrack->pos_y = isset($mov['pos_y']) ? $mov['pos_y'] : 0;
                $tempTrack->classes = isset($mov['classes']) ? $mov['classes'] : '';
                $tempTrack->timestamp = isset($mov['timestamp']) ? $mov['timestamp'] : (new \DateTime('NOW'))->getTimestamp();
                $tempTrack->type = 1;
                if (!$tempTrack->save()) {
                    $this->db->rollback();
                    return false;
                }
            }
        }

        if (isset($param['stack_over'])) {
            $param['stack_over'] = json_decode($param['stack_over'], true);
            foreach ($param['stack_over'] as $mov) {
                $tempTrack = new UserTracks();
                $tempTrack->movement_id = $param['movement_id'];
                $tempTrack->pos_x = isset($mov['pos_x']) ? $mov['pos_x'] : 0;
                $tempTrack->pos_y = isset($mov['pos_y']) ? $mov['pos_y'] : 0;
                $tempTrack->classes = isset($mov['classes']) ? $mov['classes'] : '';
                $tempTrack->timestamp = isset($mov['timestamp']) ? $mov['timestamp'] : (new \DateTime('NOW'))->getTimestamp();
                $tempTrack->type = 1;
                if (!$tempTrack->save()) {
                    $this->db->rollback();
                    return false;
                }
            }
        }

        if (isset($param['stack_click_map'])) {
            $param['stack_click_map'] = json_decode($param['stack_click_map'], true);
            foreach ($param['stack_click_map'] as $mov) {
                $tempTrack = new UserTracks();
                $tempTrack->movement_id = $param['movement_id'];
                $tempTrack->pos_x = isset($mov['pos_x']) ? $mov['pos_x'] : 0;
                $tempTrack->pos_y = isset($mov['pos_y']) ? $mov['pos_y'] : 0;
                $tempTrack->classes = isset($mov['classes']) ? $mov['classes'] : '';
                $tempTrack->timestamp = isset($mov['timestamp']) ? $mov['timestamp'] : (new \DateTime('NOW'))->getTimestamp();
                $tempTrack->type = 1;
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