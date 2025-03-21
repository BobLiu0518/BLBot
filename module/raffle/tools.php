<?php

loadModule('nickname.tools');

function getRandGroupMember() {
    date_default_timezone_set('Asia/Shanghai');
    global $Event, $CQ;

    $time = time();
    $memberList = $CQ->getGroupMemberList($Event['group_id']);
    $pool = [];

    foreach($memberList as $member) {
        if($time - $CQ->getGroupMemberInfo($Event['group_id'], $member->user_id)->last_sent_time <= 86400 * 3) {
            $pool[] = $member;
        }
    }
    if(!count($pool)) {
        $pool = $memberList;
    }
    $member = $pool[rand(0, count($pool) - 1)];

    pokeBack($member->user_id);
    return [
        'nickname' => getNickname($member->user_id, null, false) ?: $member->card ?? $member->nickname,
        'user_id' => $member->user_id,
    ];
}
