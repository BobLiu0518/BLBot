<?php

global $Event, $Queue, $CQ;

requireLvl(1);
loadModule('attack.tools');
loadModule('sun.tools');
if(!fromGroup()) replyAndLeave('?');

$from = $Event['user_id'];
$target = getRandGroupMember();
$atTarget = '@'.$target['nickname'].' ('.$target['user_id'].')';

$targetInfo = $CQ->getGroupMemberInfo($Event['group_id'], $target['user_id'], true);
if(time() - $targetInfo->last_sent_time >= 3 * 86400){
        replyAndLeave("你并不知道要去哪里打劫 {$atTarget}。\n(打劫目标连续三天未在群内出现)");
}

if($target['user_id'] == $from){
        $money = getCredit($from);
        replyAndLeave("你把自己洗劫一空。\n(金币 - $money, 金币 + $money)");
}else if(!$target['user_id']){
	replyAndLeave('没有随机到抢劫目标…');
}

$message = attack($from, $target['user_id'], $atTarget);
$Queue[]= replyMessage($message);

?>
