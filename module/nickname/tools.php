<?php

function getNickname($user_id, $group_id = null, $fallback = true) {
    global $CQ, $Event;
    $nickname = getData('nickname/'.$user_id);
    if(!$nickname && $fallback) {
        if(fromGroup() && $group_id) {
            $user = $CQ->getGroupMemberInfo($group_id ?? $Event['group_id'], $user_id, true);
            $nickname = $user->card ? $user->card : $user->nickname;
        } else {
            $nickname = $CQ->getStrangerInfo($user_id, true)->nickname;
        }
    }

    if (date('md') === '0401' && ($len = mb_strlen($nickname, 'UTF-8')) >= 2) {
        $times = $len > 4 ? 2 : 1;
        for ($i = 0; $i < $times; $i++) {
            $pos = mt_rand(0, $len - 1);
            $char = mb_substr($nickname, $pos, 1, 'UTF-8');
            $target = $pos === 0 ? 1 : ($pos === $len - 1 ? $pos - 1 : $pos + (mt_rand(0, 1) ?: -1));
            $nickname = mb_substr($nickname, 0, $target, 'UTF-8') . $char . mb_substr($nickname, $target + 1, null, 'UTF-8');
        }
    }

    return $nickname;
}
