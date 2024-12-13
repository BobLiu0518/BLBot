<?php

global $Event, $Command, $Text;
loadModule('schedule.tools');

$name = null;
$semesterStart = null;
$courses = [];

$code = trim(implode(' ', array_splice($Command, 1)).$Text);
if(!$code) {
    replyAndLeave(<<<EOT
设置课程表方法：
1. 打开 WakeUp 课程表，导入或手动录入自己的课表信息；
2. 点击右上角的分享按钮，选择“在线分享课表”，复制口令；
3. 发送指令 #schedule.wakeup <口令>，注意指令中不包含括号。
EOT);
} else if(preg_match('/「([0-9a-zA-Z\-_]+?)」/u', $code, $matches)) {
    $code = $matches[1];
} else if(!preg_match('/^[0-9a-zA-Z\-_]+$/u', $code)) {
    replyAndLeave('这好像不是 WakeUp 课程表的口令哦…');
}

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'version: 250',
    ],
]);
$rawData = json_decode(file_get_contents('https://i.wakeup.fun/share_schedule/get?key='.$code, false, $context), true);
if(!$rawData['data']) replyAndLeave('数据读取失败，可能是分享口令无效或已过期…');
$data = [];
foreach(explode("\n", $rawData['data']) as $n => $json) {
    $data[$n] = json_decode($json, true);
}

$nodesInfo = [];
foreach($data[1] as $node) {
    $nodesInfo[$node['node']] = $node;
}

$courseInfo = [];
foreach($data[3] as $course) {
    $courseInfo[$course['id']] = $course['courseName'];
}

$name = $data[2]['tableName'];
$semesterStart = strtotime($data[2]['startDate']);

foreach($data[4] as $course) {
    $weeks = [];
    for($i = $course['startWeek']; $i <= $course['endWeek']; $i++) {
        if($course['type'] == 0 || $course['type'] % 2 == $i % 2) {
            $weeks[] = $i;
        }
    }
    if($course['ownTime']) {
        $startTime = $course['startTime'];
        $endTime = $course['endTime'];
    } else {
        $startTime = $nodesInfo[$course['startNode']]['startTime'];
        $endTime = $nodesInfo[$course['startNode'] + $course['step'] - 1]['endTime'];
    }
    $courses[] = [
        'name' => $courseInfo[$course['id']],
        'weeks' => $weeks,
        'day' => strval($course['day']),
        'startTime' => $startTime,
        'endTime' => $endTime,
        'location' => $course['room'],
    ];
}

setScheduleData($Event['user_id'], $name, $semesterStart, $courses);
replyAndLeave('成功读取课程表：'.$name);
