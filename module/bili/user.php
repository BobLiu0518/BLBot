<?php

	global $Queue, $Event;
	$tpApi = "https://api.kaaass.net/biliapi/user/space?id=";
	$liveApi = "http://api.live.bilibili.com/bili/living_v2/";
	$statApi = "https://api.bilibili.com/x/space/upstat?mid=";
	$videoApi = "http://space.bilibili.com/ajax/member/getSubmitVideos?pagesize=50&mid=";

	$uid = ltrim(ltrim(nextArg(), 'uid'), 'UID');
	if(parseQQ($uid))$uid = getData("bili/user/".parseQQ($uid));
	if(!$uid)$uid = getData("bili/user/".$Event['user_id']);
	if($uid == "")leave("请提供uid！如需绑定请使用 #bili.bind ！");
	else if(!is_numeric($uid))leave('uid不合法！');
//	if(!($data = json_decode(file_get_contents($tpApi.$uid), true)['data']))leave('查询失败！');
	$liveData = json_decode(file_get_contents($liveApi.$uid), true)['data'];
	$statData = json_decode(file_get_contents($statApi.$uid), true)['data'];

	$attention = $data['card']['attention'];
	$fans = $data['card']['fans'];
	$name = $data['card']['name'];
	$sign = $data['card']['sign'];
	$face = $data['card']['face'];
	$level = $data['card']['level_info']['current_level'];
	$official = $data['card']['official_verify']['title'];
	$sex = $data['card']['sex'];
	$official = $official?"官方认证：".$official:"暂未进行个人认证";
	$archiveViews = $statData['archive']['view'];
	$sumSeconds = 0;
	$liveUrl = $liveData['url'];

	$n = 1; //小破站起始页竟然是1不是0
	do{
		$videoData = json_decode(file_get_contents($videoApi.$uid.'&page='.$n), true)['data'];
		foreach($videoData['vlist'] as $video){
			$videoLength = explode(":", $video['length']);
			$sumSeconds += $videoLength[0] * 60 + $videoLength[1];
		}
		$pages = $videoData['pages'];
		$n += 1;
	}while($n <= $pages);

	$sumtime = $sumSeconds?"看完".($sex == "女"?"她":"他")."的全部视频需要".intval($sumSeconds / 86400)."天".intval($sumSeconds % 86400 / 3600).
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

{$level}级/{$attention}关注/{$fans}粉丝/{$archiveViews}播放
EOT;
	$Queue[]= sendBack($msg);

?>
