<?php

global $Event;
loadModule('ddl.tools');

$names = nextArg(true);
if(!$names) {
    replyAndLeave('不知道你想完成什么呢…');
}
$ddls = getDdl($Event['user_id']);
$reply = [];

foreach(explode(' ', $names) as $name) {
    $match = [];
    foreach($ddls as $ddl) {
        if(strpos($ddl['name'], $name) !== false) {
            $match[] = $ddl['name'];
        }
        if($ddl['name'] == $name) {
            $match = [$ddl['name']];
            break;
        }
    }

    if(count($match) == 0) {
        $reply[] = "没有找到名为 {$name} 的待办事项哦…";
    } else if(count($match) == 1) {
        finishDdl($Event['user_id'], $match[0]);
        $reply[] = "恭喜完成任务 {$match[0]}～";
    } else {
        $reply[] = "有多个匹配 {$name} 的待办事项，请具体指定哦…";
    }
}

replyAndLeave(implode("\n", $reply));
