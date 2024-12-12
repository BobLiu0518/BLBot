<?php

loadModule('hzk.tools');
requireLvl(1);

$str = UTF2GB(nextArg(true));
$length = mb_strlen($str, 'GB2312');
if($length > 15 && fromGroup()) {
    replyAndLeave('太长了，会刷屏的，请私聊使用哦…');
}
$temp = [[], [], [], [], [], [], [], [], [], [], [], []];
$reply = '';

$pixelMap = [
    [0x01, 0x08],
    [0x02, 0x10],
    [0x04, 0x20],
    [0x40, 0x80],
];
for($i = 0; $i < $length; $i++) {
    $char = mb_substr($str, $i, 1, 'GB2312');
    $result = getHZK12($char);
    for($j = 0; $j < 12; $j++) {
        $temp[$j] = array_merge($temp[$j], $result[$j]);
    }
    if($i % 3 == 2 || $i == $length - 1) {
        for($j = 0; $j < 12; $j += 4) {
            for($k = 0; $k < 36; $k += 2) {
                $sum = 0;
                for($y = 0; $y < 4; $y++) {
                    for($x = 0; $x < 2; $x++) {
                        $sum += $temp[$j + $y][$k + $x] * $pixelMap[$y][$x];
                    }
                }
                $reply .= mb_chr(0x2800 + $sum, 'UTF-8');
            }
            $reply .= "\n";
        }
        $a = 0x2800;
        $temp = [[], [], [], [], [], [], [], [], [], [], [], []];
        $reply .= "\n";
    }
}

replyAndLeave(trim($reply) ?: '生成失败…');
