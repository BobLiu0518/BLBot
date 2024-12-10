<?php

$text = nextArg(true);
if(!$text) {
    replyAndLeave('不知道你想要编码什么呢…');
}
if(strpos($text, "[CQ:") !== false) {
    replyAndLeave('不能包含非文本内容哦…');
}

replyAndLeave(base64_encode($text));
