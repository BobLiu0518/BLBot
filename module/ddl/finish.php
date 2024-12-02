<?php

global $Event;
loadModule('ddl.tools');

$names = nextArg(true);
if(!$names) {
    replyAndLeave('不知道你想完成什么呢…');
}

foreach(explode(' ', $names) as $name) {
    finishDdl($Event['user_id'], $name);
}
replyAndLeave('恭喜完成任务～');