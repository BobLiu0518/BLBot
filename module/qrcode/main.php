<?php

global $Queue, $Text, $User_id;
loadModule('credit.tools');
while($nextArg = nextArg())
    $Text = $nextArg." ".$Text;
if(!$Text)leave("没有内容！");

decCredit($User_id, strlen($Text)/10+1);
$link = "http://tool.oschina.net/action/qrcode/generate?output=image/jpeg&error=L&type=0&margin=4&size=4&data=";
$qr = file_get_contents(urlencode($link.$Text));
$Queue[]= sendBack(sendImg($qr));
$Queue[]= sendBack("共 ".strlen($Text)." 个字节，已收取 ".(strlen($Text)/10+1)." 个金币，你的余额为 ".getCredit($User_id));

?>