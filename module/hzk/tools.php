<?php

function UTF2GB($str) {
    if(!$str) {
        replyAndLeave('不知道你想查看什么字呢…');
    }
    $str = iconv('UTF-8', 'GB2312', $str);
    if(!$str) {
        replyAndLeave('有字符无法转换为 GB2312 编码哦…');
    }
    return $str;
}

function getHZK12($char) {
    $font = fopen(getFontPath('HZK12'), 'rb');
    $high = ord($char[0]);
    $low = ord($char[1]);
    $offset = ($high - 0xA1) * 0x8D0 + ($low - 0xA1) * 0x18;
    $result = [];

    fseek($font, $offset);
    for($i = 0; $i < 12; $i++) {
        $bytes = fread($font, 2);
        $bytes = (ord($bytes[0]) << 8) | ord($bytes[1]);
        for($j = 0; $j < 12; $j++) {
            $result[$i][$j] = ($bytes >> (15 - $j)) & 1;
        }
    }
    fclose($font);
    return $result;
}