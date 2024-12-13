<?php

loadModule('hzk.tools');
requireLvl(1);

$font = strtoupper(nextArg());
$fontList = ['12', '14', '16', '16F', '16S', '24F', '24H', '24K', '24S', '32'];
preg_match('/^(?:HZK)?(\d+[FHKS]?)$/', $font, $match);
if(!$match[1] || !in_array($match[1], $fontList)) {
    replyAndLeave("无法识别字体 {$font}…\n可选字体：HZK".implode("/", $fontList));
}

replyAndLeave(getBrailledChar(nextArg(true), 'HZK'.$match[1]) ?? '生成失败…');
