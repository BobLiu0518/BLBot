<?php

loadModule('hzk.tools');
requireLvl(2);

$str = UTF2GB(nextArg(true));
$length = mb_strlen($str, 'GB2312');
if($length > 2 && fromGroup()) {
    replyAndLeave('太长了，会刷屏的，请私聊使用，或换用 #hzk.12s 哦…');
}
$reply = '';

for($i = 0; $i < $length; $i++) {
    $char = mb_substr($str, $i, 1, 'GB2312');
    $result = getHZK($char, 'HZK12', 12, false);
    for($j = 0; $j < 12; $j++) {
        for($k = 0; $k < 12; $k++) {
            $reply .= $result[$j][$k] ? '■' : '　';
        }
        $reply .= "\n";
    }
    $reply .= "\n\n";
}

replyAndLeave(trim($reply) ?: '生成失败…');
