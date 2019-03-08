<?php

global $Message, $User_id, $Queue, $CQ, $Event;

if(preg_match('/机器人/', $Message)||parseQQ($Message) == config('bot')){
    if(config('master') == $User_id || config('devgroup') == $Event['group_id'])leave();
    $message=$User_id." in Group ".$Event['group_id']." says ".$Message;
    $Queue[]= sendMaster($message);
    $Queue[]= sendDevGroup($message);
}
if(parseQQ($Message) == config('bot')){
    if($Event['user_id'] == "80000000")leave("请不要使用匿名！");
    $Queue[]= sendBack('亲亲，这边建议您不要艾特/回复呢，是没有用的哦，可以发送 '.config('prefix').'help 看看帮助哦，如果看不懂的话这边建议您换个脑子呢');
}

?>
