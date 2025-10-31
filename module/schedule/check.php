<?php

requireLvl(1);
loadModule('schedule.tools');
loadModule('nickname.tools');

global $Event, $CQ;

$arg = nextArg();
if(fromGroup() && preg_match('/^(?:\[CQ:at,qq=)?(\d+)(?:,name=.+?)?(?:])?$/', $arg, $matches)) {
    $target = intval($matches[1]);
    $arg = nextArg(true);
} else {
    $target = $Event['user_id'];
    $arg .= trim(' '.nextArg(true));
}
$time = $arg;

$nickname = fromGroup() ? getNickname($target, $Event['group_id']) : '您';

$courses = getCourses($target, $time);
$date = $time ? date('Y/m/d', strtotime($time)) : '今日';
if($courses === false) {
    replyAndLeave($nickname.' 未配置课程表，或该学期课程已结束哦…');
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
