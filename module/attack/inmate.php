<?php

global $CQ, $Event;
requireLvl(1);
loadModule('attack.tools');
$memberList = $CQ->getGroupMemberList($Event['group_id']);
$groupName = $CQ->getGroupInfo($Event['group_id'])->group_name;
$inmates = [];
foreach($memberList as $member){
	$status = getStatus($member->user_id);
	if($status == 'imprisoned' || $status == ''){
		$inmates[] = '@'.($member->card ? $member->card : $member->nickname)."\n　刑期至".getStatusEndTime($member->user_id);
	}
}
if(count($inmates)){
	replyAndLeave($groupName."狱友：\n".implode("\n", $inmates));
}else{
	replyAndLeave('群监狱现在没有人哦…');
}

?>
