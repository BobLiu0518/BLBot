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

if(!preg_match('/^(?:https?|webcal):\/\/(.+?)\//', $link, $matches)) {
    replyAndLeave('请输入完整的链接噢~');
}
$domain = $matches[1];

$iCal = new ZCiCal(file_get_contents(str_replace('webcal://', 'https://', $link)));
$timezoneName = 'Asia/Shanghai';
foreach($iCal->tree->child as $node) {
    if($node->getName() == 'VTIMEZONE' && $node->data['TZID']->getValues()) {
        $timezoneName = $node->data['TZID']->getValues();
    } else if($node->getName() == 'VEVENT') {
        $name = '';
        $start = '';
        $end = '';
        $rrule = null;
        foreach($node->data as $key => $value) {
            if($key == 'SUMMARY') {
                $name = $value->getValues();
            } else if($key == 'DTSTART') {
                $timezoneName = $value->getParameters()['tzid'];
                $start = $value->getValues();
            } else if($key == 'DTEND') {
                $end = $value->getValues();
            } else if($key == 'RRULE') {
                $rrule = $value->getValues();
            }
        }
        $events[] = [
            'name' => $name,
            'start' => $start,
            'end' => $end,
            'rrule' => $rrule,
        ];
    }
}

$timezone = new DateTimeZone($timezoneName);
$semesterStart = min(array_column($events, 'start'));
$semesterStart = DateTime::createFromFormat('Ymd\THis', $semesterStart, $timezone)->modify('Monday this week');
foreach($events as $event) {
    $start = DateTime::createFromFormat('Ymd\THis', $event['start'], $timezone);
    $end = DateTime::createFromFormat('Ymd\THis', $event['end'], $timezone);
    $courseInstances = [];
    if ($event['rrule']) {
        $rruleParts = explode(';', $event['rrule']);
        $freq = null;
        $until = null;
        $byday = null;
        foreach ($rruleParts as $part) {
            if (strpos($part, 'FREQ=') === 0) {
                $freq = str_replace('FREQ=', '', $part);
            } elseif (strpos($part, 'UNTIL=') === 0) {
                $until = str_replace('UNTIL=', '', $part);
            } elseif (strpos($part, 'BYDAY=') === 0) {
                $byday = str_replace('BYDAY=', '', $part);
            }
        }
        if ($freq === 'WEEKLY' && $until) {
            $untilDate = new DateTime($until, new DateTimeZone('UTC'));
            $untilDate->setTimezone($timezone);
            if ($byday) {
                $bydays = explode(',', $byday);
                $dayMap = ['SU' => 0, 'MO' => 1, 'TU' => 2, 'WE' => 3, 'TH' => 4, 'FR' => 5, 'SA' => 6];
                foreach ($bydays as $bd) {
                    $targetDay = $dayMap[$bd];
                    $startDay = $start->format('w');
                    $diff = ($targetDay - $startDay + 7) % 7;
                    $baseStart = clone $start;
                    $baseStart->modify('+' . $diff . ' days');
                    $current = clone $baseStart;
                    while ($current <= $untilDate) {
                        $startClone = clone $current;
                        $endClone = clone $current;
                        $endClone->modify('+'.($end->getTimestamp() - $start->getTimestamp()).' seconds');
                        $courseInstances[] = [
                            'start' => $startClone,
                            'end' => $endClone,
                        ];
                        $current->modify('+1 week');
                    }
                }
            } else {
                $current = clone $start;
                while ($current <= $untilDate) {
                    $startClone = clone $current;
                    $endClone = clone $current;
                    $endClone->modify('+'.($end->getTimestamp() - $start->getTimestamp()).' seconds');
                    $courseInstances[] = [
                        'start' => $startClone,
                        'end' => $endClone,
                    ];
                    $current->modify('+1 week');
                }
            }
        } else {
            $courseInstances[] = ['start' => $start, 'end' => $end];
        }
    } else {
        $courseInstances[] = ['start' => $start, 'end' => $end];
    }
    foreach ($courseInstances as $instance) {
        $courses[] = [
            'name' => $event['name'],
            'weeks' => [floor($instance['start']->diff($semesterStart)->days / 7) + 1],
            'day' => $instance['start']->format('w'),
            'startTime' => $instance['start']->format('H:i'),
            'endTime' => $instance['end']->format('H:i'),
        ];
    }
}
$semesterStart = $semesterStart->getTimestamp();

setScheduleData($Event['user_id'], 'iCalendar:'.$domain, $semesterStart, $courses, $timezoneName);
replyAndLeave('成功读取课程表～');
