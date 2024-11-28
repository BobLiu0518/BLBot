<?php

global $Schedulers;
$Schedulers[] = new BLBot\Scheduler(
    'scheduleNotify',
    true,
    function ($timestamp) {
        return intval(date('s', $timestamp)) < 5 && intval(date('i', $timestamp)) % 5 == 0;
    },
    function ($timestamp) {
        global $CQ, $Database;
        loadModule('schedule.tools');
        $data = $Database->schedule->find([
            'notify' => ['$ne' => false, '$exists' => true],
        ]);
        foreach($data as $userData) {
            $todayCourses = getCourses($userData, time());
            foreach($todayCourses as $course) {
                $timeDiff = $timestamp + $userData['notify'] * 60 - strtotime($course['startTime']);
                if($timeDiff >= 0 && $timeDiff < 5 * 60) {
                    $msg = "{$course['name']} 将在 {$userData['notify']} 分钟后开始哦～";
                    if($userData['note'] && $userData['note'][$course['name']]) {
                        $msg .= "\n{$userData['note'][$course['name']]}";
                    }
                    $CQ->sendPrivateMsg($userData['user_id'], $msg);
                }
            }
        }
    }
);