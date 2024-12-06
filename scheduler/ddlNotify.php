<?php

global $Schedulers;
$Schedulers[] = new BLBot\Scheduler(
    'ddlNotify',
    true,
    function ($timestamp) {
        return intval(date('s', $timestamp)) < 5 && intval(date('i', $timestamp)) % 5 == 0;
    },
    function ($timestamp) {
        global $CQ, $Database;
        loadModule('ddl.tools');
        $data = $Database->ddl->find([
            'notify' => ['$ne' => false, '$exists' => true],
        ]);
        foreach($data as $userData) {
            $timeDiff = $timestamp - strtotime($userData['notify']);
            if($timeDiff < 0 || $timeDiff >= 5 * 60) continue;

            $reply = [];
            $result = [
                'expired' => [],
                'critical' => [],
                'urgent' => [],
                'regular' => [],
            ];
            foreach($userData['ddls'] as $ddl) {
                $remainTime = $ddl['time'] - $timestamp;
                $time = date('Y/m/d', $ddl['time']);
                $description = "{$time} {$ddl['name']}";
                if($remainTime < 0) {
                    $result['expired'][] = $description;
                } else if($remainTime <= 3 * 86400) {
                    $result['critical'][] = $description;
                } else if($remainTime <= 7 * 86400) {
                    $result['urgent'][] = $description;
                } else {
                    $result['regular'][] = $description;
                }
            }
            if(count($result['expired'])) {
                $reply[] = "———— 逾期 ————\n".implode("\n", $result['expired']);
            }
            if(count($result['critical'])) {
                $reply[] = "———— 紧急 ————\n".implode("\n", $result['critical']);
            }
            if(count($result['urgent'])) {
                $reply[] = "———— 优先 ————\n".implode("\n", $result['urgent']);
            }
            if(count($result['regular'])) {
                $reply[] = "———— 其他 ————\n".implode("\n", $result['regular']);
            }

            if(count($reply)) {
                $CQ->sendPrivateMsg($userData['user_id'], implode("\n", $reply));
            }
        }
    }
);