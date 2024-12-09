<?php

global $Event;
loadModule('ddl.tools');

$ddls = getDdl($Event['user_id']);
if(!$ddls) {
    replyAndLeave('暂无未完成的待办事项哦…');
}

replyAndLeave(classifyDdls($ddls));
