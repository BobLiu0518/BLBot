<?php

global $Queue, $Text, $User_id, $Command;
requireLvl(1);
loadModule('credit.tools');

$text = trim(implode(' ', array_slice($Command, 1))."\n".$Text);
if(trim($text) === "")replyAndLeave("没有填写要生成的内容呢OvO");
if(strlen($text) > 271)replyAndLeave("消息太长啦！QwQ");
if(strpos($text, "[CQ:") !== false)replyAndLeave("二维码生成只支持纯文本哦");
$fee = intval(mb_strlen($text, 'utf-8'))*100;
decCredit($User_id, $fee);

$qr = sendImg((new SimpleSoftwareIO\QrCode\Generator)->encoding('UTF-8')->format('png')->size(1024)->margin(2)->style('round')->gradient(20, 125, 125, 75, 125, 175, 'vertical')->generate($text));

$Queue[]= sendBack($qr);
$Queue[]= replyMessage("共 ".mb_strlen($text, 'utf-8')." 字符，已收取 ".$fee." 金币，你的余额为 ".getCredit($User_id));

?>
