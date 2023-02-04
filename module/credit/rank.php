<?php

function sortCredit($a, $b){
	return $b[1] - $a[1];
}

function getName($user_id){
	global $CQ, $Event;
	return $CQ->getGroupMemberInfo($Event['group_id'], $user_id)->nickname;
}

global $CQ, $Queue, $Event;
requireLvl(1);
loadModule('credit.tools');
if(!fromGroup()){
	replyAndLeave("群内财富榜…等等，好像不在群里？");
}

$groupName = $CQ->getGroupInfo($Event['group_id'])->group_name;
$groupMemberList = $CQ->getGroupMemberList($Event['group_id']);
$creditData = array();
$reply = $groupName.'财富榜：';

foreach($groupMemberList as $groupMember){
	$creditData[] = array($groupMember->user_id, getCredit($groupMember->user_id));
}

usort($creditData, 'sortCredit');
foreach(array_slice($creditData, 0, 5) as $n => $groupMember){
	if($groupMember[1] > 0){
		$reply .= "\n#".($n + 1).' '.$groupMember[1].'金币 @'.getName($groupMember[0]);
	}
}

$Queue[]= replyMessage($reply);

?>
