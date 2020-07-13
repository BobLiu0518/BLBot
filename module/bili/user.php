<?php

	requireLvl(1);

	global $Queue, $Event;
	$relationApi = "https://api.bilibili.com/x/relation/stat?vmid=";
	$liveApi = "https://api.live.bilibili.com/bili/living_v2/";
	$statApi = "https://api.bilibili.com/x/space/upstat?mid=";
	$videoApi = "https://space.bilibili.com/ajax/member/getSubmitVideos?pagesize=50&mid=";
	$spaceApi = "https://api.bilibili.com/x/space/acc/info?mid=";

	$uid = ltrim(ltrim(nextArg(), 'uid'), 'UID');
	if(parseQQ($uid))$uid = getData("bili/user/".parseQQ($uid));
	if(!$uid)$uid = getData("bili/user/".$Event['user_id']);
	if($uid == "")leave("请提供uid！如需绑定请使用 #bili.bind ！");
	else if(!is_numeric($uid))leave('uid不合法！');
	$relationData = json_decode(file_get_contents($relationApi.$uid), true)['data'];
	$liveData = json_decode(file_get_contents($liveApi.$uid), true)['data'];
	$statData = json_decode(file_get_contents($statApi.$uid), true)['data'];
	$spaceData = json_decode(file_get_contents($spaceApi.$uid), true)['data'];

	$following = $relationData['following'];
	$follower = $relationData['follower'];
	$name = $spaceData['name'];
	$sign = $spaceData['sign'];
	$face = $spaceData['face'];
	$level = $spaceData['level'];
	$official = $spaceData['official']['title'];
	$sex = $spaceData['sex'];
	$official = $official?"官方认证：".$official:"暂未进行个人认证";
	$archiveViews = $statData['archive']['view'];
	$articleViews = $statData['article']['view'];
	$sumSeconds = 0;
	$sumPlay = 0;
	$liveUrl = $liveData['url'];
	$videoList = array();

	$n = 1; //小破站起始页竟然是1不是0
	do{
		$videoData = json_decode(file_get_contents($videoApi.$uid.'&page='.$n), true)['data'];
		foreach($videoData['vlist'] as $video)
			$videoList[] = $video;
		$pages = $videoData['pages'];
		$n += 1;
	}while($n <= $pages);

	foreach($videoList as $video){
		$videoLength = explode(":", $video['length']);
		$sumSeconds += $videoLength[0] * 60 + $videoLength[1];
		$sumPlay += $video['play'];
	}

	$days = ($sex == "女"?"她":"他").'做UP主已经 '.intval((time() - $videoList[count($videoList)-1]['created'])/60/60/24).' 天了';
	$sumtime = $sumSeconds?"看完".($sex == "女"?"她":"他")."的全部视频需要 ".intval($sumSeconds / 86400)."天".intval($sumSeconds % 86400 / 3600).
		"小时".intval($sumSeconds % 3600 / 60)."分钟".($sumSeconds % 60)."秒":($sex == "女"?"她":"他")."没有发过视频或访问被拒绝";

	$msg = <<<EOT
Bilibili 用户 uid{$uid} 的数据：
https://space.bilibili.com/{$uid}
{$liveUrl}
[CQ:image,file={$face}]
{$name}
{$sign}
{$official}
{$sumtime}
{$days}

{$level}级/{$following}关注/{$follower}粉丝
{$archiveViews}播放/{$sumPlay}真实播放/{$articleViews}专栏阅读
EOT;
	$Queue[]= sendBack($msg);

?>
