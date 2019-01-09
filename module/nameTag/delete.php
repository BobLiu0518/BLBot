<?php

global $CQ, $Event, $Queue;

if(!fromGroup())
    leave("只能在群聊中使用！");
if($CQ->getGroupMemberInfo($Event['group_id'], config("bot","2094361499"))->role != "owner")
    leave("没有权限！");
else{
    $CQ->setGroupSpecialTitle($Event['group_id'], $Event['user_id']);
    $Queue[]= sendBack('[CQ:at,qq='.$Event['user_id'].'] 已删除群头衔！');
}

?>