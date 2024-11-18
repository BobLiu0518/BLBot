<?php

global $CQ, $Event;
requireLvl(1);

if(fromGroup()) {
    $targets = $CQ->getGroupMemberList($Event['group_id']);
} else {
    $targets = json_decode("[{\"user_id\":{$Event['user_id']}}]");
}
$weekday = intval(date('N'));
$time = date('H:i');
$week = intval(date('W'));

$results = [];
foreach($targets as $target) {
    $data = getData('schedule/'.$target->user_id);
    if(!$data) continue;
    $data = json_decode($data, true);

    // 匹配节数
    $section = null;
    $isTimeFullyMatched = false;
    $sectionTimes = json_decode($data['setting']['sectionTimes'], true);
    foreach($sectionTimes as $sectionTime) {
        if($time >= $sectionTime['s'] && $time <= $sectionTime['e']) {
            $isTimeFullyMatched = true;
            $section = $sectionTime['i'];
            break;
        } else if($time <= $sectionTime['s']) {
            $section = $sectionTime['i'];
            break;
        }
    }

    // 匹配周数
    $settingsExtend = json_decode($data['setting']['extend'], true);
    $startSemester = $settingsExtend['startSemester'] / 1000;
    $currentWeek = ($week - intval(date('W', $startSemester)) + 52) % 52 + 1;

    // 匹配当日课程
    // 第{$currentWeek}周 周{$weekday}
    $todayCourses = array_filter($data['courses'], function ($course) use ($weekday, $currentWeek) {
        return $course['day'] == $weekday && in_array($currentWeek, explode(',', $course['weeks']));
    });
    if(!count($todayCourses)) {
        $results[$target->user_id] = '今日无课';
        continue;
    }

    // 匹配当前/下节课程 第{$section}节
    if($section === null) {
        $results[$target->user_id] = '今日课程已上完';
        continue;
    }
    $result = null;
    foreach($todayCourses as $todayCourse) {
        if(in_array($section, explode(',', $todayCourse['sections']))) {
            if($isTimeFullyMatched) {
                $sections = explode(',', $todayCourse['sections']);
                $startTime = $sectionTimes[intval($sections[0]) - 1]['s'];
                $endTime = $sectionTimes[intval($sections[count($sections) - 1]) - 1]['e'];
                $result = "正在上 {$todayCourse['name']}（{$startTime}~{$endTime}）";
            } else {
                $startTime = $sectionTimes[intval($sections[0]) - 1]['s'];
                $result = "准备上 {$todayCourse['name']}（{$startTime}起）";
            }
            break;
        } else if($section < intval(explode(',', $todayCourse['sections'])[0])) {
            $startTime = $sectionTimes[intval($sections[0]) - 1]['s'];
            $result = "准备上 {$todayCourse['name']}（{$startTime}起）";
            break;
        }
    }
    if($result === null) $result = '今日课程已上完';
    $results[$target->user_id] = $result;
}

if(!count($results)) {
    replyAndLeave(fromGroup() ? '暂无群友配置了课程表哦…' : '暂未配置课程表哦…');
} else {
    if(fromGroup()) {
        $reply = '群友在：';
        foreach($results as $user_id => $content) {
            $user = $CQ->getGroupMemberInfo($Event['group_id'], $user_id);
            $nickname = $user->card ?? $user->nickname;
            $reply .= "\n[ {$nickname} ]\n‣ {$content}";
        }
    } else {
        $reply = '';
        foreach($results as $user_id => $content) {
            $reply .= $content;
        }
    }
    replyAndLeave($reply);
}