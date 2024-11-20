<?php

global $Event;

$nickname = nextArg();
if(!$nickname) {
    replyAndLeave('不知道你要设置什么作为昵称呢…');
}
if(preg_match('/[\x{0000}-\x{001F}\x{007F}-\x{009F}]/u', $nickname)) {
    replyAndLeave('昵称中不能包含 Unicode 控制字符哦…');
}
if(preg_match('/\[CQ:/', $nickname)) {
    replyAndLeave('昵称中不能包含特殊内容哦…');
}
if(preg_match('/\s/', $nickname)) {
    replyAndLeave('昵称中不能包含空白字符哦…');
}
if(mb_strlen($nickname) > 20) {
    replyAndLeave('昵称太长了哦…');
}
setData('nickname/'.$Event['user_id'], $nickname);
replyAndLeave('设置昵称成功～');