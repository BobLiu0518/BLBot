<?php

global $Queue, $Text, $User_id;
loadModule('credit.tools');
while($nextArg = nextArg())
    $Text = $nextArg." ".$Text;
if($Text === "")replyAndLeave("没有填写要生成的内容呢OvO");
if(strlen($Text) > 271)replyAndLeave("消息太长啦！QwQ");
if(strpos($Text, "[CQ:") !== false)replyAndLeave("二维码生成只支持纯文本哦");
$fee = intval(mb_strlen($Text, 'utf-8'))*100;

decCredit($User_id, $fee);
$link = "http://tool.oschina.net/action/qrcode/generate?output=image%2Fjpeg&error=L&type=0&margin=15&size=4&data=";
$qr = file_get_contents($link.urlencode($Text));
if(!$qr){
    addCredit($User_id, $fee);
    replyAndLeave("生成失败呜呜呜");
}
$Queue[]= sendBack(sendImg($qr));
$Queue[]= replyMessage("共 ".strlen($Text)." 字节，已收取 ".$fee." 金币，你的余额为 ".getCredit($User_id));

?>
