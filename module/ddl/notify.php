<?php

requireLvl(4);
loadModule('ddl.tools');

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

$db = getDdlDb();
$notify = $db->get($Event['user_id'])['notify'];
$arg = nextArg(true);
if($arg == 'disable') {
    $notify = false;
} else if(!$arg) {
    $notify = '08:00';
} else {
    $notify = strtotime($arg);
    if(!$notify) {
        replyAndLeave("无法识别的时间：{$arg}");
    }
    $notify = date('H:i', $notify);
}

$db->set($Event['user_id'], [
    'notify' => $notify,
]);
replyAndLeave($notify ? "已启用待办订阅，在每天 {$notify} 进行提醒～" : '已禁用待办订阅～');