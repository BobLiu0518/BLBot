<?php

global $Event;
requireLvl(2);

$link = nextArg();
if(!$link) {
    replyAndLeave(<<<EOT
设置课程表方法：
1. 打开小爱课程表，导入或手动录入自己的课表信息，非小米设备可下载小爱同学或小爱课程表 app：
https://zhengy7.lanzoue.com/i9wkK0mx1fhe
2. 在课程表设置中，选择分享课表，复制分享链接；
3. 发送指令 #schedule.ai <分享链接>，注意指令中不包含括号。
EOT);
} else if(!preg_match('/linkToken=([0-9a-zA-Z\+\/=]+)$/', $link, $matches)) {
    replyAndLeave('这好像不是小爱课表的链接哦…');
}
$params = explode('%26', base64_decode($matches[1]));
$api = "https://i.ai.mi.com/course-multi/table?ctId={$params[4]}&userId={$params[0]}&deviceId={$params[1]}&sourceName=course-app-browser";
$data = json_decode(file_get_contents($api), true)['data'];

setData('schedule/'.$Event['user_id'], json_encode($data));
replyAndLeave('成功读取课程表：'.$data['name']);