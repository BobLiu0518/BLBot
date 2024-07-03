<?php

global $Event, $Queue, $CQ;
requireLvl(1);
loadModule('jrrp.tools');

function randString(array $strArr){
	return $strArr[rand(0, sizeof($strArr)-1)];
}

$QQ = nextArg() ?? $Event['user_id'];
if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
    $QQ = parseQQ($QQ);
}

if($Event['user_id'] != $QQ){
	if(!fromGroup()){
		replyAndLeave('只能查询自己和群员的信息哦…');
	}
	$inGroup = false;
	foreach($CQ->getGroupMemberList($Event['group_id']) as $groupMember){
		if($groupMember->user_id == $QQ){
			$inGroup = true;
		}
	}
	if(!$inGroup){
		replyAndLeave($QQ.' 不在本群哦…');
	}
}
$jrrp = getRp($QQ, time());

if($Event['user_id'] == $QQ){
	if($jrrp >= 90) $reply = randString(['* 你充满了决心。', '你种了一整个花园的四叶草吗？你拥有了好运的精髓！', '精灵们今天非常开心！他们会尽可能给所有人带来好运的。']);
	else if($jrrp >= 70) $reply = randString(['你身上散发着好运的气息。', '幸运女神眷顾着你。', '精灵们今天的心情很不错。你今天要走运了。']);
	else if($jrrp >= 50) $reply = randString(['你释放出一种正能量，感觉美好的事情随时可能发生。', '你身上有种不寻常的温暖。', '精灵们今天没有任何感情倾向。因此今天的运势全掌握在你手中。']);
	else if($jrrp >= 20) $reply = randString(['你有什么心事吗？你有点不对劲。', '你所到之处空气都变得阴沉沉的。', '今天精灵们十分气恼。你要走霉运了。', '今天精灵们有些烦躁。你要走霉运了。']);
	else $reply = randString(['你印堂发黑。', '你究竟是干了什么才会遭此不幸呢…', '精灵今天十分不满。它们会竭尽全力给你捣蛋的。']);
	$Queue[]= replyMessage('你今天的人品是 '.$jrrp.'。'.$reply.(($jrrp < 50)?'（仅供娱乐，请勿当真）':''));
}else{
	$targetInfo = $CQ->getGroupMemberInfo($Event['group_id'], $QQ);
	if($jrrp <= 25) $reply = '看起来很适合打劫…（不是';
	else if($jrrp >= 90) $reply = 'Bot 先吸一口为敬（';
	else if($jrrp == 100) $reply = '总感觉神明都站在'.($targetInfo->sex == 'female' ? '她' : '他').'那边…？';
	else $reply = '';
	$Queue[]= replyMessage('@'.($targetInfo->card ? $targetInfo->card : $targetInfo->nickname)." 今天的人品是 ".$jrrp."。".$reply);
}

?>
