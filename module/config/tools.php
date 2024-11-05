<?php

if(!fromGroup()) {
    replyAndLeave('本指令只能在群聊中使用…');
}

function requireGroupAdmin() {
    global $CQ, $Event;
    if($CQ->getGroupMemberInfo($Event['group_id'], $Event['user_id'])->role == 'member') {
        replyAndLeave('群指令配置只支持群管理使用哦~');
    }
}

function printConfig($data) {
    $mode = ['blacklist' => '黑名单', 'whitelist' => '白名单'][$data['mode']];
    $commands = implode(' ', $data['commands']);
    $silence = $data['silence'] ? '启用' : '禁用';
    if(!$commands) {
        $commands = '无';
    }
    return <<<EOT
［群指令配置］
匹配模式：{$mode}模式
配置指令：{$commands}
静默模式：{$silence}
EOT;
}