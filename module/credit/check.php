<?php

global $Queue, $Event, $CQ;
use kjBot\SDK\CQCode;
loadModule('credit.tools');
loadModule('exp.tools');
loadModule('attack.tools');

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

$exp = getExp($QQ);
$level = getLvl($QQ);
$status = getStatus($QQ);
$statusEnd = getStatusEndTime($QQ);
$msg = "您的金币余额为 ".getCredit($QQ)."，经验值为 ".$exp."，等级为 Lv".$level." ～";
if($Event['user_id'] == $QQ){
	switch($level) {
		case 2: $msg .= "\n再签到 ".(30-$exp)." 天即可升级 Lv3～"; break;
		case 1: $msg .= "\n再签到 ".(7-$exp)." 天即可升级 Lv2～"; break;
		case 0: $msg .= "\n签到后即可升级 Lv1 哦～"; break;
	}
}
switch($status) {
	case 'imprisoned': $msg .= "\n当前身处监狱中，预计 ".$statusEnd." 出狱"; break;
	case 'confined': $msg .= "\n当前身处监狱禁闭室中，预计 ".$statusEnd." 出狱"; break;
	case 'arknights':
	case 'genshin':
		$msg .= "\n当前身处异世界"; break;
	case 'hospitalized': $msg .= "\n当前身处医院中，预计 ".$statusEnd." 出院"; break;
	case 'free': default: break;
}

if($Event['user_id'] != $QQ){
	$msg = str_replace('您', '@'.($CQ->getGroupMemberInfo($Event['group_id'], $QQ)->card ? $CQ->getGroupMemberInfo($Event['group_id'], $QQ)->card : $CQ->getGroupMemberInfo($Event['group_id'], $QQ)->nickname).' ', $msg);
}
$Queue[]= replyMessage($msg);

?>
