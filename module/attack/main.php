<?php

global $Event, $Queue, $CQ;

requireLvl(1);
loadModule('attack.tools');

$from = $Event['user_id'];
$target = nextArg();
if(!(preg_match('/\d+/', $target, $match) && $match[0] == $target)){
        $target = parseQQ($target);
}
$target = intval($target);
if($target == config('bot')){
        replyAndLeave('你竟然想抢劫 Bot？！');
}else if($target === 0){
        replyAndLeave("要抢劫谁呢？\n(注：复制含有“@”的消息，@ 会失效。可以手动重新 @ 或者直接输入 QQ 号。)");
}
$groupMemberList = $CQ->getGroupMemberList($Event['group_id']);
$targetInGroup = false;
foreach($groupMemberList as $groupMember){
        if($groupMember->user_id == $target){
                $targetInGroup = true;
        }
}
if(!$targetInGroup){
        replyAndLeave("你并不知道要去哪里打劫 {$target}。\n(打劫目标不在本群内)");
}

$targetInfo = $CQ->getGroupMemberInfo($Event['group_id'], $target, true);
$atTarget = '@'.($targetInfo->card ? $targetInfo->card : $targetInfo->nickname);
if(time() - $targetInfo->last_sent_time >= 3 * 86400){
        replyAndLeave("你并不知道要去哪里打劫 {$atTarget}。\n(打劫目标连续三天未在群内出现)");
}

if(!fromGroup() || $target == $from){
        $money = getCredit($from);
        replyAndLeave("你把自己洗劫一空。\n(金币 - $money, 金币 + $money)");
}else if(!$target){
        replyAndLeave('要抢劫谁呢？');
}

$message = attack($from, $target, $atTarget);
$Queue[]= replyMessage($message);
$Queue[]= sendBack('[CQ:poke,qq='.$target.']');

?>
