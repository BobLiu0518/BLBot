<?php

	global $Queue;
	$api = "http://api.bilibili.com/x/relation/stat?vmid=";
	$api2 = "https://api.bilibili.com/x/space/acc/info?mid=";
	if(($uid = intval(nextArg())) === NULL)leave('请提供uid！');
	if(!($data = json_decode(file_get_contents($api.$uid),ture)['data']))leave('查询失败！');
	if(!($data2 = json_decode(file_get_contents($api2.$uid),ture)['data']))leave('查询失败！');

	$following = $data['following'];
	$black = $data['black'];
	$follower = $data['follower'];
	$name = $data2['name'];
	$sign = $data2['sign'];
	$face = $data2['face'];
	$level = $data2['level'];
	$coins = $data2['coins'];
	$official = $data2['official']['title'];
	if(!$official)$official = "暂未认证";
	else $official = "官方认证：".$official;

	$msg = <<<EOT
Bilibili 用户 uid{$uid} 的数据：
[CQ:image,file={$face}]
{$name}
{$sign}
{$official}

{$level}级/{$coins}硬币/{$following}关注/{$follower}粉丝
EOT;
	$Queue[]= sendBack($msg);

?>
