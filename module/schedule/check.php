<?php

global $CQ, $Event;
requireLvl(1);

if(fromGroup()) {
    $targets = $CQ->getGroupMemberList($Event['group_id']);
} else {
    $targets = json_decode("[{\"user_id\":{$Event['user_id']}}]");
}
$weekday = date('N');
$time = date('H:i');
$week = intval(date('W'));

$results = [];
foreach($targets as $target) {
    $data = getData('schedule/'.$target->user_id);
    if(!$data) continue;
    $data = json_decode($data, true);

    // 匹配周数
    $semesterStart = new DateTime('@'.$data['semesterStart']);
    $semesterStart->modify('Monday this week');
    $currentWeekStart = new DateTime();
    $currentWeekStart->modify('Monday this week');
    $currentWeek = ceil($semesterStart->diff($currentWeekStart)->days / 7) + 1;

    // 匹配当日课程
    // 第{$currentWeek}周 周{$weekday}
    $todayCourses = array_filter($data['courses'], function ($course) use ($weekday, $currentWeek) {
        return $course['day'] == $weekday && in_array($currentWeek, $course['weeks']);
    });
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
        $results[$target->user_id] = ' ‣ 今日课程已上完';
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
            $reply[] = "[ {$nickname} ]\n{$content}";
        }
        replyAndLeave(implode("\n", $reply));
    } else {
        foreach($results as $user_id => $content) {
            replyAndLeave($content);
        }
    }
}