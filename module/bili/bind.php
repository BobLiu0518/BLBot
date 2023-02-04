<?php

global $Event;

if($uid = getData("bili/user/".$Event['user_id'])){
	if($uid == nextArg())
		replyAndLeave("您已经绑定过这个账号啦～");
	else
		replyAndLeave("您已绑定账号 uid".$uid."，如需解绑可以使用 #bili.unbind 哦");
}

$uid = ltrim(nextArg(), 'uidUID:');
if(!$uid)replyAndLeave("请填写你的uid哦～");
if(!is_numeric($uid)) replyAndLeave('uid不合法…请填写纯数字uid哦');
setData("bili/user/".$Event['user_id'], $uid);
replyAndLeave("绑定成功耶！");

?>
