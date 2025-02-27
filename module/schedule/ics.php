<?php

global $Event, $Command, $Text;
requireLvl(1);
loadModule('schedule.tools');
require('icalendar/zapcallib.php');

$name = null;
$semesterStart = null;
$courses = [];

$link = nextArg(true);
if(!$link) {
    replyAndLeave(<<<EOT
设置课程表方法：
1. 在教务系统中找到 .ics 文件下载链接；
2. 发送指令 #schedule.ics <链接>，注意指令中不包含括号。
EOT);
}

$events = [];

$iCal = new ZCiCal(file_get_contents($link));
$timezoneName = 'Asia/Shanghai';
foreach($iCal->tree->child as $node) {
    if($node->getName() == 'VTIMEZONE' && $node->data['TZID']->getValues()) {
        $timezoneName = $node->data['TZID']->getValues();
    } else if($node->getName() == 'VEVENT') {
        $name = '';
        $start = '';
        $end = '';
        foreach($node->data as $key => $value) {
            if($key == 'SUMMARY') {
                $name = $value->getValues();
            } else if($key == 'DTSTART') {
                $timezoneName = $value->getParameters()['tzid'];
                $start = $value->getValues();
            } else if($key == 'DTEND') {
                $end = $value->getValues();
            }
        }
        $events[] = [
            'name' => $name,
            'start' => $start,
            'end' => $end,
        ];
    }
}

$timezone = new DateTimeZone($timezoneName);
$semesterStart = min(array_column($events, 'start'));
$semesterStart = DateTime::createFromFormat('Ymd\THis', $semesterStart, $timezone)->modify('Monday this week');
foreach($events as $event) {
    $start = DateTime::createFromFormat('Ymd\THis', $event['start'], $timezone);
    $end = DateTime::createFromFormat('Ymd\THis', $event['end'], $timezone);
    $courses[] = [
        'name' => $event['name'],
        'weeks' => [ceil($start->diff($semesterStart)->days / 7) + 1],
        'day' => $start->format('w'),
        'startTime' => $start->format('H:i'),
        'endTime' => $end->format('H:i'),
    ];
}
$semesterStart = $semesterStart->getTimestamp();

setScheduleData($Event['user_id'], 'iCalendar', $semesterStart, $courses, $timezoneName);
replyAndLeave('成功读取课程表～');