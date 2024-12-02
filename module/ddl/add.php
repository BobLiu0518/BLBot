<?php

global $Event;
loadModule('ddl.tools');
requireLvl(1);

$name = nextArg();
$time = nextArg(true);
if(!$name || !$time) {
    replyAndLeave('不知道你想设置什么呢…');
}
$time = strtotime($time);
if(!$time) {
    replyAndLeave("无法识别的时间：{$time}");
}
if(mb_strlen($name) > 10) {
    replyAndLeave('名称太长了哦，精简一下吧～');
}

setDdl($Event['user_id'], $name, $time);
$time = date('Y/m/d', $time);
replyAndLeave("设置任务 {$name} 成功，截止：{$time}");