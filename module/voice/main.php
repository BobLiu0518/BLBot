<?php

global $Event, $Queue, $Text, $Command;
loadModule('credit.tools');

replyAndLeave('功能暂时下线…');

$Text = implode(' ', array_slice($Command, 1))."\n".$Text;

$Text = trim(removeCQCode(removeEmoji($Text)));
$strength = strlen($Text);

$fee = strlen(preg_replace('# #','',$Text))*100;
if(0 == $fee)replyAndLeave("想让我说什么呢？");

decCredit($Event['user_id'], $fee);
$Queue[]= sendBack("[CQ:tts,text=".$Text."]");
$Queue[]= replyMessage('共 '.$strength.' 字节，收取 '.$fee.' 金币，您的余额为 '.getCredit($Event['user_id']));

?>
