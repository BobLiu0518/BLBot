<?php

function getNickname($user_id, $group_id = null, $fallback = true) {
    global $CQ, $Event;
    $nickname = getData('nickname/'.$user_id);
    if(!$nickname && $fallback) {
        $nickname = '群友';
    }
    return $nickname;
}