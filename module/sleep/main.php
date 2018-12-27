<?php

global $Queue, $Event, $CQ;

if(!fromGroup())leave("只能在群聊中使用！");

date_default_timezone_set('Asia/Shanghai');

$time='';
while(true){
    $x=nextArg();
    if($x !== NULL){
        $time.=$x.' ';
    }else{
        break;
    }
}

try{
    $CQ->setGroupBan($Event['group_id'], $Event['user_id'], (strtotime($time)-time()));
}catch(\Exception $e){leave();}

?>