<?php

global $CQ, $Event, $Queue;
$nocheck = false;

if(!fromGroup())
    leave("只能在群聊中使用！");
if($CQ->getGroupMemberInfo($Event['group_id'], config("bot","2094361499"))->role != "owner")
    leave("没有权限！");
else{
    if(!$tag = nextArg())leave('请输入需要的头衔！');
    if($tag == "--nocheck"){$nocheck = true; $tag = nextArg();}
    if(mb_strlen($tag) > 6 && !$nocheck)leave('头衔过于长！');
    $CQ->setGroupSpecialTitle($Event['group_id'], $Event['user_id'], $tag);
    $Queue[]= sendBack('[CQ:at,qq='.$Event['user_id'].'] 已设置群头衔 '.$tag.' ！');
}

?>
