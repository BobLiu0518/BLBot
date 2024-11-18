<?php

global $Event;

$data = getData('schedule/'.$Event['user_id']);
if(!$data) {
    replyAndLeave('未储存课程表信息…');
}
$name = json_decode($data, true)['name'];
delData('schedule/'.$Event['user_id']);
replyAndLeave("删除课表 {$name} 信息成功。");