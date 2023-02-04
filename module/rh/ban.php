<?php

global $CQ, $Event;

if(!fromGroup())replyAndLeave("只能在群聊使用哦～");
if($CQ->getGroupMemberInfo($Event['group_id'], $Event['user_id'])->role == 'member')
	replyAndLeave("只支持群管理使用哦～");

setData('rh/'.$Event['group_id'], json_encode(array('status' => 'banned')));
replyAndLeave("已禁止本群赛马，如之后需要解除可以使用 #feedback 联系 Bot～");

?>
