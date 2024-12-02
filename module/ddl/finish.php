<?php

global $Event;
loadModule('ddl.tools');

$name = nextArg();
if(!$name) {
    replyAndLeave('不知道你想完成什么呢…');
}

finishDdl($Event['user_id'], $name);
replyAndLeave('恭喜完成任务～');