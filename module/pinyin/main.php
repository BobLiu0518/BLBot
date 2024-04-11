<?php

global $Text, $Command;
requireLvl(1);
use Overtrue\Pinyin\Pinyin;

$text = preg_replace('/\[CQ:.+?\]/', '', implode(' ', array_slice($Command, 1)).$Text);
if(mb_strlen($text, 'UTF-8') == 1){
	replyAndLeave(implode(' / ', Pinyin::polyphones($text)[$text]));
}else{
	replyAndLeave(Pinyin::fullSentence($text)->join(' '));
}

?>
