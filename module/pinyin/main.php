<?php

global $Text, $Command;
use Overtrue\Pinyin\Pinyin;

$text = preg_replace('/\[CQ:.+?\]/', '', implode(' ', array_slice($Command, 1)).$Text);
if(mb_strlen($text, 'UTF-8') == 1) {
    replyAndLeave(implode(' / ', Pinyin::polyphones($text)[$text] ?? ['未查询到结果…']));
} else {
    replyAndLeave(Pinyin::fullSentence($text)->join(' '));
}
