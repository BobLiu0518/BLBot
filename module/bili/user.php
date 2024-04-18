<?php

	requireLvl(2);

	global $Queue, $Event;
	$relationApi = "https://api.bilibili.com/x/relation/stat?vmid=";
	$liveApi = "https://api.live.bilibili.com/bili/living_v2/";
	$statApi = "https://api.bilibili.com/x/space/upstat?mid=";
	$videoApi = "https://api.bilibili.com/x/space/arc/search?ps=50&order=pubdate&mid=";
	$spaceApi = "https://api.bilibili.com/x/space/acc/info?mid=";

	ini_set('user_agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36');

	$uid = ltrim(nextArg()??'', 'uidUID:');
	if(parseQQ($uid))$uid = getData("bili/user/".parseQQ($uid));
	if(!$uid)$uid = getData("bili/user/".$Event['user_id']);
	if($uid == "")replyAndLeave("请提供uid哦～如果想查询自己，可以使用 #bili.bind <uid> 绑定自己的账号哦！(括号不填)");
	else if(!is_numeric($uid))replyAndLeave('uid不合法…请填写纯数字uid哦');

	$context = stream_context_create(['http' => ['header' => 'Cookie: SESSDATA='.getData('bili/api/sessdata')]]);
	$relationData = json_decode(file_get_contents($relationApi.$uid, false, $context), true)['data'];
	$liveData = json_decode(file_get_contents($liveApi.$uid, false, $context), true)['data'];
	$statData = json_decode(file_get_contents($statApi.$uid, false, $context), true)['data'];
	$spaceData = json_decode(file_get_contents($spaceApi.$uid, false, $context), true)['data'];

	$following = $relationData['following'];
	$follower = $relationData['follower'];
	$name = $spaceData['name'];
	$sign = $spaceData['sign'];
	$face = $spaceData['face'];
	$level = $spaceData['level'];
	$official = $spaceData['official']['title'];
	$sex = $spaceData['sex'];
	$official = $official?"\n官方认证：".$official:'';
	$archiveViews = $statData['archive']['view'];
	if(!$archiveViews) $archiveViews = '未知';
	$articleViews = $statData['article']['view'];
	if(!$articleViews) $articleViews = '未知';
	$sumSeconds = 0;
	$sumPlay = 0;
	$liveUrl = $liveData['url'];
	$videoList = array();

	$n = 1; //小破站起始页竟然是1不是0
	do{
		$videoData = json_decode(file_get_contents($videoApi.$uid.'&pn='.$n, false, $context), true)['data'];
		foreach($videoData['list']['vlist'] as $video)
			$videoList[] = $video;
		$sumvideos = $videoData['page']['count'];
		$n += 1;
	}while(($n-1)*50 <= $sumvideos);

	foreach($videoList as $video){
		$videoLength = explode(":", $video['length']);
		$sumSeconds += $videoLength[0] * 60 + $videoLength[1];
		$sumPlay += $video['play'];
	}

	$days = $videoList[count($videoList)-1]['created']?(($sex == "女"?"她":"他").'做UP主已经 '.intval((time() - $videoList[count($videoList)-1]['created'])/60/60/24 + 1)." 天了，"):'';
	$sumvideos = '一共发布了 '.count($videoList).' 个视频，';
	$sumtime = $sumSeconds?"看完".($sex == "女"?"她":"他")."的全部视频需要 ".intval($sumSeconds / 86400)."天".intval($sumSeconds % 86400 / 3600).
		"小时".intval($sumSeconds % 3600 / 60)."分钟".($sumSeconds % 60)."秒":($sex == "女"?"她":"他")."没有发过视频或访问被拒绝";

	$msg = <<<EOT
Bilibili 用户 uid{$uid} 的数据：
https://space.bilibili.com/{$uid}
{$liveUrl}
[CQ:image,file={$face}]
{$name}
{$sign}{$official}
{$days}{$sumvideos}{$sumtime}
{$level}级/{$following}关注/{$follower}粉丝
{$archiveViews}播放/{$sumPlay}真实播放/{$articleViews}专栏阅读
• “真实播放”为将所有稿件的播放量累加得出
EOT;
	if(count($videoList)){
		$randVideo = $videoList[array_rand($videoList, 1)];
		$msg .= "\n\n随机视频：\n".$randVideo['title']."\nhttps://b23.tv/av".$randVideo['aid'];
	}

	$Queue[]= replyMessage($msg);

?>
