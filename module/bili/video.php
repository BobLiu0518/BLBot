<?php

	global $Queue;
	$api = "http://api.bilibili.com/x/web-interface/view?aid=";
	if(($av = intval(nextArg())) === NULL)leave('请提供av号！');
	if(!($data = json_decode(file_get_contents($api.$av),ture)['data']))leave('查询失败！');

	$pic = $data['pic'];
	$title = $data['title'];
	$desc = $data['desc'];
	$owner = $data['owner']['name'];
	$view = $data['stat']['view'];
	$danmaku = $data['stat']['danmaku'];
	$favorite = $data['stat']['favorite'];
	$coin = $data['stat']['coin'];
	$like = $data['stat']['like'];
	$reply = $data['stat']['reply'];
	$videos = $data['videos'];

	$msg = <<<EOT
Bilibili 视频 av{$av} 的数据：

[CQ:image,file={$pic}]

{$title} by {$owner}，共{$videos}分p

{$desc}

{$view}观看/{$danmaku}弹幕/{$reply}评论/{$like}赞/{$coin}硬币/{$favorite}收藏
EOT;
	$Queue[]= sendBack($msg);

?>
