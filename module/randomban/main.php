<?php

global $Event, $CQ, $Queue;
loadModule('randomBan.tools');

$role = $CQ->getGroupMemberInfo($Event['group_id'], $Event['user_id'], true)->role;
$bot = $CQ->getGroupMemberInfo($Event['group_id'], config('bot'), true)->role;
if($role == 'owner' || ($role == 'admin' && $bot == 'member')){
	replyAndLeave("身为".($role == 'owner'?"群主":"管理员")."就可以这样调戏我嘛？");
}else if($bot == 'member'){
	replyAndLeave("Bot 不是管理员呜呜呜");
}else if($bot == 'admin' && $role == 'admin'){
	replyAndLeave("您已被禁…等下，你也是管理？".(rand(0,5)?'':'那要不你就假装被禁言 '.intval($t / 60).'分'.($t % 60).'秒吧～'));
}

$t = randomBan();
$Queue[]= replyMessage('您已被禁言 '.intval($t / 60).'分'.($t % 60).'秒～');

?>
