<?php

if(!fromGroup())leave('请在群聊中使用！');

	global $Event, $CQ, $Queue;
	$t = rand(0, 10*60);
	if(!$t)
		$CQ->setGroupBan($Event['group_id'], $Event['user_id'], 2592000);
	else
		$CQ->setGroupBan($Event['group_id'], $Event['user_id'], $t);
	$Queue[]= sendBack('[CQ:at,qq='.$Event['user_id'].'] 已被禁言 '.intval($t / 60).'分钟'.($t % 60).'秒！');

	if(!$t){
		sleep(10*60);
		$CQ->setGroupBan($Event['group_id'], $Event['user_id'], 0);
	}

?>
