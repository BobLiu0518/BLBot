<?php

global $Event;
loadModule('alias.tools');

$confirm = nextArg();
if($confirm !== 'confirm') {
    replyAndLeave('确认清空设置的别名吗？确定请发送 #alias.clear confirm');
}
clearAlias($Event['user_id']);
replyAndLeave('清空完毕～');
