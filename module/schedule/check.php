<?php

requireLvl(1);
loadModule('schedule.tools');

global $Event, $CQ;

$arg = nextArg();
if(preg_match('/^(?:\[CQ:at,qq=)?(\d+)(?:,name=.+?)?(?:])?$/', $arg, $matches)) {
    $target = intval($matches[1]);
    $arg = nextArg(true);
} else {
    $target = $Event['user_id'];
    $arg .= trim(' '.nextArg(true));
}
if($arg) {
    $time = strtotime($arg);
    if(!$time) {
        replyAndLeave("无法识别日期：{$arg}…");
    }
    $date = date('Y/m/d', $time);
} else {
    $time = time();
    $date = '今日';
}

$user = $CQ->getGroupMemberInfo($Event['group_id'], $target);
$nickname = $user->card ?? $user->nickname ?? replyAndLeave("{$target} 不在本群哦…");

$courses = getCourses($target, $time);
if($courses === false) {
    replyAndLeave($nickname.' 未配置课程表哦…');
} else if(!count($courses)) {
    replyAndLeave($nickname." {$date} 无课～");
}

$result = [$nickname." {$date} 课程："];
    $timezone = getTimezoneGMTOffset(getTimezone($target));
    if($timezone != 'GMT+8'){
        $result[]="({$timezone})";
    }
foreach($courses as $course) {
    $result[] = "{$course['startTime']}~{$course['endTime']} {$course['name']}";
}

replyAndLeave(implode("\n", $result));
