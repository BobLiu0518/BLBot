<?php

global $Event;
loadModule('ddl.tools');
requireLvl(1);

$name = nextArg();
if(!$name) {
    replyAndLeave('不知道你想设置什么呢…');
}
if($name == '逾期' || $name == 'expired') {
    replyAndLeave("无法设置 {$name} 为任务名称哦…");
}

$time = nextArg(true);
$ddl = $time ? strtotime($time) : 1e16;
if(!$ddl) {
    replyAndLeave("无法识别的时间：{$time}");
}
$time = $ddl >= 1e16 ? '长期' : date('Y/m/d', $ddl);
if(mb_strlen($name) > 10) {
    replyAndLeave('名称太长了哦，精简一下吧～');
}
$tasks = getDdl($Event['user_id']);
foreach($tasks as $task) {
    if($task['name'] == $name) {
        updateDdl($Event['user_id'], $task['name'], $ddl);
        replyAndLeave("更新任务 {$name} 成功，截止：{$time}");
    }
}

setDdl($Event['user_id'], $name, $ddl);
replyAndLeave("设置任务 {$name} 成功，截止：{$time}");
