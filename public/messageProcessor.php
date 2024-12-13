<?php

$message = trim(preg_replace('/\[CQ:at,qq='.config('bot').'(,name=.+?)?\]/', '', $Event['message']));
$message = preg_replace('/\[CQ:face,id=(\d+?),large=\]/', '[CQ:face,id=$1]', $message);
if(preg_match('/\[CQ:reply,id=(-?\d+?)\]/', $message, $matches)) {
    $Referer = $matches[1];
    $message = preg_replace('/\[CQ:reply,id=(-?\d+?)\]/', '', $message);
}

$length = strpos($message, PHP_EOL);
if(false === $length) {
    $length = strlen($message);
}

if(preg_match('/^(\/)/', $message, $prefix)) {
    $Command = parseCommand(mb_substr($message, mb_strlen($prefix[1]) - 1, $length));
    $Text = substr($message, $length + 1);
    $module = substr(nextArg(), strlen($prefix[1]));
    loadModule($module);
} else { //不是命令
    replyAndLeave("不知道怎么使用 Bot ？发送 /help 即可查看帮助～");
}
