<?php

requireLvl(1);
global $Event;

if($Event['group_id'] != config('devgroup')) {
    replyAndLeave('请在 Bot 开发群内使用本指令哦');
}
$Queue[] = sendMaster("{$Event['user_id']} 申请加群");
$Queue[] = replyMessage('已提交申请，请耐心等候回复哦');