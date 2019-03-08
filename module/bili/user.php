<?php

	global $Queue;
	$api = "http://api.bilibili.com/x/relation/stat?vmid=";
	if(($uid = intval(nextArg())) === NULL)leave('请提供uid！');
	if(!($data = json_decode(file_get_contents($api.$uid),ture)['data']))leave('查询失败！');

	$following = $data['following'];
	$black = $data['black'];
	$follower = $data['follower'];

	$msg = <<<EOT
Bilibili 用户 uid{$uid} 的数据：

关注了 {$following} 个用户；
拥有 {$follower} 个粉丝。
EOT;
	$Queue[]= sendBack($msg);

?>
