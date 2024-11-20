<?php

global $Queue, $Event, $CQ;
use kjBot\SDK\CQCode;
loadModule('credit.tools');
loadModule('exp.tools');
loadModule('attack.tools');
loadModule('nickname.tools');

$QQ = nextArg() ?? $Event['user_id'];
if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)) {
	$QQ = parseQQ($QQ);
}

if($Event['user_id'] != $QQ && !isAdmin()) {
	if(!fromGroup()) {
		replyAndLeave('只能查询自己和群员的信息哦…');
	}
	$inGroup = false;
	foreach($CQ->getGroupMemberList($Event['group_id']) as $groupMember) {
		if($groupMember->user_id == $QQ) {
			$inGroup = true;
		}
	}
	if(!$inGroup) {
		replyAndLeave($QQ.' 不在本群哦…');
	}
}

$exp = getExp($QQ);
$level = getLvl($QQ);
$status = getStatus($QQ);
$statusEnd = getStatusEndTime($QQ);
$msg = "您的金币余额为 ".getCredit($QQ)."，经验值为 ".$exp."，等级为 Lv".$level." ～";
if($Event['user_id'] == $QQ) {
	switch($level) {
		case 2:
			$msg .= "\n再签到 ".(30 - $exp)." 天即可升级 Lv3～";
			break;
		case 1:
			$msg .= "\n再签到 ".(7 - $exp)." 天即可升级 Lv2～";
			break;
		case 0:
			$msg .= "\n签到后即可升级 Lv1 哦～";
			break;
	}
}
switch($status) {
	case 'imprisoned':
		$msg .= "\n当前身处监狱中，预计 ".$statusEnd." 出狱";
		break;
	case 'confined':
		$msg .= "\n当前身处监狱禁闭室中，预计 ".$statusEnd." 出狱";
		break;
	case 'arknights':
	case 'genshin':
		$msg .= "\n当前身处异世界";
		break;
	case 'universe':
		$msg .= "\n当前身处宇宙中";
		break;
	case 'hospitalized':
		$msg .= "\n当前身处医院中，预计 ".$statusEnd." 出院";
		break;
	case 'saucer':
		$characters = ['▖', '▗', '▘', '▝', '▚', '▞', '▀', '▄', '▌', '▐', '▙', '▛', '▜', '▟', '█'];
		$randomParts = '';
		for($i = 0; $i < 5; $i++) {
			$randomParts .= $characters[array_rand($characters)];
		}
		$msg .= "\n你被外星人".$randomParts."了。";
		break;
	case 'free':
		$lastCheckinTime = filemtime('../storage/data/checkin/'.$QQ);
		if(intval(date('Ymd')) - intval(date('Ymd', $lastCheckinTime)) > 0) {
			$msg .= "\n今天还没有签到哦～";
		}
	default: break;
}

if($Event['user_id'] != $QQ) {
	$msg = preg_replace('/您|你/', '@'.getNickname($QQ).' ', $msg);
}
$Queue[] = replyMessage($msg);