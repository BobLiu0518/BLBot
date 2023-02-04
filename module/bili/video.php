<?php

	requireLvl(1);

	global $Queue;
	$api = "http://api.bilibili.com/x/web-interface/view?";
	$vid = nextArg();
	if(strpos($vid, 'BV') === 0) $bvid = $vid;
	else if(is_numeric($vid = ltrim(ltrim($vid, 'av'), 'AV'))) $avid = $vid;
	else replyAndLeave('请输入av号或BV号哦');
	if(!($data = json_decode(file_get_contents($api.'aid='.$avid.'&bvid='.$bvid),ture)['data']))replyAndLeave('查询失败…');

	$avid = $data['aid'];
	$bvid = $data['bvid'];
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

	if(count($data['staff']) > 1){
		$owner = '多位Staff';
		$staff = '创作团队：';
		foreach($data['staff'] as $person){
			$staff .= $person['title'].'：'.$person['name'].'；';
		}
		$staff = rtrim($staff, '；')."。\n\n";
	}

	$msg = <<<EOT
Bilibili 视频 av{$avid} / {$bvid} 的数据：
https://b23.tv/{$bvid}

[CQ:image,file={$pic}]

{$title} by {$owner}，共{$videos}P

{$staff}{$desc}

{$view}观看/{$danmaku}弹幕/{$reply}评论/{$like}赞/{$coin}硬币/{$favorite}收藏
EOT;
	$Queue[]= replyMessage($msg);

?>
