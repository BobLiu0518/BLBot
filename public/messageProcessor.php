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

if(preg_match('/^('.config('prefix', '#').')/', $message, $prefix)
    || preg_match('/^('.config('prefix2', '.').')/', $message, $prefix) && config('enablePrefix2', false)) {
    $Command = parseCommand(mb_substr($message, mb_strlen($prefix[1]) - 1, $length));
    $Text = substr($message, $length + 1);
    $module = substr(nextArg(), strlen($prefix[1]));
    try {
        if(config('alias', false) == true) {
            loadModule('alias.tools');
            if($alias = getAlias($Event['user_id'])[$module]) {
                $module = $alias;
            }
        }
        loadModule($module);
    } catch (\Exception $e) {
        throw $e;
    }
} else { //不是命令
    $Message = $message;
    $Command = parseCommand(substr($message, 0, $length));
    $Text = substr($message, $length + 1);
    require('../middleWare/Chain.php');
}
