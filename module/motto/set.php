<?php

global $Event;
loadModule('motto.tools');

$motto = nextArg();
if(!$motto) {
    replyAndLeave('不知道你要设置什么作为个性签名呢…');
}
if(preg_match('/[\x{0000}-\x{001F}\x{007F}-\x{009F}]/u', $motto)) {
    replyAndLeave('个性签名中不能包含 Unicode 控制字符哦…');
}
if(preg_match('/\[CQ:/', $motto)) {
    replyAndLeave('个性签名中不能包含特殊内容哦…');
}
if(preg_match('/\s/', $motto)) {
    replyAndLeave('个性签名中不能包含空白字符哦…');
}
if(mb_strlen($motto) > 32) {
    replyAndLeave('个性签名太长了哦…');
}

setMotto($Event['user_id'], $motto);
replyAndLeave('设置个性签名成功～');