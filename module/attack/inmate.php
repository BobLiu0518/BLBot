<?php

global $CQ, $Event;
requireLvl(1);
loadModule('attack.tools');
$memberList = $CQ->getGroupMemberList($Event['group_id']);
$groupName = $CQ->getGroupInfo($Event['group_id'])->group_name;
$inmates = [];
foreach($memberList as $member){
	$status = getStatus($member->user_id);
	if($status == 'imprisoned' || $status == 'confined'){
		$inmates[] = [
			'nickname' => ($member->card ? $member->card : $member->nickname),
			'end' => getStatusEndTime($member->user_id),
		];
	}
}
if(count($inmates)){
	usort($inmates, function($a, $b){
		if($a['end'] == '∞') return 1;
		else if($b['end'] == '∞') return -1;
		return $a['end'] - $b['end'];
	});
	$reply .= $groupName.' 狱友：';
	foreach($inmates as $inmate){
		$reply .= "\n@".$inmate['nickname']."\n　刑期至：".$inmate['end'];
	}
	replyAndLeave($reply);
}else{
	replyAndLeave('群监狱现在没有人哦…');
}

?>
