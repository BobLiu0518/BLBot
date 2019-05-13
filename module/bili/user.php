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
	$sex = $data2['sex'];
	if(!$official)$official = "暂未认证";
	else $official = "官方认证：".$official;

	do{
		$n += 1;
		$api3 = "http://space.bilibili.com/ajax/member/getSubmitVideos?pagesize=100&tid=0&page=".$n."&keyword=&order=pubdate&mid=".$uid;
		$data = json_decode(file_get_contents($api3), true)['data'];
		$vlists[] = $data['vlist'];
	}while($data['pages'] > $n);

	foreach($vlists as $vlist)
		foreach($vlist as $video){
		$duration = explode(":", $video['length']);
		$minutes = intval($duration[0]);
		$seconds = intval($duration[1]);
		$sumseconds += 60*$minutes + $seconds;
	}

	$sumtime = "看完".($sex == "女"?"她":"他")."全部视频需要".intval($sumseconds / 60 / 60)."小时".intval(($sumseconds / 60) % 60)."分钟".($sumseconds % 60 % 60)."秒";

	$msg = <<<EOT
Bilibili 用户 uid{$uid} 的数据：
[CQ:image,file={$face}]
{$name}
{$sign}
{$official}
{$sumtime}

{$level}级/{$following}关注/{$follower}粉丝
EOT;
	$Queue[]= sendBack($msg);

?>
