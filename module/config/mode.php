<?php

global $Event;
requireLvl(1);
loadModule('config.tools');
requireGroupAdmin();

$mode = nextArg();
$data = getConfig($Event['group_id']);
if(in_array($mode, ['b', 'blacklist', '黑名单'])) {
    $data['mode'] = 'blacklist';
} else if(in_array($mode, ['w', 'whitelist', '白名单'])) {
    $data['mode'] = 'whitelist';
} else {
    replyAndLeave('模式 '.$mode.' 不存在。可选：黑名单 / 白名单');
}

setConfig($Event['group_id'], $data);
replyAndLeave('设置模式成功。');