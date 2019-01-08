<?php

global $CQ, $Event, $Queue;

if(!$tag = nextArg())leave('请输入需要的头衔！');
if(mb_strlen($tag) > 6)leave('头衔过于长！');
try{
    $CQ->setGroupSpecialTitle($Event['group_id'], $Event['user_id'], $tag);
}catch(\Exception $e){leave("没有权限");}
$Queue[]= sendBack('[CQ:at,qq='.$Event['user_id'].'] 已设置群头衔 '.$tag.' ！');

?>