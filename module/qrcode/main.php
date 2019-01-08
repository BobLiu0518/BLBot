<?php

global $Queue, $Text, $User_id;
loadModule('credit.tools');
while($nextArg = nextArg())
    $Text = $nextArg." ".$Text;
if(!$Text)leave("没有内容！");
if(strlen($Text) > 271)leave("消息过长！");
$fee = intval(mb_strlen($Text, 'utf-8')/10)+1;

decCredit($User_id, $fee);
$link = "http://tool.oschina.net/action/qrcode/generate?output=image%2Fjpeg&error=L&type=0&margin=4&size=4&data=";
$qr = file_get_contents($link.urlencode($Text));
if(!$qr){
    addCredit($User_id, $fee);
    leave("生成失败！");
}
$Queue[]= sendBack(sendImg($qr));
$Queue[]= sendBack("共 ".strlen($Text)." 个字节，已收取 ".$fee." 个金币，你的余额为 ".getCredit($User_id));

?>