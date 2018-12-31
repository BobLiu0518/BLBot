<?php

global $Message, $User_id, $Queue, $CQ, $Event;

if(preg_match('/机器人/', $Message)||parseQQ($Message) == config('bot')){
    if(config('master') == $User_id || config('devgroup') == $Event['group_id'])leave();
    $message=$User_id." in Group ".$Event['group_id']." says ".$Message;
    $Queue[]= sendMaster($message);
    $Queue[]= sendDevGroup($message);
}
if(parseQQ($Message) == config('bot')){
    $Queue[]= sendBack('艾特我没有卵用，请发送 '.config('prefix').'help 查看帮助');
}

?>