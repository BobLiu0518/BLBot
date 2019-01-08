<?php

global $CQ, $Event, $Queue;

try{
    $CQ->setGroupSpecialTitle($Event['group_id'], $Event['user_id']);
}catch(\Exception $e){leave("没有权限");}
$Queue[]= sendBack('[CQ:at,qq='.$Event['user_id'].'] 已删除群头衔！');

?>