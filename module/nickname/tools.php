<?php

function getNickname($user_id, $group_id = null, $fallback = true) {
    global $CQ, $Event;
    $nickname = getData('nickname/'.$user_id);
    if(!$nickname && $fallback) {
        if(fromGroup() && $group_id) {
            $user = $CQ->getGroupMemberInfo($group_id ?? $Event['group_id'], $user_id, true);
            $nickname = $user->card ?? $user->nickname;
        } else {
            $nickname = $CQ->getStrangerInfo($user_id, true)->nickname;
        }
    }
    return $nickname;
}