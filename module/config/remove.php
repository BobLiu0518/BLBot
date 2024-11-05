<?php

global $Event;
requireLvl(1);
loadModule('config.tools');
requireGroupAdmin();

$command = preg_replace('/^#/', '', nextArg());
$data = getConfig($Event['group_id']);
if(!in_array($command, $data['commands'])) {
    replyAndLeave('未添加指令 #'.$command.' …');
}

$data['commands'] = array_diff($data['commands'], [$command]);
setConfig($Event['group_id'], $data);
replyAndLeave('移除指令 #'.$command.' 成功。');