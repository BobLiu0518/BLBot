<?php

requireLvl(3);
loadModule('schedule.tools');

global $CQ, $Event;
$db = getScheduleDb();
$data = $db->get($Event['user_id']);
$courses = $data['courses'];
if(!$data['notify']) {
    replyAndLeave('请先使用 #schedule.notify 启用上课订阅功能哦…');
}

$courseName = nextArg();
$note = nextArg(true);
if(!$courseName) {
    if(fromGroup()) {
        replyAndLeave('如果希望查询设置的课程备注，请私信 Bot 使用本指令。');
    }
    if(!$data['note'] || !count($data['note'])) {
        replyAndLeave('你还没有设置过备注哦…');
    }
    $reply = ['当前设置的备注：'];
    foreach($data['note'] as $course => $note) {
        $reply[] = "「{$course}」\n· {$note}";
    }
    replyAndLeave(implode("\n", $reply));
}

$courseFound = false;
$bestMatch = [];
foreach($courses as $course) {
    if($courseName == $course['name']) {
        $courseFound = true;
        break;
    }
    if(strpos($course['name'], $courseName) !== false) {
        $bestMatch[] = $course['name'];
    }
}
if(!$courseFound) {
    if(count($bestMatch) == 1) {
        $courseName = $bestMatch[0];
    } else if(count($bestMatch) > 1) {
        replyAndLeave('有多个匹配的课程：'.implode(' / ', $bestMatch).'，请具体指定');
    } else {
        replyAndLeave("课程 {$courseName} 不存在…");
    }
}

if(!$note) {
    $db->remove($Event['user_id'], 'notes.'.$courseName);
    replyAndLeave("已清除课程 {$courseName} 的备注～");
}

$db->set($Event['user_id'], [
    'note.'.$courseName => $note,
]);
replyAndLeave("已设置课程 {$courseName} 的备注～");
