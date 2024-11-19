<?php

global $CQ, $Event;
requireLvl(1);

if(!fromGroup()) {
    replyAndLeave('不在群内哦…');
}
$targets = $CQ->getGroupMemberList($Event['group_id']);
$type = intval(nextArg());
$types = [
    ['name' => '本周', 'round' => 1,],
    ['name' => '本学期', 'round' => 0],
];

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

    $total = 0;
    foreach($data['courses'] as $course) {
        if($type == 0 && in_array($currentWeek, $course['weeks'])) {
            $total += strtotime($course['endTime']) - strtotime($course['startTime']);
        } else if($type == 1) {
            $total += (strtotime($course['endTime']) - strtotime($course['startTime'])) * count($course['weeks']);
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
        $totalHours = round($data['total'] / 60 / 60, $types[$type]['round']);
        $reply .= "\n#{$n} {$totalHours}小时 @{$nickname}";
    }
    replyAndLeave($reply);
}