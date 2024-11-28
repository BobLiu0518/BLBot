<?php

requireLvl(3);
loadModule('schedule.tools');

global $CQ, $Event;
$isFriend = false;
$friendList = $CQ->getFriendList();
foreach($friendList as $friend) {
    if($friend->user_id == $Event['user_id']) {
        $isFriend = true;
        break;
    }
}
if(!$isFriend) {
    replyAndLeave('你还不是 Bot 的好友，无法订阅通知哦…');
}

$db = getScheduleDb();
$notify = $db->get($Event['user_id'])['notify'];
$arg = nextArg();
if(is_numeric($arg)) {
    $notify = intval($arg);
    if($notify < 0 || $notify > 60) {
        replyAndLeave('通知时间只能在 0~60 分钟内哦…');
    }
} else if(!$notify) {
    $notify = 15;
} else if($notify && $arg == 'disable') {
    $notify = false;
}

$db->set($Event['user_id'], [
    'notify' => $notify,
]);
replyAndLeave($notify ? "已启用课程订阅，在课程开始前 {$notify} 分钟进行提醒～" : '已禁用课程订阅～');