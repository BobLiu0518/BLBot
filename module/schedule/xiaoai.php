<?php

global $Event, $Command, $Text;
loadModule('schedule.tools');
loadModule('schedule.xiaoai.tools');

$name = null;
$semesterStart = null;
$courses = [];

$link = trim(implode(' ', array_splice($Command, 1)).$Text);
if(!$link) {
    replyAndLeave(<<<EOT
设置课程表方法：
1. 打开小爱课程表，导入或手动录入自己的课表信息，非小米设备可下载小爱同学或小爱课程表 app；
注：关于如何绕过下线提示、强制从教务导入课表的方法，请自行搜寻；
2. 在课程表设置中，选择分享课表，复制分享链接；
3. 发送指令 /schedule.xiaoai <分享链接>，注意指令中不包含括号。
EOT);
} else if(!preg_match('/(?:linkToken|token)=([0-9a-zA-Z\+\/=]+)$/', $link, $matches)) {
    replyAndLeave('这好像不是小爱课表的链接哦…');
}
$params = explode('%26', base64_decode($matches[1]));
$data = (new AiSchedule())->getCourseTable($params[2]);
if(!$data) replyAndLeave('读取失败，可能是链接无效…');

$name = $data['name'];
$semesterStart = json_decode($data['setting']['extend'], true)['startSemester'] / 1000;
if(!$semesterStart) {
    $dateTime = new DateTime('Monday this week');
    $dateTime->modify('-'.($data['setting']['presentWeek'] - 1).'weeks');
    $semesterStart = $dateTime->getTimestamp();
}

$sectionTimes = [];
foreach(json_decode($data['setting']['sectionTimes'], true) as $sectionTime) {
    $sectionTimes[$sectionTime['i']] = [
        'startTime' => $sectionTime['s'],
        'endTime' => $sectionTime['e'],
    ];
}

foreach($data['courses'] as $course) {
    $courseSection = explode(',', $course['sections']);
    $courses[] = [
        'name' => $course['name'],
        'weeks' => array_map('intval', explode(',', $course['weeks'])),
        'day' => strval($course['day']),
        'startTime' => $sectionTimes[intval($courseSection[0])]['startTime'],
        'endTime' => $sectionTimes[intval($courseSection[count($courseSection) - 1])]['endTime'],
        'location' => $course['position'],
    ];
}

setScheduleData($Event['user_id'], $name, $semesterStart, $courses);
replyAndLeave('成功读取课程表：'.$name);
