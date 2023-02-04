<?php

global $CQ, $Event, $Queue;
$nocheck = false;

requireLvl(1);

if(!fromGroup())
    replyAndLeave("只能在群聊中使用～");
if($CQ->getGroupMemberInfo($Event['group_id'], config("bot","2094361499"))->role != "owner")
    replyAndLeave("没有权限～");
else{
    if(!$tag = nextArg())replyAndLeave('请输入需要的头衔～');
    if($tag == "--nocheck"){$nocheck = true; requireLvl(4); $tag = nextArg();}
    if(mb_strlen($tag) > 6 && !$nocheck)replyAndLeave('头衔过于长～');
    $CQ->setGroupSpecialTitle($Event['group_id'], $Event['user_id'], $tag);
    $Queue[]= replyMessage('已设置群头衔 '.$tag.' ～');
}

?>
