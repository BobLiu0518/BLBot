<?php

date_default_timezone_set('Asia/Shanghai');
global $Event, $Queue, $CQ;
loadModule('sun.tools');

if(!fromGroup()){
    replyAndLeave('?');
}

$member = getRandGroupMember();
$Queue[]= replyMessage('抽到了 '.'@'.$member['nickname'].' ('.$member['user_id'].')');
$CQ->groupPoke($Event['group_id'], $Event['user_id']);

?>
