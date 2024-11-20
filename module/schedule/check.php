<?php

requireLvl(1);
loadModule('schedule.tools');

global $Event, $CQ;
$target = nextArg() ?? $Event['user_id'];
if(!is_numeric($target)) $target = parseQQ($target);
if(!is_numeric($target)) $target = $Event['user_id'];
$user = $CQ->getGroupMemberInfo($Event['group_id'], $target);
$nickname = $user->card ?? $user->nickname;

try {
    $courses = getCourses($target, time());
} catch (Exception $e) {
    replyAndLeave($e->getMessage());
}
$result = [$nickname.' 今日课程：'];
foreach($courses as $course) {
    $result[] = "{$course['startTime']}~{$course['endTime']} {$course['name']}";
}

replyAndLeave(implode("\n", $result));
