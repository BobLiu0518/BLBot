<?php

global $Message, $Command;

if($Message == '群友在上什么课') {
    loadModule('schedule.everyone');
    leave();
}

if(preg_match('/^这是来自「WakeUp课程表」的课表分享，30分钟内有效哦，如果失效请朋友再分享一遍叭。为了保护隐私我们选择不监听你的剪贴板，请复制这条消息后，打开App的主界面，右上角第二个按钮 -> 从分享口令导入，按操作提示即可完成导入~分享口令为「(.+)」$/', $Message, $matches)) {
    $Command = ['fromMiddleware', $matches[1]];
    loadModule('schedule.wakeup');
}

if(preg_match('/^https:\/\/i.ai.mi.com\/h5\/precache\/ai-schedule\/#\/import_schedule\?linkToken=.+$/', $Message, $matches)) {
    $Command = ['fromMiddleware', $matches[0]];
    loadModule('schedule.xiaoai');
}