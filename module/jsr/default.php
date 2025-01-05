<?php

global $Event;
requireLvl(1);

$db = new BLBot\Database('jsr');
$station = nextArg();
if(!$station) {
    $current = $db->get($Event['user_id'])['default'];
    if(!$current) {
        replyAndLeave('不知道你想设置什么作为默认站点呢…');
    }
    $db->set($Event['user_id'], ['default' => null]);
    replyAndLeave('成功清除默认站点～');
}
$station = preg_replace('/站$/', '', $station);
if(!in_array($station, ['春申', '新桥', '车墩', '叶榭', '亭林', '金山园区', '金山卫'])) {
    replyAndLeave('默认站点只能设置春申~金山卫内的车站哦…');
}
$db->set($Event['user_id'], ['default' => $station]);
replyAndLeave("成功设置默认站点为 {$station}站～");