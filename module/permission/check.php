<?php

global $Event;

$QQ = nextArg() ?? $Event['user_id'];
if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
    $QQ = parseQQ($QQ);
}

$list = json_decode(getData('usertype.json'),true);
foreach($list as $type => $users)
	foreach($users as $user)
		if($user == $QQ)
			replyAndLeave($QQ.' 的权限为 '.$type.' ~');
replyAndLeave($QQ.' 的权限为 User ~');

?>
