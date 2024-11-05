<?php

global $Event;
requireLvl(1);
loadModule('config.tools');
requireGroupAdmin();

$command = preg_replace('/^#/', '', nextArg());
$data = getConfig($Event['group_id']);
if(preg_match('/\./', $command)) {
    replyAndLeave('只能设置一级指令哦…');
} else if(!checkModule($command)) {
    replyAndLeave('指令 #'.$command.' 不存在…');
}

$data['commands'][] = $command;
setConfig($Event['group_id'], $data);
replyAndLeave('添加指令 #'.$command.' 成功。');