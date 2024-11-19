<?php

global $CQ, $Event;
requireLvl(1);
loadModule('schedule.tools');

if(!fromGroup()) {
    replyAndLeave('不在群内哦…');
}
$targets = $CQ->getGroupMemberList($Event['group_id']);
$type = intval(nextArg());
$types = [
    ['name' => '本周', 'round' => 1,],
    ['name' => '本学期', 'round' => 0],
    ['name' => '今日', 'round' => 2],
];

$results = [];
foreach($targets as $target) {
    $data = getData('schedule/'.$target->user_id);
    if(!$data) continue;
    $data = json_decode($data, true);
    $currentWeek = getWeek($data['semesterStart'], time());
    $weekday = date('w');

    $total = 0;
    foreach($data['courses'] as $course) {
        if($type == 0 && in_array($currentWeek, $course['weeks'])) {
            $total += strtotime($course['endTime']) - strtotime($course['startTime']);
        } else if($type == 1) {
            $total += (strtotime($course['endTime']) - strtotime($course['startTime'])) * count($course['weeks']);
        } else if($type == 2 && in_array($currentWeek, $course['weeks']) && $course['day'] == $weekday) {
            $total += strtotime($course['endTime']) - strtotime($course['startTime']);
        }
    }
    $results[] = [
        'user_id' => $target->user_id,
        'total' => $total,
    ];
}
usort($results, function ($a, $b) {
    return $b['total'] <=> $a['total'];
});

if(!count($results)) {
    replyAndLeave(fromGroup() ? '暂无群友配置了课程表哦…' : '暂未配置课程表哦…');
} else {
    $groupName = $CQ->getGroupInfo($Event['group_id'])->group_name;
    $reply = $groupName.' '.$types[$type]['name'].'课程时长榜';
    foreach(array_splice($results, 0, 9) as $n => $data) {
        $n++;
        $user = $CQ->getGroupMemberInfo($Event['group_id'], $data['user_id']);
        $nickname = $user->card ?? $user->nickname;
        $totalHours = number_format($data['total'] / 60 / 60, $types[$type]['round']);
        $reply .= "\n#{$n} {$totalHours}小时 @{$nickname}";
    }
    replyAndLeave($reply);
}