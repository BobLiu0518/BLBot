<?php

global $CQ, $Event;
requireLvl(1);
loadModule('schedule.tools');

if(fromGroup()) {
    $targets = $CQ->getGroupMemberList($Event['group_id']);
} else {
    $targets = json_decode("[{\"user_id\":{$Event['user_id']}}]");
}
$weekday = date('N');
$time = date('H:i');

$results = [];
foreach($targets as $target) {
    try {
        $todayCourses = getCourses($target->user_id, time());
    } catch (Exception $e) {
        continue;
    }

    if(!count($todayCourses)) {
        $results[$target->user_id] = ' ‣ 今日无课';
        continue;
    }

    // 匹配当前/下节课程
    foreach($todayCourses as $course) {
        if($time < $course['startTime']) {
            $results[$target->user_id] = " ‣ 下一节 {$course['startTime']}「{$course['name']}」";
            break;
        } else if($time >= $course['startTime'] && $time < $course['endTime']) {
            $results[$target->user_id] = " ‣「{$course['name']}」进行中\n 　{$course['startTime']} ~ {$course['endTime']}";
            break;
        }
    }
    if(!$results[$target->user_id]) {
        $total = 0;
        foreach($todayCourses as $course) {
            $total += strtotime($course['endTime']) - strtotime($course['startTime']);
        }
        $results[$target->user_id] = ' ‣ 今日课程已上完，共 '.round($total / 60 / 60, 1).' 小时';
    }
}

if(!count($results)) {
    replyAndLeave(fromGroup() ? '暂无群友配置了课程表哦…' : '暂未配置课程表哦…');
} else {
    if(fromGroup()) {
        $reply = [];
        foreach($results as $user_id => $content) {
            $user = $CQ->getGroupMemberInfo($Event['group_id'], $user_id);
            $nickname = $user->card ?? $user->nickname;
            $reply[] = "〖{$nickname}〗\n{$content}";
        }
        replyAndLeave(implode("\n", $reply));
    } else {
        foreach($results as $user_id => $content) {
            replyAndLeave($content);
        }
    }
}