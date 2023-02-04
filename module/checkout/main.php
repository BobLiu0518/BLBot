<?php

global $Event, $Queue, $User_id, $Message;
loadModule('credit.tools');

$fuck = rand(1, 9) * 1111;
//$fuck = getCredit($Event['user_id']); /* [CQ:code,id=13] */
decCredit($Event['user_id'], $fuck);
$reply = "签出成功，失去 ".$fuck." 个金币!";
if($Message)
        $reply = str_replace("签出", $Message, $reply);
$Queue[]= replyMessage($reply);

?>
