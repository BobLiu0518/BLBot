<?php

global $CQ, $Queue, $Event;
requireLvl(1);
loadModule('credit.tools');
loadModule('nickname.tools');
if(!fromGroup()) {
    replyAndLeave("群内财富榜…等等，好像不在群里？");
}

$groupName = $CQ->getGroupInfo($Event['group_id'])->group_name;
$groupMemberList = $CQ->getGroupMemberList($Event['group_id']);
$creditData = array();
$reply = $groupName.'财富榜：';

foreach($groupMemberList as $groupMember) {
    if(($credit = getCredit($groupMember->user_id)) > 0) {
        $creditData[] = array($groupMember->user_id, $credit);
    }
}

usort($creditData, fn($a, $b) => $b[1] - $a[1]);
$lastUserCredit = null;
foreach(array_slice($creditData, 0, 5) as $n => $groupMember) {
    $rank = $lastUserCredit === $groupMember[1] ? $rank : $n + 1;
    $reply .= "\n#{$rank} {$groupMember[1]}金币 @".getNickName($groupMember[0], $Event['group_id']);
    $lastUserCredit = $totalCredit;
}

$Queue[] = replyMessage($reply);
