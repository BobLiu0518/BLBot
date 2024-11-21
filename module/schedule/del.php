<?php

global $Event;

$db = new BLBot\Database('schedule');
$data = $db->get($Event['user_id']);
if(!$data) {
    replyAndLeave('未储存课程表信息…');
}
$db->delete($Event['user_id']);
replyAndLeave("删除课表 {$data['name']} 信息成功。");