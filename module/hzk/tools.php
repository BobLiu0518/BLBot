<?php

function parseFont($font) {
    $fontList = ['12', '14', '16', '16F', '16S', '24F', '24H', '24K', '24S', '32'];
    preg_match('/^(?:HZK)?(\d+[FHKS]?)$/', strtoupper($font), $match);
    if(!$match[1] || !in_array($match[1], $fontList)) {
        replyAndLeave("无法识别字体 {$font}…\n可选字体：HZK".implode("/", $fontList));
    }
    return 'HZK'.$match[1];
}

function UTF2GB(string $str) {
    if(!$str) {
        replyAndLeave('不知道你想查看什么字呢…');
    }
    $str = mb_convert_kana($str, 'NA');
    $converted = iconv('UTF-8', 'GB2312', $str);
    if(!$converted) {
        $unsupported = [];
        $str = mb_str_split($str, 1, 'UTF-8');
        foreach($str as $n => $char) {
            $str[$n] = iconv('UTF-8', 'GB2312', $char);
            if(!$str[$n]) {
                $unsupported[] = $char;
            }
        }
        replyAndLeave('字符 '.implode('/', $unsupported).' 无法转换为 GB2312 编码哦…');
    }
    return $converted;
}

function getBrailledChar(string $font, string $str) {
    $font = parseFont($font);
    preg_match('/(\d+)/', $font, $match);
    $size = intval($match[1]);
    $lines = lineBreaker(UTF2GB($str), $size);
    foreach($lines as $line) {
        $reply .= getBraille(getHZK($line, $font, $size))."\n";
    }
    return trim($reply) ?: null;
}

function lineBreaker(string $str, int $size) {
    $charsPerLine = floor(36 / $size);
    if(ceil(mb_strlen($str, 'GB2312') / $charsPerLine) > 5 && fromGroup()) {
        replyAndLeave('太长了，会刷屏的，请私聊使用哦…');
    }
    $result = [''];
    $lines = 0;
    for($i = 0; $i < mb_strlen($str, 'GB2312'); $i++) {
        $char = mb_substr($str, $i, 1, 'GB2312');
        if(mb_strlen($result[$lines], 'GB2312') == $charsPerLine || $char == "\n") {
            $lines++;
            $result[$lines] = '';
        }
        if($char != "\n") {
            $result[$lines] .= $char;
        }
    }
    return $result;
}

function getHZK(string $str, string $font, int $size) {
    $font = fopen(getFontPath($font), 'rb');
    $result = [];

    for($i = 0; $i < mb_strlen($str, 'GB2312'); $i++) {
        $char = mb_substr($str, $i, 1, 'GB2312');
        $high = ord($char[0]);
        $low = ord($char[1]);
        $bytes = ceil($size / 8);
        $offset = (($high - 0xA1) * 0x5E + ($low - 0xA1)) * $bytes * $size;

        fseek($font, $offset);
        for($j = 0; $j < $size; $j++) {
            $bits = 0;
            for($k = 0; $k < $bytes; $k++) {
                $byte = fread($font, 1);
                $bits = ($bits << 8) | ord($byte);
            }
            for($k = 0; $k < $size; $k++) {
                $result[$j][$i * $size + $k] = ($bits >> ($bytes * 8 - $k - 1)) & 1;
            }
        }
    }
    fclose($font);
    return $result;
}

function getBraille(array $data) {
    $pixelMap = [
        [0x01, 0x08],
        [0x02, 0x10],
        [0x04, 0x20],
        [0x40, 0x80],
    ];
    for($i = 0; $i < count($data); $i += 4) {
        for($j = 0; $j < count($data[$i]); $j += 2) {
            $sum = 0;
            for($y = 0; $y < 4; $y++) {
                for($x = 0; $x < 2; $x++) {
                    if($j + $x >= count($data[$i])) {
                        break;
                    }
                    if($i + $y >= count($data)) {
                        break 2;
                    }
                    $sum += $data[$i + $y][$j + $x] * $pixelMap[$y][$x];
                }
            }
            $result .= mb_chr(0x2800 + $sum, 'UTF-8');
        }
        $result .= "\n";
    }
    return $result;
}
