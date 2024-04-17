<?php

global $Event, $CQ, $Queue;
requireLvl(5);

$memberList = $CQ->getGroupMemberList($Event['group_id']);
foreach($memberList as $member){
	$Queue[]= sendBack('[CQ:poke,qq='.$member->user_id.']');
}

?>
